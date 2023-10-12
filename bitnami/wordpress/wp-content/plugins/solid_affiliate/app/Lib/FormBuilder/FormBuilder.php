<?php

namespace SolidAffiliate\Lib\FormBuilder;

use SolidAffiliate\Lib\Integrations\WooCommerceIntegration;
use SolidAffiliate\Lib\RandomData;
use SolidAffiliate\Lib\SchemaFunctions;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Lib\Utils;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Lib\VO\FormFieldArgs;
use SolidAffiliate\Lib\VO\Schema;
use SolidAffiliate\Lib\VO\SchemaEntry;
use SolidAffiliate\Models\Affiliate;

/**
 * @psalm-import-type ShowOnFormType from SchemaEntry
 * @psalm-import-type FormFieldArgsType from FormFieldArgs
 *
 * @psalm-type FormContext = 'new'|'edit'|'preview'|'non_admin_new'|'non_admin_edit'
 */
class FormBuilder
{
    const BEFORE_RECAPTCHA_ACTION = 'solid_affiliate/affiliate_registration_form/before_recaptcha';

    # TODO: Make values constants and can use a type _*
    /** @var array<FormContext, string> */
    const SHOW_ON_KEY_MAPPINGS = [
        'new' => 'show_on_new_form',
        'non_admin_new' => 'show_on_non_admin_new_form',
        'edit' => 'show_on_edit_form',
        'non_admin_edit' => 'show_on_non_admin_edit_form',
        'preview' => 'show_on_preview_form'
    ];

    const DEFAULT_TOOLTIP_ICON = '
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="none">
    <path d="M22.5 40H27.5V35H22.5V40ZM25 0C11.2 0 0 11.2 0 25C0 38.8 11.2 50 25 50C38.8 50 50 38.8 50 25C50 11.2 38.8 0 25 0ZM25 45C13.975 45 5 36.025 5 25C5 13.975 13.975 5 25 5C36.025 5 45 13.975 45 25C45 36.025 36.025 45 25 45ZM25 10C19.475 10 15 14.475 15 20H20C20 17.25 22.25 15 25 15C27.75 15 30 17.25 30 20C30 25 22.5 24.375 22.5 32.5H27.5C27.5 26.875 35 26.25 35 20C35 14.475 30.525 10 25 10Z" fill="rgb(34, 113, 177)"></path>
    </svg>';

    /**
     * Builds out all the fields for a form as table rows and cells, using each field's SchemEntry.
     * To render the form as a table, the FormBuilder output must be wrapped in the <table> and <tbody> elements.
     *
     * @param Schema      $schema
     * @param FormContext $form_context
     * @param object|null $item
     * @param bool        $display_inline
     * @param bool        $hide_titles
     * @param bool        $hide_descriptions
     * @param string      $display
     *
     * @return string
     */
    public static function build_form(
        $schema,
        $form_context,
        $item = null,
        $display_inline = false,
        $hide_titles = false,
        $hide_descriptions = false,
        $display = 'inline-block'
    )
    {
        $keys = SchemaFunctions::keys($schema);

        $fields = array_map(function ($field_name) use ($schema, $form_context, $item, $hide_titles, $hide_descriptions) {
            $schema_entry = $schema->entries[$field_name];
            $show_on_form_key = self::SHOW_ON_KEY_MAPPINGS[$form_context];

            if (isset($schema_entry->$show_on_form_key) && $schema_entry->$show_on_form_key) {
                /** @var ShowOnFormType */
                $show_on_form_value = (string)$schema_entry->$show_on_form_key;

                switch ($show_on_form_value) {
                    case 'hidden':
                        return self::build_field($schema, $field_name, $hide_titles, $hide_descriptions, $item, false, true);
                    case 'disabled':
                        return self::build_field($schema, $field_name, $hide_titles, $hide_descriptions, $item, true, false);
                    case 'hidden_and_disabled':
                        return self::build_hidden_and_disabled_fields($schema, $field_name, $hide_titles, $hide_descriptions, $item);
                    default:
                        return self::build_field($schema, $field_name, $hide_titles, $hide_descriptions, $item);
                }
            } else {
                return "";
            }
        }, $keys);

        $random_id = 'sld-form' . RandomData::string();

        $css = "
        <style>
            p.sld_field-description { display: block; opacity: 0.75; font-size:12px; margin:0 }
        </style>
        ";

        $css = $display_inline ? $css . "<style>
            table.form-table th { display: none; }
            table.form-table tr { display: inline-block; }
            table.form-table td { display: inline-block; }
        </style> " : $css;

        $css = $css . "
        <style>
        .sld_field-title.required>span.sld_title-text:after {
            color: red;
            content: ' * ';
        }

        </style>
        ";

        return $css . "<div style='display: {$display};' id='{$random_id}'>" . implode(" ", $fields) . "</div>";
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // Build field functions for visible, hidden, disabled fields
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Builds a form field based on the SchemaEntry.
     *
     * @param Schema $schema
     * @param string $field_name
     * @param bool $hide_titles
     * @param bool $hide_descriptions
     * @param object|null $item
     * @param bool $disabled
     * @param bool $hidden
     *
     *
     * @return string
     */
    public static function build_field(
        $schema,
        $field_name,
        $hide_titles,
        $hide_descriptions,
        $item = null,
        $disabled = false,
        $hidden = false
    )
    {
        if ($item && property_exists($item, $field_name)) {
            /** @var string|int $value */
            $value = $item->$field_name;
        } else {
            /** @var string|int $default */
            $default = $schema->entries[$field_name]->user_default;
            $value = $default;
        }

        // If it's hidden, bail out here and create a hidden field
        // No need to wrap it since it is not visible
        if ($hidden) {
            return self::build_hidden_field($field_name, $value);
        }

        $args = self::get_field_args_from_schema($schema, $field_name, $hide_titles, $hide_descriptions, $value);

        switch ($args->field_type) {
            case 'wp_editor':
                $field = WpEditorField::build_wp_editor_field($args, $disabled);
                break;
            case 'wp_dropdown_pages':
                $field = WpDropDownPages::build_wp_dropdown_pages($args, $disabled);
                break;
            case 'number':
                $field = NumberField::build_number_field($args, $disabled);
                break;
            case 'affiliate_select':
                $field = self::build_affiliate_select_field($args, $disabled);
                break;
            case 'user_select':
                $field = self::build_user_select_field($args, $disabled);
                break;
            case 'woocommerce_product_select':
                $field = self::build_woocommerce_product_select_field($args, $disabled);
                break;
            case 'woocommerce_coupon_select':
                $field = self::build_woocommerce_coupon_select_field($args, $disabled);
                break;
            case 'select':
                $field = SelectField::build_select_field($args, $disabled);
                break;
            case 'checkbox':
                $field = CheckBoxField::build_checkbox_field($args, $disabled);
                break;
            case 'multi_checkbox':
                $field = self::build_multi_checkbox_field($args, $disabled);
                break;
            case 'radio_buttons':
                $field = self::build_radio_buttons_field($args, $disabled);
                break;
            case 'text':
                $field = TextField::build_text_field($args, $disabled);
                break;
            case 'textarea':
                $field = TextAreaField::build_textarea_field($args, $disabled);
                break;
            default:
                $field = TextField::build_text_field($args, $disabled);
        }

        $field = self::_maybe_wrap_field_in_div($field, $args);

        return $field;
    }

    /**
     * Builds a disbaled and hidden form field for a SchemaEntry.
     *
     * @param Schema $schema
     * @param string $field_name
     * @param boolean $hide_titles
     * @param boolean $hide_descriptions
     * @param object|null $item
     *
     * @return string
     */
    public static function build_hidden_and_disabled_fields($schema, $field_name, $hide_titles, $hide_descriptions, $item = null)
    {
        $hidden_field = self::build_field($schema, $field_name, $hide_titles, $hide_descriptions, $item, false, true);
        $disabled_field = self::build_field($schema, $field_name, $hide_titles, $hide_descriptions, $item, true, false);
        return $disabled_field . " " . $hidden_field;
    }

    /**
     * Returns an HTML Hidden Input.
     *
     * @param string $field_name
     * @param mixed $value
     *
     * @return string
     */
    public static function build_hidden_field($field_name, $value)
    {
        $html_escaped_value = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');

        return  "<input type='hidden' id='{$field_name}' name='{$field_name}' value='{$html_escaped_value}'>";
    }

    /**
     * Returns an HTML Hidden Input for an arbitrarily nested associative array.
     * It handles the recursive nature of the array.
     *
     * @param string $field_name
     * @param array $value
     *
     * @return string
     */
    public static function build_hidden_fields_for_associative_array($field_name, $value)
    {
        $fields = [];

        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($value as $key => $val) {
            if (is_array($val)) {
                $fields[] = self::build_hidden_fields_for_associative_array("{$field_name}[{$key}]", $val);
            } else {
                $val = esc_attr((string)$val);
                $fields[] = "<input type='hidden' id='{$field_name}[{$key}]' name='{$field_name}[{$key}]' value='{$val}'>";
            }
        }
        return implode(" ", $fields);
    }


    /////////////////////////////////////////////////////////////////////////////////////////
    // Builders for specific form fields
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Undocumented function
     *
     * @param FormFieldArgs $args
     * @param bool $disabled
     *
     * @return string
     */
    # TODO: Does not seem to be using the passed in disabled
    # TODO:3: Creates multiple inputs with the same ID
    public static function build_multi_checkbox_field($args, $disabled = false)
    {
        // NOTE: to simplify making the type signature of $value in this function comply with the type signature of
        // the function FormBuilder::get_field_args_from_schema, I left the type sig of value mixed, when in reality it exects array<mixed>.
        $value = is_array($args->value) ? $args->value : (array)$args->value;

        $field_name = $args->field_name;
        $field_id = $args->field_id;
        # TODO:2: This does not actually make an checkboxes required because the old way was requiring all checkboxes to be checked
        $_required = self::set_required_string($args->required);
        $label_class = $args->label_class;

        # TODO: How to render the per checkbox label?
        $checkboxes = array_map(
            /**
             * @param list<string> $tuple
             */
            function ($tuple) use ($value, $field_name, $field_id, $_required, $label_class) {
                $current_checkbox_value = $tuple[0];

                $disabled = false;

                // If the current checkbox is in the value array then it should be checked
                $checked = (in_array($current_checkbox_value, $value)) ?  'checked' : '';
                // return "<option value='{$tuple[0]}' {$is_selected}>{$tuple[1]}</option>";
                $value = esc_attr((string)$tuple[0]);
                # TODO: The the labels are not clickable because they all have the same name=field_name, so all label click the first checkbox
                return "<input {$disabled} type='checkbox' name='{$field_name}[]' id='{$field_id}' class='regular-text' value='{$value}' {$checked} />
                        <label class='{$label_class}'>{$tuple[1]}</label>
                        <br/>";
            },
            Validators::arr($args->select_options)
        );

        $checkboxes_imploded = implode(" ", $checkboxes);

        # TODO: Do we wrap in the top level label?
        return "
    <tr class='row-{$field_name}'>
        " . self::maybe_render_field_title($args) . "
        <td>
            <label for='{$args->label_for_value}' class='{$args->label_class}'>
                {$checkboxes_imploded}
                " . self::maybe_render_field_description($args) . "
            </label>
        </td>
    </tr>
    ";
    }

    /**
     * Undocumented function
     *
     * @param FormFieldArgs $args
     * @param bool $disabled
     *
     * @return string
     */
    # TODO: Test somewhere because I don't think it is being used
    # TODO: does not seem to be using the passed in disabled
    public static function build_radio_buttons_field($args, $disabled = false)
    {
        // NOTE: to simplify making the type signature of $value in this function comply with the type signature of
        // the function FormBuilder::get_field_args_from_schema, I left the type sig of value mixed, when in reality it exects array<mixed>.
        $value = is_array($args->value) ? $args->value : (array)$args->value;


        $field_name = $args->field_name;
        $label_class = $args->label_class;
        $required = self::set_required_string($args->required);

        # TODO: How to render the per checkbox label?
        $checkboxes = array_map(
            /**
             * @param list<string> $tuple
             */
            function ($tuple) use ($value, $field_name, $required, $label_class) {
                $current_checkbox_value = $tuple[0];
                $label = $tuple[1];

                $disabled = isset($tuple[2]) ? $tuple[2] : '';

                // If the current checkbox is in the value array then it should be checked
                $checked = (in_array($current_checkbox_value, $value)) ?  'checked' : '';
                // return "<option value='{$tuple[0]}' {$is_selected}>{$tuple[1]}</option>";

                $value = esc_attr((string)$current_checkbox_value);
                return "<input {$disabled} type='radio' name='{$field_name}' id='{$current_checkbox_value}' class='regular-text' value='{$value}' {$required} {$checked} />
                            <label for='{$current_checkbox_value}' class='{$label_class}'>{$label}</label>
                        <br/>";
            },
            Validators::arr($args->select_options)
        );

        $checkboxes_imploded = implode(" ", $checkboxes);

        # TODO: Do we wrap in the top level label?
        return "
    <tr class='row-{$field_name}'>
		" . self::maybe_render_field_title($args) . "
        <td>
            <label for='{$args->field_name}' class='{$args->label_class}'>
                {$checkboxes_imploded}
                " . self::maybe_render_field_description($args) . "
            </label>
        </td>
    </tr>
    ";
    }

    /**
     * For Affiliate Search
     *
     * @param FormFieldArgs $args
     * @param bool $disabled
     *
     * @return string
     */
    public static function build_affiliate_select_field($args, $disabled = false)
    {
        $disabled = self::set_disabled_string($disabled);
        $values = array_column($args->select_options, 0);
        /** @var string|int|float|bool $selected_value */
        $selected_value = $args->value !== null ? $args->value : reset($values);

        $maybe_affiliate = Affiliate::find((int)$selected_value);

        if ($maybe_affiliate instanceof Affiliate) {
            $email = Affiliate::account_email_for($maybe_affiliate);
            $selects = ["<option value='{$maybe_affiliate->id}' selected='selected'>#{$maybe_affiliate->id} - {$email}</option>"];
        } else {
            $selects = [];
        }

        $selects_imploded = implode(' ', $selects);

        $required = self::set_required_string($args->required);

        return self::build_select_search_field(
            $args,
            $disabled,
            $required,
            $selects_imploded,
            "solid-affiliate-affiliate-search-select"
        );
    }

    /**
     * For User Search
     *
     * @param FormFieldArgs $args
     * @param bool $disabled
     *
     * @return string
     */
    public static function build_user_select_field($args, $disabled = false)
    {
        $disabled = ($disabled === true) ? 'disabled' : '';
        $values = array_column($args->select_options, 0);
        /** @var string|int|float|bool $selected_value */
        $selected_value = $args->value !== null ? $args->value : reset($values);

        $maybe_user = get_user_by('id', (int)$selected_value);

        if ($maybe_user instanceof \WP_User) {
            $email = $maybe_user->user_email;
            $selects = ["<option value='{$maybe_user->ID}' selected='selected'>#{$maybe_user->ID} - {$email}</option>"];
        } else {
            $selects = [];
        }

        $selects_imploded = implode(' ', $selects);

        $required = self::set_required_string($args->required);

        return self::build_select_search_field(
            $args,
            $disabled,
            $required,
            $selects_imploded,
            "solid-affiliate-user-search-select"
        );
    }

    /**
     * Returns an WooCommerce Coupon Select Form Input
     *
     * @param FormFieldArgs $args
     * @param bool $disabled
     *
     * @return string
     */
    public static function build_woocommerce_coupon_select_field($args, $disabled = false)
    {
        $disabled = ($disabled === true) ? 'disabled' : '';
        $values = array_column($args->select_options, 0);
        /** @var string|int|float|bool $selected_value */
        $selected_value = $args->value !== null ? $args->value : reset($values);

        if (WooCommerceIntegration::is_a_valid_coupon_id((int)$selected_value)) {
            $woocommerce_coupon = new \WC_Coupon((int)$selected_value);
            $id = $woocommerce_coupon->get_id();

            $selects = [
                "<option value='{$id}' selected='selected'>
                    {$woocommerce_coupon->get_code()} - ({$id})
                </option>"
            ];
        } else {
            $selects = [];
        }

        $selects_imploded = implode(' ', $selects);

        $required = self::set_required_string($args->required);

        return self::build_select_search_field(
            $args,
            $disabled,
            $required,
            $selects_imploded,
            'solid-affiliate-woocommerce-coupon-search-select'
        );
    }

    /** 
     * For WooCommerce Product Search
     *
     * @param FormFieldArgs $args
     * @param bool $disabled
     *
     * @return string
     */
    public static function build_woocommerce_product_select_field($args, $disabled = false)
    {
        $disabled = ($disabled === true) ? 'disabled' : '';
        $values = array_column($args->select_options, 0);
        /** @var string|int|float|bool $selected_value */
        $selected_value = $args->value !== null ? $args->value : reset($values);

        // $maybe_user = get_user_by('id', (int)$selected_value);
        $maybe_woocommerce_product = wc_get_product((int)$selected_value);

        if ($maybe_woocommerce_product instanceof \WC_Product) {
            $selects = ["<option value='{$maybe_woocommerce_product->get_id()}' selected='selected'>#{$maybe_woocommerce_product->get_id()} - {$maybe_woocommerce_product->get_name()}</option>"];
        } else {
            $selects = [];
        }

        // if ($maybe_user instanceof \WP_User) {
        //     $email = $maybe_user->user_email;
        //     $selects = ["<option value='{$maybe_user->ID}' selected='selected'>#{$maybe_user->ID} - {$email}</option>"];
        // } else {
        //     $selects = [];
        // }

        $selects_imploded = implode(' ', $selects);

        $required = self::set_required_string($args->required);

        return self::build_select_search_field(
            $args,
            $disabled,
            $required,
            $selects_imploded,
            'solid-affiliate-woocommerce-product-search-select'
        );
    }

    /**
     * Returns a select search field form input.
     *
     * @param FormFieldArgs $args
     * @param bool|string $disabled
     * @param string $required
     * @param string $selects_imploded
     * @param string $select_class
     *
     * @return string
     */
    private static function build_select_search_field($args, $disabled, $required, $selects_imploded, $select_class)
    {
        return "
    <tr class='row-{$args->field_name}'>
        " . self::maybe_render_field_title($args) . "
        <td>
            <label for='{$args->label_for_value}' class='{$args->label_class}'>
                <select
                    {$disabled}
                    name='{$args->field_name}'
                    id='{$args->field_id}'
                    class='{$select_class}'
                    $required
                >
                        {$selects_imploded}
                </select>
            </label>
            " . self::maybe_render_field_description($args) . "
        </td>
    </tr>
        ";
    }

    /**
     * Returns the HTML for a date picker.
     *
     * @return string
     */
    public static function render_solid_date_picker()
    {
        $o = '
        <select name="date_range_preset" id="date_range_preset" required="">
            <option value="today">' . __('Today', 'solid-affiliate') . '</option> <option value="yesterday">' . __('Yesterday', 'solid-affiliate') . '</option> <option value="this_week">' . __('This Week', 'solid-affiliate') . '</option> <option value="last_week">' . __('Last Week', 'solid-affiliate') . '</option> <option value="this_month" selected="selected">' . __('This Month', 'solid-affiliate') . '</option> <option value="last_month">' . __('Last Month', 'solid-affiliate') . '</option> <option value="this_quarter">' . __('This Quarter', 'solid-affiliate') . '</option> <option value="last_quarter">' . __('Last Quarter', 'solid-affiliate') . '</option> <option value="this_year">' . __('This Year', 'solid-affiliate') . '</option> <option value="last_year">' . __('Last Year', 'solid-affiliate') . '</option> <option value="all_time">' . __('All Time', 'solid-affiliate') . '</option> <option value="custom">' . __('Custom', 'solid-affiliate') . '</option>
        </select>
        ';

        $current_month_first_day = date('Y-m-01');
        $o .= '<label for="start_date"><p>' . __('Start Date', 'solid-affiliate') . '</p></label>';
        $o .= '<input type="date" name="start_date" id="start_date" class="regular-text" placeholder="Start Date" value="' . $current_month_first_day . '">';

        $current_month_last_day = date('Y-m-t');
        $o .= '<label for="end_date"><p>' . __('End Date', 'solid-affiliate') . '</p></label>';
        $o .= '<input type="date" name="end_date" id="end_date" class="regular-text" placeholder="End Date" value="' . $current_month_last_day . '">';

        return $o;
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // CRUD Form Builders
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param Schema $schema
     * @param string $submit_action
     * @param string $nonce
     * @param string|int $form_id
     * @param string $singular_resource_name
     * @param object|null $item
     * @param array{0: string, 1: string}|null $hidden_field_tuple
     * 
     * @return string
     */
    public static function render_crud_form_new($schema, $submit_action, $nonce, $form_id, $singular_resource_name, $item = null, $hidden_field_tuple = null)
    {
        $button_text = sprintf(__('Add New %1$s', 'solid-affiliate'), $singular_resource_name);
        return self::render_entire_crud_form('new', $schema, $submit_action, $nonce, $form_id, $button_text, $item, $hidden_field_tuple);
    }

    /**
     * @param Schema $schema
     * @param string $submit_action
     * @param string $nonce
     * @param string|int $form_id
     * @param string $singular_resource_name
     * @param object|null $item
     * @param array{0: string, 1: string}|null $hidden_field_tuple
     * 
     * @return string
     */
    public static function render_crud_form_edit($schema, $submit_action, $nonce, $form_id, $singular_resource_name, $item = null, $hidden_field_tuple = null)
    {
        $button_text = sprintf(__('Update %1$s', 'solid-affiliate'), $singular_resource_name);
        return self::render_entire_crud_form('edit', $schema, $submit_action, $nonce, $form_id, $button_text, $item, $hidden_field_tuple);
    }

    /**
     * Undocumented function
     *
     * @param FormContext $form_context
     * @param Schema $schema
     * @param string $submit_action
     * @param string $nonce
     * @param string|int $form_id
     * @param string $button_text
     * @param object|null $item
     * @param array{0: string, 1: string}|null $hidden_field_tuple
     * 
     * @return string
     */
    public static function render_entire_crud_form($form_context, $schema, $submit_action, $nonce, $form_id, $button_text, $item = null, $hidden_field_tuple = null)
    {
        if (is_null($item) || !isset($item->id)) {
            $field_id = 0;
        } else {
            $field_id = (int) $item->id;
        }
        ob_start();
?>
        <form action="" method="post" id="<?php echo $form_id ?>">
            <table class="form-table">
                <tbody>
                    <?php echo FormBuilder::build_form($schema, $form_context, $item) ?>
                </tbody>
            </table>

            <input type="hidden" name="field_id" value="<?php echo ($field_id) ?>">

            <?php if (isset($hidden_field_tuple)) { ?>
                <input type="hidden" name="<?php echo ($hidden_field_tuple[0]) ?>" value="<?php echo (esc_attr($hidden_field_tuple[1])) ?>">
            <?php } ?>

            <?php wp_nonce_field($nonce); ?>
            <?php submit_button(__($button_text, 'solid-affiliate'), 'primary', $submit_action); ?>
        </form>
<?php
        return ob_get_clean();
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // Helper Functions for rending parts of form fields
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Renders the title label for a form field.
     * 
     * @param FormFieldArgs $args
     *
     * @return string
     */
    public static function maybe_render_field_title($args)
    {
        if ($args->hide_title) {
            return '';
        } else {
            $required = self::set_required_string($args->required);

			# TOOD: Add configuration for the title's custom class 
            return "<th scope='row'>
                        <div class='sld_field-title {$required}'>
                            <span class='sld_title-text'>{$args->label}</span>
                            " . self::maybe_build_tooltip($args) . "
                        </div>
                    </th>";
        }
    }

    /**
     * Returns the HTML string for the field's description.
     *
     * @param FormFieldArgs $args
     *
     * @return string
     */
    public static function maybe_render_field_description($args)
    {
        if ($args->hide_description) {
            return '';
        } else {
            # TODO: Do we want to insert a hardcoded class that exists even if they apply a custom class?
            #       'description' would always exist and $args->description_class is either our default or custom
            return "<p class='description {$args->description_class}'>{$args->description}</p>";
        }
    }

    /**
     * Returns the HTML for a tooltip.
     *
     * @param FormFieldArgs $args
     *
     * @return string
     */
    public static function maybe_build_tooltip($args)
    {
        $ttc = $args->tooltip_content;
        if (!empty($ttc)) {
            return "<span data-html='true' class='sld-tooltip {$args->tooltip_class}' aria-label='{$ttc}' data-sld-tooltip-content='{$ttc}' data-microtip-position='bottom-center' role='tooltip aria-expanded='false'>
                " . $args->tooltip_body . "
                </span>";
        } else {
            return '';
        }
    }

    /**
     * Takes an assoc array of arbitrary attributes and combines them into an escaped string.
     *
     * Example:
     *   build_custom_attributes_string(['min' => '0', 'step' => 'any'])
     *   => " min='0' step='any' "
     *
     * @param array<string, string> $custom_attributes
     *
     * @return string
     */
    public static function build_custom_attributes_string($custom_attributes)
    {
        if (empty($custom_attributes)) {
            return "";
        }

        // TODO use Map Reduce
        $strings = [];
        foreach ($custom_attributes as $attribute => $value) {
            $strings[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';
        }

        return implode(' ', $strings);
    }

    /**
     * Returns the string to disabled a form field if disabled is true.
     *
     * @param boolean $disabled
     *
     * @return string
     */
    public static function set_disabled_string($disabled)
    {
        return $disabled ? 'disabled' : '';
    }

    /**
     * Returns the string to mark a form field as required if required is true.
     *
     * @param boolean $required
     *
     * @return string
     */
    public static function set_required_string($required)
    {
        return $required ? 'required' : '';
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // Build out the FormFieldArgs for a rendering a form field.
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Returns a FormFieldArgs based on a SchemaEntry
     *
     * @param Schema $schema
     * @param string $field_name
     * @param boolean $hide_titles
     * @param boolean $hide_descriptions
     * @param mixed $value
     *
     *
     * @return FormFieldArgs
     */
    public static function get_field_args_from_schema(Schema $schema, $field_name, $hide_titles, $hide_descriptions, $value = '')
    {
        if (isset($schema->entries[$field_name])) {
            $s = $schema->entries[$field_name];

            $defaults = new FormFieldArgs([
                'label'             => $field_name,
                'field_name'        => $field_name,
                'field_id'          => "{$field_name}",
                'field_type'        => 'text',
                'value'             => '',
                'placeholder'       => '',
                'required'          => false,
                'description'       => '',
                'label_for_value'   => self::_set_label_for_value_default($field_name),
                'select_options'    => [],
                'custom_attributes' => [],
                'wrapper_class'     => '',
                'label_class'       => 'sld_field-label',
                'description_class' => 'sld_field-description',
                'hide_title'        => $hide_titles,
                'hide_description'  => $hide_descriptions,
                'shows_placeholder' => true,
                'tooltip_content'   => '',
                'tooltip_class'     => '',
                'tooltip_body'      => self::DEFAULT_TOOLTIP_ICON
            ]);



            if (is_callable($s->enum_options)) {
                // Enumeration might entail queries and computation: we support its JIT resolution.
                // @todo here pass some value to discern what kind of request this is, to provide context.
                $select_options = call_user_func($s->enum_options);
            } else {
                $select_options = !empty($s->enum_options) ? $s->enum_options : $defaults->select_options;
            }

            $field_type = self::_get_form_field_type_from_schema_entry($s);
            if ($field_type === 'date') {
                if (empty($value)) {
                    $value = '';
                } else {
                    $value = Utils::date_picker_time((string)$value);
                }
            }


            // Support Float types by adding HTML attributes to the number input.
            $custom_attributes = $s->custom_form_input_attributes ? $s->custom_form_input_attributes : $defaults->custom_attributes;
            if ($s->type === 'float') {
                $float_args = [
                    'min' => '0',
                    'step' => '0.0001'
                ];
                $custom_attributes = array_merge($float_args, $custom_attributes);
            }

            $args = new FormFieldArgs([
                'label'             => $s->display_name ? __($s->display_name, 'solid-affiliate') : __($defaults->label, 'solid-affiliate'),
                'field_name'        => $field_name,
                'field_id'          => "{$field_name}",
                'field_type'        => $field_type,
                'value'             => $value,
                # TODO: This defaults to display_name when there is an empty string for placeholder.
                #       If it were a nullable field in SchemaEntry that defaulted to '', then it would be more of an explicit default.
                #       But for now, adding shows_placeholder accounts for this without changing other forms.
                'placeholder'       => self::_set_placeholder($s, $defaults->data),
                'required'          => $s->required ? true : $defaults->required,
                'description'       => $s->form_input_description ? __($s->form_input_description, 'solid-affiliate') : __($defaults->description, 'solid-affiliate'),
                'label_for_value'   => $s->label_for_value ? $s->label_for_value : $defaults->label_for_value,
                'select_options'    => $select_options,
                'custom_attributes' => $custom_attributes,
                'wrapper_class'     => $s->form_input_wrap_class ? $s->form_input_wrap_class : $defaults->wrapper_class,
                'label_class'       => $s->form_label_class ? $s->form_label_class : $defaults->label_class,
                'description_class' => $s->form_description_class ? $s->form_description_class : $defaults->description_class,
                'hide_title'        => $s->hide_form_input_title ? $s->hide_form_input_title : $defaults->hide_title,
                'hide_description'  => $s->hide_form_description ? $s->hide_form_description : $defaults->hide_description,
                'shows_placeholder' => $s->shows_placeholder,
                'tooltip_content'   => $s->form_tooltip_content ? $s->form_tooltip_content : $defaults->tooltip_content,
                'tooltip_class'     => $s->form_tooltip_class ? $s->form_tooltip_class : $defaults->tooltip_class,
                'tooltip_body'      => $defaults->tooltip_body
            ]);

            $args = wp_parse_args($args, $defaults->data);
        } else {
            $args = wp_parse_args([], []);
        }

        return new FormFieldArgs([
                'label'             => Validators::str($args['label']),
                'label_for_value'   => Validators::str($args['label_for_value']),
                'field_name'        => Validators::str($args['field_name']),
                'field_id'          => Validators::str($args['field_id']),
                'field_type'        => Validators::str($args['field_type']),
                'value'             => $args['value'],
                'placeholder'       => Validators::str($args['placeholder']),
                'required'          => boolval($args['required']),
                'description'       => Validators::str($args['description']),
                'select_options'    => Validators::enum_options_array($args['select_options']),
                'custom_attributes' => Validators::custom_form_input_attributes_array($args['custom_attributes']),
                'wrapper_class'     => Validators::str($args['wrapper_class']),
                'label_class'       => Validators::str($args['label_class']),
                'description_class' => Validators::str($args['description_class']),
                'hide_title'        => boolval($args['hide_title']),
                'hide_description'  => boolval($args['hide_description']),
                'shows_placeholder' => boolval($args['shows_placeholder']),
                'tooltip_content'   => Validators::str($args['tooltip_content']),
                'tooltip_class'     => Validators::str($args['tooltip_class']),
                'tooltip_body'      => Validators::str($args['tooltip_body'])
        ]);
    }

    /**
     * Returns the form field type from a SchemEntry.
     *
     * @param SchemaEntry $schema_entry
     *
     * @return string
     */
    private static function _get_form_field_type_from_schema_entry($schema_entry)
    {
        if ($schema_entry->form_input_type_override) {
            return $schema_entry->form_input_type_override;
        }

        if ($schema_entry->is_enum && ($schema_entry->type !== 'multi_checkbox')) {
            return 'select';
        }

        switch ($schema_entry->type) {
            case 'wp_editor':
                return 'wp_editor';
            case 'multi_checkbox':
                return 'multi_checkbox';
            case 'wp_page':
                return 'wp_dropdown_pages';
            case 'bool':
                return 'checkbox';
                // case 'tinyint':
                // case 'smallint':
                // case 'mediumint':
                // case 'int':
                // case 'integer':
            case 'bigint':
                return 'number';
            case 'float':
                return 'number'; # TODO needs to allow decimals. Look into this when doing a Money pass
            case 'varchar':
            case 'text':
                if ($schema_entry->is_password) {
                    return 'password';
                } else {
                    return 'text';
                }
                // case 'tinytext':
                // case 'text':
            case 'datetime':
                return 'date';
            default:
                return 'text';
        }
    }

    /**
     * Renders the title label for a form field.
     * 
     * @param string $field
     * @param FormFieldArgs $args
     *
     * @return string
     */
    private static function _maybe_wrap_field_in_div($field, $args)
    {
        $class = $args->wrapper_class;

        if (empty($class)) {
            return $field;
        } else {
            return "<div class='{$class}'>" . $field . "</div>";
        }
    }

    /**
     * Uses the SchemEntry declarations about the field's placeholder and returns the placeholder text.
     *
     * @param SchemaEntry $s
     * @param FormFieldArgsType $defaults
     *
     * @return string
     */
    private static function _set_placeholder($s, $defaults)
    {
        if ($s->shows_placeholder) {
            return $s->form_input_placeholder ? $s->form_input_placeholder : $defaults['placeholder'];
        } else {
            return '';
        }
    }

    /**
     * Prefixes the SchemaEntry key ($field_name), so that the field name and label for are not the same.
     *
     * @param string $field_name
     *
     * @return string
     */
    private static function _set_label_for_value_default($field_name)
    {
        return 'label-' . $field_name;
    }

    /**
     * HTML for Google reCAPTCHA checkbox v2
     *
     * @return string
     */
    public static function render_solid_g_recaptcha_v2()
    {
        # NOTE: Would probably be better to have the hook per page that a reCAPTCHA(s) will be rendered,
        #       because if there are more than one reCAPTCHAs on a page then the js's will be src'd twice.
        #       But for now, there is only 1 reCAPTCHA per page.
        do_action(self::BEFORE_RECAPTCHA_ACTION);
        $error_msg = __('Client side reCAPTCHA verification failed.', 'solid-affiliate');
        ob_start();
    ?>
        <script src='https://www.google.com/recaptcha/api.js?onload=SLDreCaptchaCallback&render=explicit' async defer></script>
        <?php $sitekey = Validators::str(Settings::get(Settings::KEY_RECAPTCHA_SITE_KEY)) ?>
        <div id="solid-affiliate-g-recaptcha" data-sitekey="<?php echo ($sitekey) ?>" data-error_msg="<?php echo $error_msg ?>"></div>
<?php
        return ob_get_clean();
    }
}
