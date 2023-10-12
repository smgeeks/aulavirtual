<?php

namespace SolidAffiliate\Lib\FormBuilder;

use SolidAffiliate\Lib\VO\FormFieldArgs;

class WpDropDownPages
{
    /**
     * Undocumented function
     *
     * @param FormFieldArgs $args
     * @param boolean $disabled
     *
     * @return string
     */
    # TODO: Does not seem to use the disabled flag
    public static function build_wp_dropdown_pages($args, $disabled = false)
    {
        $select = wp_dropdown_pages([
            'echo' => 0,
            'name' => $args->field_name,
            'selected' => (int)$args->value,
            'show_option_none' => '-- ' . __('select a page', 'solid-affiliate') . ' --'
        ]);

        return self::_html($args, $select);
    }

    /**
     * Undocumented function
     *
     * @param FormFieldArgs $args
     * @param string $select
     *
     * @return string
     */
    private static function _html($args, $select)
    {
        return "
<tr class='row-{$args->field_name}'>
    " .FormBuilder::maybe_render_field_title($args) . "
    <td>
        <label for='{$args->label_for_value}' class='{$args->label_class}'>
            {$select}
        </label>
        " . FormBuilder::maybe_render_field_description($args) . "
    </td>
</tr>
";
    }
}



