<?php

namespace SolidAffiliate\Views\Admin\Referrals;

use SolidAffiliate\Controllers\ReferralsController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Models\Referral;

class EditView
{
    /**
     * @param object $item
     * @return string
     */
    public static function render($item)
    {
        $singular = __('Referral', 'solid-affiliate');
        $form_id = 'referrals-edit';
        $schema = Referral::schema();
        $nonce = ReferralsController::NONCE_SUBMIT_REFERRAL;
        $submit_action = ReferralsController::POST_PARAM_SUBMIT_REFERRAL;

        $maybe_serialized_item_commissions = (string)$item->serialized_item_commissions;
        $item_commissions_view = "";
        if (!empty($maybe_serialized_item_commissions)) {
            $item_commissions = Validators::arr_of_item_commission(
                unserialize($maybe_serialized_item_commissions)
            );

            $item_commissions_view = ItemCommissionsView::render($item_commissions, (int)$item->id, (float)$item->commission_amount);
        }

        $form = FormBuilder::render_crud_form_edit($schema, $submit_action, $nonce, $form_id, $singular, $item);
        ob_start();
?>

        <div class="wrap">
            <h1><?php echo sprintf(__('Update %1$s', 'solid-affiliate'), $singular); ?></h1>

            <?php echo $form ?>
            <div style="padding: 40px 0; width: 75%;">
                <?php echo ($item_commissions_view) ?>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}
