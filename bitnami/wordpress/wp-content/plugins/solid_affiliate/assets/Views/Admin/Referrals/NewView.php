<?php

namespace SolidAffiliate\Views\Admin\Referrals;

use SolidAffiliate\Controllers\ReferralsController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Models\Referral;

class NewView
{

    /**
     * @return string
     */
    public static function render()
    {
        $singular = __('Referral', 'solid-affiliate');
        $form_id = 'referrals-new';
        $schema = Referral::schema();
        $nonce = ReferralsController::NONCE_SUBMIT_REFERRAL;
        $submit_action = ReferralsController::POST_PARAM_SUBMIT_REFERRAL;
        $form = FormBuilder::render_crud_form_new($schema, $submit_action, $nonce, $form_id, $singular);
        ob_start();
?>
        <div class="wrap">
            <h1><?php echo sprintf(__('Add New %1$s', 'solid-affiliate'), $singular); ?></h1>

            <div class="notice notice-info inline">
                <p>
                    <strong><?php _e('Notes on creating new Referrals via this form', 'solid-affiliate') ?></strong>
                <ul>
                    <ul>
                        <?php printf(__('<li>&bull; Solid Affiliate is primarily designed to work with WooCommerce and WooCommerce Subscriptions, automatically generating Referrals from orders as they happen.', 'solid-affiliate'), admin_url('users.php')) ?>
                        <?php printf(__('<li>&bull; This form will allow you to manually create a Referral record, while keeping the data requirements strict to ensure reliability.', 'solid-affiliate'), admin_url('users.php')) ?>
                        <?php printf(__('<li>&bull; A real Visit <em>or</em> Coupon must be associated with every Referral. If the <em>Referral Source</em> is <em>Visit</em>, you can leave a 0 for <em>Coupon ID</em>; and vice versa.</li>', 'solid-affiliate'), admin_url('users.php')) ?>
                    </ul>
                    </p>
            </div>

            <?php echo $form ?>
        </div>
<?php
        return ob_get_clean();
    }
}
