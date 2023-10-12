<?php

namespace SolidAffiliate\Views\Admin\AffiliateGroups;

use SolidAffiliate\Controllers\AffiliateGroupsController;
use SolidAffiliate\Controllers\AffiliatesController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Models\Affiliate;
use SolidAffiliate\Models\AffiliateGroup;

class EditView
{
    /**
     * @param object $item
     * @return string
     */
    public static function render($item)
    {
        $singular = __('Affiliate Group', 'solid-affiliate');
        $form_id = 'affiliate-groups-edit';
        $schema = AffiliateGroup::schema();
        $nonce = AffiliateGroupsController::NONCE_SUBMIT_AFFILIATE;
        $submit_action = AffiliateGroupsController::POST_PARAM_SUBMIT_AFFILIATE_GROUP;

        $form = FormBuilder::render_crud_form_edit($schema, $submit_action, $nonce, $form_id, $singular, $item);
        ob_start();
?>
        <div class="wrap">
            <h1><?php echo sprintf(__('Update %1$s', 'solid-affiliate'), $singular); ?></h1>
            <?php echo $form ?>
        </div>
<?php
        return ob_get_clean();
    }
}
