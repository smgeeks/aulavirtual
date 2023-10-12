<?php

namespace SolidAffiliate\Lib\FormBuilder;

use SolidAffiliate\Lib\VO\FormFieldArgs;

class WpEditorField
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
    public static function build_wp_editor_field($args, $disabled = false)
    {
        // NOTE: to simplify making the type signature of $value in this function comply with the type signature of
        // the function FormBuilder::get_field_args_from_schema, I left the type sig of value mixed, when in reality it exects string.
        $value = (string)$args->value;

        // Turn on the output buffer
        ob_start();

        // Echo the editor to the buffer
        wp_editor($value, $args->field_name);

        // Store the contents of the buffer in a variable
        $editor = ob_get_clean();

        return self::_html($args, $editor);
    }

    /**
     * Undocumented function
     *
     * @param FormFieldArgs $args
     * @param string|false $editor
     *
     * @return string
     */
    private static function _html($args, $editor)
    {
        # TODO: Render description using shared function?
        return "
<tr class='row-{$args->field_name}'>
    " . FormBuilder::maybe_render_field_title($args) . "
    <td>
        {$editor}
        <p style='white-space: pre-line' class='{$args->description_class}'>{$args->description}</p>
    </td>
</tr>
";
    }
}

