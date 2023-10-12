<?php

namespace SolidAffiliate\Models;

use SolidAffiliate\Lib\ControllerFunctions;
use SolidAffiliate\Lib\CustomAffiliateSlugs\AffiliateCustomSlugBase;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Lib\MikesDataModel;
use SolidAffiliate\Lib\MikesDataModelTrait;
use SolidAffiliate\Lib\SchemaFunctions;
use SolidAffiliate\Lib\Utils;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Lib\VO\DatabaseTableOptions;
use SolidAffiliate\Lib\VO\Either;
use SolidAffiliate\Lib\VO\Schema;
use SolidAffiliate\Lib\VO\SchemaEntry;
use SolidAffiliate\Models\Affiliate;
use SolidAffiliate\Views\Admin\Affiliates\EditView;

/**
 * @psalm-import-type EnumOptionsReturnType from \SolidAffiliate\Lib\VO\SchemaEntry
 *
 * @psalm-type AffiliateMetaDataToUpsert_type = array{id?: positive-int, affiliate_id: positive-int, meta_key: AffiliateMeta::META_KEY_*, meta_value: mixed}
 *
 * @property array $attributes
 *
 * @property int $id
 * @property int $affiliate_id
 * @property string $meta_key
 * @property string $meta_value
 * @property string $created_at
 * @property string $updated_at
 */
class AffiliateMeta extends MikesDataModel
{
    use MikesDataModelTrait;

    const TABLE = 'solid_affiliate_affiliate_meta';
    const PRIMARY_KEY = 'id';
    const MODEL_NAME = 'AffiliateMeta';

    protected $table = self::TABLE;
    protected $primary_key = self::PRIMARY_KEY;

    const META_KEY_CUSTOM_AFFILIATE_SLUG = 'sld_custom_affiliate_slug';
    const META_KEY_CUSTOM_AFFILIATE_SLUG_IS_EDITABLE_BY_AFFILIATE = 'sld_custom_affiliate_slug_affiliate_can_edit';
    const META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS = 'sld_disable_referral_notification_emails';
    const OPTIONS_FOR_META_KEY = [
        self::META_KEY_CUSTOM_AFFILIATE_SLUG,
        self::META_KEY_CUSTOM_AFFILIATE_SLUG_IS_EDITABLE_BY_AFFILIATE,
        self::META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS,
    ];

    const SCHEMA_KEY_AFFILIATE_ID = 'affiliate_id';
    const SCHEMA_KEY_META_KEY = 'meta_key';
    const SCHEMA_KEY_META_VALUE = 'meta_value';

    const POST_PARAM_SUBMIT_MISC_META = 'solid-affiliate_submit_misc_meta';
    const NONCE_SUBMIT_MISC_META = 'solid-affiliate_submit_misc_meta_nonce';

    /**
     * @var Schema<AffiliateMeta::SCHEMA_KEY_*|AffiliateMeta::PRIMARY_KEY|'created_at'|'updated_at'>|null
     */
    private static $schema_cache = null;

    /**
     * @return array{0: string, 1: string}
     */
    private static function _custom_slug_enum_option()
    {
        return [self::META_KEY_CUSTOM_AFFILIATE_SLUG, __('Custom Affiliate Slug Data', 'solid-affiliate')];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function _custom_slug_permissions_enum_option()
    {
        return [self::META_KEY_CUSTOM_AFFILIATE_SLUG_IS_EDITABLE_BY_AFFILIATE, __('Custom Affiliate Slug Permissions', 'solid-affiliate')];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function _disable_referral_notification_emails_enum_option()
    {
        return [self::META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS, __('Disable Referral Notification Emails', 'solid-affiliate')];
    }

    /**
     * @return EnumOptionsReturnType
     */
    private static function _all_meta_key_enum_options()
    {
        return [
            self::_custom_slug_enum_option(),
            self::_custom_slug_permissions_enum_option(),
            self::_disable_referral_notification_emails_enum_option(),
        ];
    }

    /**
     * @return Schema<AffiliateMeta::SCHEMA_KEY_*|AffiliateMeta::PRIMARY_KEY|'created_at'|'updated_at'>
     */
    public static function schema()
    {
        if (!is_null(self::$schema_cache)) {
            return self::$schema_cache;
        }

        $entries = [
            self::PRIMARY_KEY => new SchemaEntry([
                'type' => 'bigint',
                'length' => 20,
                'auto_increment' => true,
                'primary_key' => true,
                'show_list_table_column' => true,
                'display_name' => __('ID', 'solid-affiliate'),
                'show_on_new_form' => false,
                'show_on_edit_form' => 'hidden_and_disabled',
                'user_default' => null,
                'validate_callback' =>
                /** @param mixed $id */
                static function ($id) {
                    return is_int($id);
                },
                'sanitize_callback' =>
                /** @param mixed $id */
                static function ($id) {
                    return (int) $id;
                }
            ]),
            self::SCHEMA_KEY_AFFILIATE_ID => new SchemaEntry([
                'type' => 'bigint',
                'length' => 20,
                'show_list_table_column' => true,
                'required' => true,
                'key' => true,
                'display_name' => __('Affiliate ID', 'solid-affiliate'),
                'form_input_description' => __("The Associated Affiliate", 'solid-affiliate'),
                'show_on_new_form' => true,
                'show_on_edit_form' => 'hidden_and_disabled',
                'user_default' => null,
                'validate_callback' =>
                /** @param mixed $id */
                static function ($id) {
                    return is_int($id) && (Affiliate::find($id));
                },
                'sanitize_callback' =>
                /** @param mixed $id */
                static function ($id) {
                    return (int) $id;
                }
            ]),
            self::SCHEMA_KEY_META_KEY => new SchemaEntry([
                'type' => 'varchar',
                'length' => 255,
                'required' => true,
                'key' => true,
                'display_name' => __('Data Type', 'solid-affiliate'),
                'form_input_description' => __('Used to identify what type of data is associated to the Affiliate.', 'solid-affiliate'),
                'is_enum' => true,
                'enum_options' => self::_all_meta_key_enum_options(),
                'show_on_new_form' => true,
                'show_on_edit_form' => 'hidden_and_disabled',
                'show_list_table_column' => true,
                'user_default' => '',
                'validate_callback' =>
                /** @param mixed $key */
                static function ($key) {
                    return in_array(Validators::str($key), self::OPTIONS_FOR_META_KEY, true);
                },
                'sanitize_callback' =>
                /** @param mixed $key */
                static function ($key) {
                    return strtolower(trim(Validators::str($key)));
                }
            ]),
            self::SCHEMA_KEY_META_VALUE => new SchemaEntry([
                'type' => 'text',
                # If we can allow for false and required in upsert, then leave false?
                'required' => false,
                'display_name' => __('Data Value', 'solid-affiliate'),
                'form_input_description' => __('The data value associated to the Affiliate.', 'solid-affiliate'),
                'show_on_new_form' => true,
                'show_on_edit_form' => true,
                'show_list_table_column' => true
            ]),
            'created_at' => new SchemaEntry([
                'type' => 'datetime',
                'key' => true,
                'display_name' => __('Created At', 'solid-affiliate'),
                'show_list_table_column' => false,
                'validate_callback' =>
                /** @param mixed $created_at */
                static function ($created_at) {
                    return is_int(strtotime(Validators::str($created_at)));
                },
                'sanitize_callback' =>
                /** @param mixed $created_at */
                static function ($created_at) {
                    return Validators::str($created_at);
                }
            ]),
            'updated_at' => new SchemaEntry([
                'type' => 'datetime',
                'display_name' => __('Updated At', 'solid-affiliate'),
                'validate_callback' =>
                /** @param mixed $updated_at */
                static function ($updated_at) {
                    return is_int(strtotime(Validators::str($updated_at)));
                },
                'sanitize_callback' =>
                /** @param mixed $updated_at */
                static function ($updated_at) {
                    return Validators::str($updated_at);
                }
            ])
        ];

        self::$schema_cache = new Schema(['entries' => $entries]);

        return self::$schema_cache;
    }

    /**
     * @return DatabaseTableOptions
     */
    public static function ct_table_options()
    # TODO: Do we care about DB level conditional unique constraints? My feeling is it may be difficult without knowing what the DB is
    {
        return new DatabaseTableOptions(array(
            'singular'     => 'Affiliate Meta',
            'plural'       => 'Affiliate Metas',
            'show_ui'      => false,        // Make custom table visible on admin area (check 'views' parameter)
            'show_in_rest' => false,        // Make custom table visible on rest API
            # TODO: Is this version per DB table or DB Schema?
            'version'      => 1,           // A new table is always 1, bump if there is a DB affecting change to this table
            'primary_key'  => self::PRIMARY_KEY,    // If not defined will be checked on the field that hsa primary_key as true on schema
            'schema'       => self::schema()
        ));
    }

    /**
     * @return string[]
     *
     * @psalm-return array<array-key, string>
     */
    public static function required_fields()
    {
        $schema = self::schema();
        $required_fields = SchemaFunctions::required_fields_from_schema($schema);

        return $required_fields;
    }

    /**
     * General function to sanitize and validate AffiliateMeta of any type.
     *
     * @param AffiliateMetaDataToUpsert_type $data
     * @param boolean $use_defaults
     *
     * @return Either<int>
     */
    public static function upsert_with_value_sanitization_and_validation($data, $use_defaults = false)
    {
        if (isset($data[self::SCHEMA_KEY_META_VALUE])) {
            /** @var mixed $meta_value */
            $meta_value = $data[self::SCHEMA_KEY_META_VALUE];
        } else {
            $error_msg = __('Missing meta_value. A value for this AffiliateMeta is required.', 'solid-affiliate');
            return new Either([$error_msg], 0, false);
        }

        $affiliate_id = Validators::int_from_array($data, self::SCHEMA_KEY_AFFILIATE_ID);
        if (Utils::is_number_zero($affiliate_id)) {
            # NOTE: Should probably allow for updating AffiliateMeta without an Affiliate ID, as it can be identified with just the ID
            $error_msg = __('Invalid Affiliate ID. An Affiliate ID is required to create or update this AffiliateMeta.', 'solid-affiliate');
            return new Either([$error_msg], 0, false);
        }

        $meta_key = Validators::str_from_array($data, self::SCHEMA_KEY_META_KEY);

        switch ($meta_key) {
            case self::META_KEY_CUSTOM_AFFILIATE_SLUG:
                $sanitized_value = AffiliateCustomSlugBase::sanitize_slug($meta_value, false);
                $validate_tuple = AffiliateCustomSlugBase::validate_slug($sanitized_value, $affiliate_id);
                break;
            case self::META_KEY_CUSTOM_AFFILIATE_SLUG_IS_EDITABLE_BY_AFFILIATE:
                $sanitized_value = (bool)$meta_value;
                $validate_tuple = [true, ''];
                break;
            case self::META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS:
                $sanitized_value = (bool)$meta_value;
                $validate_tuple = [true, ''];
                break;
            default:
                $error_msg = __('Invalid meta_key. The meta key provided is of an unsupported AffiliateMeta type.', 'solid-affiliate') . ' - ' . $meta_key;
                return new Either([$error_msg], 0, false);
        }

        $validated_data = array_merge($data, [
            self::SCHEMA_KEY_AFFILIATE_ID => $affiliate_id,
            self::SCHEMA_KEY_META_KEY => $meta_key,
            self::SCHEMA_KEY_META_VALUE => $sanitized_value
        ]);

        if ($validate_tuple[0]) {
            return self::upsert($validated_data, $use_defaults);
        } else {
            return new Either([$validate_tuple[1]], 0, false);
        }
    }

    /**
     * @return void
     */
    public static function register_affiliate_edit_hooks()
    {
        add_filter(EditView::AFFILIATE_EDIT_AFTER_FORM_FILTER, [self::class, 'render_affiliate_meta_on_affiliate_edit'], 10, 2);
    }

    /**
     * Return to the AFFILIATE_EDIT_AFTER_FORM_FILTER filter to be rendered on the Affiliate Edit page.
     * Displays the relevant forms, table data, and settings to the admin.
     *
     * @param array<string> $panels
     * @param Affiliate|null $affiliate
     *
     * @return array<string>
     */
    public static function render_affiliate_meta_on_affiliate_edit($panels, $affiliate)
    {
        if (is_null($affiliate)) {
            return $panels;
        }

        $header = '<h2 id="edit-affiliate-affiliate_meta">' . __('Misc. Settings', 'solid-affiliate') . '</h2>';
        $schema = self::misc_meta_schema(); // TODO
        $object = (object)self::misc_meta_values_for_affiliate_id($affiliate->id); // TODO

        $form =  FormBuilder::render_entire_crud_form(
            'edit',
            $schema,
            self::POST_PARAM_SUBMIT_MISC_META,
            self::NONCE_SUBMIT_MISC_META,
            'affiliate-meta-admin-edit',
            'Update affiliate setting',
            $object
        );

        $meta_panel = $header . $form;

        array_unshift($panels, $meta_panel);
        return $panels;
    }


    /**
     * @return Schema
     */
    public static function misc_meta_schema()
    {
        // make an entry for the Disable Referral Notification Emails
        $entries = [
            self::META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS => new SchemaEntry([
                'type' => 'bool',
                'default' => false,
                'display_name' => __('Disable Referral Notification Emails', 'solid-affiliate'),
                'show_on_edit_form' => true,
                'form_input_description' => __('Turn this setting on to prevent any referral notification emails from being sent to this specific affiliate.', 'solid-affiliate')
            ]),
            // TODO reuse from above
            self::SCHEMA_KEY_AFFILIATE_ID => new SchemaEntry([
                'type' => 'bigint',
                'length' => 20,
                'show_list_table_column' => true,
                'required' => true,
                'key' => true,
                'display_name' => __('Affiliate ID', 'solid-affiliate'),
                'form_input_description' => __("The Associated Affiliate", 'solid-affiliate'),
                'show_on_new_form' => true,
                'show_on_edit_form' => 'hidden',
                'user_default' => null,
                'validate_callback' =>
                /** @param mixed $id */
                static function ($id) {
                    return is_int($id) && (Affiliate::find($id));
                },
                'sanitize_callback' =>
                /** @param mixed $id */
                static function ($id) {
                    return (int) $id;
                }
            ]),
        ];

        return new Schema(['entries' => $entries]);
    }

    /**
     * @param int $affiliate_id
     * @return array
     */
    public static function misc_meta_values_for_affiliate_id($affiliate_id)
    {
        $meta_keys = Validators::array_of_string(array_keys(self::misc_meta_schema()->entries));

        $meta_values = [];

        foreach ($meta_keys as $_index => $meta_key) {
            /**
             * @psalm-suppress MixedAssignment
             * @psalm-suppress ArgumentTypeCoercion
             */
            $meta_values[$meta_key] = self::get_meta_value($affiliate_id, $meta_key);
        }

        $meta_values[self::SCHEMA_KEY_AFFILIATE_ID] = $affiliate_id;

        return $meta_values;
    }

    /**
     * 
     * @param int $affiliate_id
     * @param self::META_KEY* $meta_key
     * 
     * @return AffiliateMeta|null
     */
    public static function get_meta_for($affiliate_id, $meta_key)
    {
        $maybe_meta = AffiliateMeta::find_where([
            AffiliateMeta::SCHEMA_KEY_AFFILIATE_ID => $affiliate_id,
            AffiliateMeta::SCHEMA_KEY_META_KEY => $meta_key
        ]);

        return $maybe_meta;
    }

    /**
     * Undocumented function
     *
     * @param int $affiliate_id
     * @param self::META_KEY* $meta_key
     * @return mixed|null
     */
    public static function get_meta_value($affiliate_id, $meta_key)
    {
        $maybe_meta = self::get_meta_for($affiliate_id, $meta_key);

        if (is_null($maybe_meta)) {
            return null;
        } else {
            return $maybe_meta->meta_value;
        }
    }

    /**
     * @return void
     */
    public static function POST_update_misc_meta_handler()
    {
        $eitherPostParams = ControllerFunctions::extract_and_validate_POST_params(
            $_POST,
            ['affiliate_id', self::META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS], // TODO
            self::misc_meta_schema()
        );

        if ($eitherPostParams->isLeft) {
            ControllerFunctions::handle_redirecting_and_exit('REDIRECT_BACK', $eitherPostParams->left);
        } else {
            $params = $eitherPostParams->right;
            // TODO This currently assumes just 1 meta field is in the form. We'd have to loop in reality
            $affiliate_meta_data = [
                self::SCHEMA_KEY_AFFILIATE_ID => (int)$params['affiliate_id'],
                self::SCHEMA_KEY_META_KEY => self::META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS,
                self::SCHEMA_KEY_META_VALUE => $params[self::META_KEY_IS_DISABLE_REFERRAL_NOTIFICATION_EMAILS]
            ];
            // add a primary key to the meta_data if an AffiliateMeta record already exists
            $maybe_affiliate_meta = self::get_meta_for($affiliate_meta_data[self::SCHEMA_KEY_AFFILIATE_ID], $affiliate_meta_data[self::SCHEMA_KEY_META_KEY]);
            if ($maybe_affiliate_meta) {
                $affiliate_meta_data[self::PRIMARY_KEY] = (int)$maybe_affiliate_meta->id;
            }

            self::upsert_with_value_sanitization_and_validation($affiliate_meta_data, false);
        }
    }
}
