<?php

namespace SolidAffiliate\Views\Admin\Affiliates;

use SolidAffiliate\Controllers\AffiliatesController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Models\Affiliate;

class NewView
{

    /**
     * @return string
     */
    public static function render()
    {
        $singular = __('Affiliate', 'solid-affiliate');
        $form_id = 'affiliates-new';
        $schema = Affiliate::schema_with_custom_registration_data();
        $nonce = AffiliatesController::NONCE_SUBMIT_AFFILIATE;
        $submit_action = AffiliatesController::POST_PARAM_SUBMIT_AFFILIATE;

        // if there's a user_id and it's proper in the query params, pass in a partial item to the form
        $item = (object)['user_id' => (isset($_REQUEST['user_id']) && ((int)$_REQUEST['user_id'] != 0)) ? (int)$_REQUEST['user_id'] : null];
        $form = FormBuilder::render_crud_form_new($schema, $submit_action, $nonce, $form_id, $singular, $item);
        ob_start();
?>
        <div class="wrap">
            <h1><?php echo sprintf(__('Add New %1$s', 'solid-affiliate'), $singular); ?></h1>

            <div class="notice notice-info inline">
                <p>
                    <strong><?php _e('Notes on creating new Affiliates', 'solid-affiliate') ?></strong>
                <ul>
                    <?php printf(__('<li>&bull; It is easier to <a href="%1$s">search for a User</a>, click <em>Edit</em> to see the User profile, and then click the <em>Register this User as an Affiliate</em> under the <code>Solid Affiliate</code> section within their profile. </li>', 'solid-affiliate'), admin_url('users.php')) ?>
                </ul>
                </p>
            </div>

            <?php echo $form ?>
        </div>

<?php
        return ob_get_clean();
    }
}
