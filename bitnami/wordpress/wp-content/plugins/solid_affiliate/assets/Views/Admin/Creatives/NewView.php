<?php

namespace SolidAffiliate\Views\Admin\Creatives;

use SolidAffiliate\Controllers\CreativesController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Models\Creative;

class NewView
{

    /**
     * @return string
     */
    public static function render()
    {
        $singular = __('Creative', 'solid-affiliate');
        $form_id = 'creatives-new';
        $schema = Creative::schema();
        $nonce = CreativesController::NONCE_SUBMIT_CREATIVE;
        $submit_action = CreativesController::POST_PARAM_SUBMIT_CREATIVE;

        $form = FormBuilder::render_crud_form_new($schema, $submit_action, $nonce, $form_id, $singular);
        ob_start();
?>

        <div class="wrap">
            <h1><?php echo sprintf(__('Add New %1$s', 'solid-affiliate'), $singular); ?></h1>
            <?php echo $form ?>
        </div>
<?php
        return ob_get_clean();
    }
}
