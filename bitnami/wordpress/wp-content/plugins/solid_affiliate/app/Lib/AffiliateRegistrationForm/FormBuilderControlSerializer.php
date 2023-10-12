<?php

namespace SolidAffiliate\Lib\AffiliateRegistrationForm;

use SolidAffiliate\Lib\SchemaFunctions;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Lib\VO\FormBuilderControl;
use SolidAffiliate\Lib\VO\Schema;
use SolidAffiliate\Lib\VO\SchemaEntry;

/**
 * @psalm-import-type FormBuilderControlType from \SolidAffiliate\Lib\VO\FormBuilderControl
 * @psalm-import-type ControlMaxlengthType from \SolidAffiliate\Lib\VO\FormBuilderControl
 * @psalm-import-type ControlValuesArrayType from \SolidAffiliate\Lib\VO\FormBuilderControl
 * @psalm-import-type ControlTextareaRowsType from \SolidAffiliate\Lib\VO\FormBuilderControl
 * @psalm-import-type ControlNumberMinType from \SolidAffiliate\Lib\VO\FormBuilderControl
 * @psalm-import-type ControlNumberMaxType from \SolidAffiliate\Lib\VO\FormBuilderControl
 * @psalm-import-type ControlType from \SolidAffiliate\Lib\VO\FormBuilderControl
 * @psalm-import-type TextSubtype from \SolidAffiliate\Lib\VO\FormBuilderControl
 */
class FormBuilderControlSerializer
{
    # TODO:1: Do we want to force this?
    /** @var ControlValuesArrayType */
    const DEFAULT_SINGLE_CHECKBOX_VALUES = [['label' => '', 'value' => '', 'selected' => false]];

    /**
     * Returns an array of FormBuilderControl's for the custom form builder UI given a Schema
     *
     * @param Schema $schema
     *
     * @return array<FormBuilderControlType>
     */
    public static function from_schema_to_controls($schema)
    {
        return array_reduce(
            SchemaFunctions::keys_on_non_admin_new_form_from_schema($schema, [true, 'hidden_and_disabled']),
            /**
             * TODO:3: Why can't psalm know that the type-alias FormBuilderControlType is equal to the array literal?
             * @param array<empty>|array<array-key, array{description: string, descriptionText: string, editable: bool, label: string, max: string, maxlength: string, min: string, name: string, placeholder: string, required: bool, rows: string, subtype: string, type: string, values: array<array-key, array{label: string, selected: bool, value: string}>}> $arr
             * @param string $name
             */
            function ($arr, $name) use ($schema) {
                $entry = $schema->entries[$name];

                if (in_array($entry->type, SchemaEntry::FORM_TYPES_SUPPORTED_BY_FORM_BUILDER) || in_array($entry->form_input_type_override, SchemaEntry::FORM_TYPES_SUPPORTED_BY_FORM_BUILDER)) {
                    $arr[] = self::_build_form_builder_control($entry, $name)->data;
                }

                return $arr;
            },
            []
        );
    }

    /**
     * Returns a FormBuilderControl given a SchemaEntry
     *
     * @param SchemaEntry $entry
     * @param string $name
     *
     * @return FormBuilderControl
     */
    private static function _build_form_builder_control($entry, $name)
    {
        $type = self::_control_type($entry);

        return new FormBuilderControl([
            'label' => $entry->display_name,
            'placeholder' => $entry->form_input_placeholder,
            'name' => $name,
            'type' => $type,
            'descriptionText' => $entry->form_input_description,
            'description' => $entry->form_tooltip_content,
            'required' => $entry->required,
            'values' => self::_control_values($entry),
            'subtype' => self::_control_subtype($entry, $type),
            'maxlength' => self::_maxlength_custom_attribute($entry, $type),
            'rows' => self::_rows_custom_attribute($entry, $type),
            'min' => self::_min_custom_attribute($entry, $type),
            'max' => self::_max_custom_attribute($entry, $type),
            'editable' => self::_is_editable($entry)
        ]);
    }

    /**
     * Returns the type for a FormBuilderControl given a Schema Entry
     *
     * @param SchemaEntry $entry
     *
     * @return ControlType
     */
    private static function _control_type($entry)
    {
        if (!is_null($entry->form_input_type_override)) {
            if ($entry->form_input_type_override === 'radio_buttons') {
                return FormBuilderControl::CONTROL_TYPE_RADIO_GROUP;
            }
        }

        if (SchemaEntry::is_form_select_entry($entry)) {
            return FormBuilderControl::CONTROL_TYPE_SELECT;
        }

        switch ($entry->type) {
            case 'text':
                return FormBuilderControl::CONTROL_TYPE_TEXTAREA;
            case 'varchar':
                return FormBuilderControl::CONTROL_TYPE_TEXT;
            case 'bigint':
                return FormBuilderControl::CONTROL_TYPE_NUMBER;
            case 'bool':
                return FormBuilderControl::CONTROL_TYPE_CHECKBOX_GROUP;
            case 'multi_checkbox':
                return FormBuilderControl::CONTROL_TYPE_CHECKBOX_GROUP;
            case 'textarea':
                return FormBuilderControl::CONTROL_TYPE_TEXTAREA;
            default:
                # NOTE: Should never reach here do the conditional in FormBuilderControlSerializer::from_schema_to_controls(),
                #       but psalm does not know this.
                return FormBuilderControl::CONTROL_TYPE_TEXT;
        }
    }

    /**
     * Returns the FormBuilderControl's subtype given a SchemaEntry and ControlType
     *
     * @param SchemaEntry $entry
     * @param ControlType $type
     *
     * @return TextSubtype
     */
    private static function _control_subtype($entry, $type)
    {
        if (!in_array($type, FormBuilderControl::TYPES_WITH_SUBTYPES)) {
            return '';
        }

        if ($entry->is_password) {
            return FormBuilderControl::TEXT_SUBTYPE_PASSWORD;
        }

        switch ($entry->form_input_type_override) {
            case 'email':
                return FormBuilderControl::TEXT_SUBTYPE_EMAIL;
            case 'url':
                return FormBuilderControl::TEXT_SUBTYPE_URL;
            default:
                return FormBuilderControl::TEXT_SUBTYPE_TEXT;
        }
    }

    /**
     * Returns the values for a FormBuilderControl given a SchemaEntry's enum options.
     *
     * @param SchemaEntry $entry
     *
     * @return ControlValuesArrayType
     */
    private static function _control_values($entry)
    {
        if ($entry->type === 'bool') {
            return self::DEFAULT_SINGLE_CHECKBOX_VALUES;
        } else {
            return self::_enum_options_to_control_values($entry);
        }
    }

    /**
     * Converts an SchemaEntry's enum options to a FormBuilderControl's values.
     *
     * @param SchemaEntry $entry
     *
     * @return ControlValuesArrayType
     */
    private static function _enum_options_to_control_values($entry)
    {
        $enum_options = $entry->enum_options;

        if (is_callable($enum_options)) {
            $options = call_user_func($enum_options);
        } else {
            $options = $enum_options;
        }

        return array_map(
            function ($tuple) {
                return [
                    'label' => $tuple[1],
                    'value' => $tuple[0],
                    'selected' => false
                ];
            },
            $options
        );
    }

    /**
     * Converts an SchemaEntry's custom form input attributes to a FormBuilderControl's maxlength.
     *
     * @param SchemaEntry $entry
     * @param ControlType $type
     *
     * @return ControlMaxlengthType
     */
    private static function _maxlength_custom_attribute($entry, $type)
    {
        $attrs = $entry->custom_form_input_attributes;

        if (isset($attrs['maxlength']) && in_array($type, FormBuilderControl::TYPES_WITH_MAXLENGTH)) {
            return Validators::numeric_str($attrs['maxlength']);
        } else {
            return '';
        }
    }

    /**
     * Converts an SchemaEntry's custom form input attributes to a FormBuilderControl's rows.
     *
     * @param SchemaEntry $entry
     * @param ControlType $type
     *
     * @return ControlTextareaRowsType
     */
    private static function _rows_custom_attribute($entry, $type)
    {
        $attrs = $entry->custom_form_input_attributes;

        if (isset($attrs['rows']) && in_array($type, FormBuilderControl::TYPES_WITH_ROWS)) {
            return Validators::numeric_str($attrs['rows']);
        } else {
            return '';
        }
    }

    /**
     * Converts an SchemaEntry's custom form input attributes to a FormBuilderControl's min.
     *
     * @param SchemaEntry $entry
     * @param ControlType $type
     *
     * @return ControlNumberMinType
     */
    private static function _min_custom_attribute($entry, $type)
    {
        $attrs = $entry->custom_form_input_attributes;

        if (isset($attrs['min']) && in_array($type, FormBuilderControl::TYPES_WITH_MIN)) {
            return Validators::numeric_str($attrs['min']);
        } else {
            return '';
        }
    }

    /**
     * Converts an SchemaEntry's custom form input attributes to a FormBuilderControl's max.
     *
     * @param SchemaEntry $entry
     * @param ControlType $type
     *
     * @return ControlNumberMaxType
     */
    private static function _max_custom_attribute($entry, $type)
    {
        $attrs = $entry->custom_form_input_attributes;

        if (isset($attrs['max']) && in_array($type, FormBuilderControl::TYPES_WITH_MAX)) {
            return Validators::numeric_str($attrs['max']);
        } else {
            return '';
        }
    }

    /**
     * Whether or not a field is editable.
     *
     * @param SchemaEntry $entry
     *
     * @return boolean
     */
    private static function _is_editable($entry)
    {
        if ($entry->show_on_non_admin_edit_form === true) {
            return true;
        } else {
            return false;
        }
    }
}