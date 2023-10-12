<?php

namespace SolidAffiliate\Views\Admin\AffiliateGroups;

use SolidAffiliate\Controllers\AffiliateGroupsController;
use SolidAffiliate\Controllers\AffiliatesController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Models\Affiliate;
use SolidAffiliate\Models\AffiliateGroup;

class NewView
{

    /**
     * @return string
     */
    public static function render()
    {
        $singular = __('Affiliate Group', 'solid-affiliate');
        $form_id = 'affiliate-groups-new';
        $schema = AffiliateGroup::schema();
        $nonce = AffiliateGroupsController::NONCE_SUBMIT_AFFILIATE;
        $submit_action = AffiliateGroupsController::POST_PARAM_SUBMIT_AFFILIATE_GROUP;

        // initialize the $item with default data if applicable
        $form = FormBuilder::render_crud_form_new($schema, $submit_action, $nonce, $form_id, $singular);

        ob_start();
?>
        <div class="wrap">
            <h1><?php echo sprintf(__('Add New %1$s', 'solid-affiliate'), $singular); ?></h1>

            <div class="notice notice-info inline">
                <p>
                    <strong><?php _e('Notes on creating new Affiliate Groups', 'solid-affiliate') ?></strong>
                <ul>
                    <li><?php _e('You will be able to add and remove affiliates to this group after the group is created.', 'solid-affiliate') ?></li>
                    <li><?php _e('The group name, commission rate, and commission type will be editable by you after creation as well.', 'solid-affiliate') ?></li>
                </ul>
                </p>
            </div>

            <?php echo $form ?>
        </div>

<?php
        return ob_get_clean();
    }
}
