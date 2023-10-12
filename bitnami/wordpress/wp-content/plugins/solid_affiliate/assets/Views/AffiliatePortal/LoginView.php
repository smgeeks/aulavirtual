<?php

namespace SolidAffiliate\Views\AffiliatePortal;

/**
 * shortcodes.affiliate_portal_login view.
 * WordPress MVC view.
 *
 * @author Mike Holubowski <https://www.github.com/mholubowski>
 * @package solid-affiliate
 * @version 1.0.0
 */

use SolidAffiliate\Controllers\AffiliatePortalController;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Lib\Validators;

# TODO: Decide how we want to do form name scoping if we want to persist the email on a failed submit.
#       Validators::LoginCredentials forces the keys of the params to be user_pass and user_email.
class LoginView
{

    /**
     * @return string
     */
    public static function render()
    {
        $form_id = 'solid-affiliate-affiliate-portal_login-form';
        $nonce = AffiliatePortalController::NONCE_SUBMIT_AFFILIATE_LOGIN;
        $submit_action = AffiliatePortalController::POST_PARAM_SUBMIT_AFFILIATE_LOGIN;

        $affiliate_word = Validators::str(Settings::get(Settings::KEY_AFFILIATE_WORD_ON_PORTAL_FORMS));
        $login_cta = __('Login as', 'solid-affiliate') . ' ' . $affiliate_word;

        ob_start();
?>
        <?php echo (self::render_css()) ?>
        <script>
            // make an ajax request to generate a new nonce and then replace the value of the nonce field in the form
            jQuery(document).ready(function($) {
                var data = {
                    'action': 'sld_affiliate_generate_login_nonce',
                };

                $.post(window.SolidAffiliate.ajaxurl, data, function(response) {
                    if (response.success) {
                        $('#<?php echo $form_id ?> #_wpnonce').val(response.data);
                    }
                });
            });
        </script>

        <div class="sld-col-2 sld-ap-form_box sld-ap-login-component">
            <?php if ($submit_action == Validators::str_from_array($_GET, 'submit_action')) { ?>
                <?php echo (AffiliatePortalController::affiliate_portal_notices()) ?>
            <?php } ?>

            <h2><?php echo ($login_cta) ?></h2>
            <form action="" method="post" class="sld-ap-form" id="<?php echo $form_id ?>">
                <div class="sld-ap-form_group">
                    <label for="user_email"><?php _e('Account email', 'solid-affiliate') ?></label>
                    <input type="email" id="sld-ap-login-email" name="user_email" required>
                </div>
                <div class="sld-ap-form_group">
                    <label for="user_pass"><?php _e('Password', 'solid-affiliate') ?></label>
                    <input type="password" id="sld-ap-login-pass" name="user_pass" required>
                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-pass"><?php _e('Forgot your password?', 'solid-affiliate') ?></a>
                </div>
                <input type="hidden" name="field_id" value="0">

                <?php wp_nonce_field($nonce); ?>
                <!-- TODO: Was there a reason that 'id' was set to the string that $submit_action is, but does not use the $submit_action variable? -->
                <input type="submit" name="<?php echo ($submit_action) ?>" id="<?php echo ($submit_action) ?>" class="sld-ap-form_login" value="<?php echo ($login_cta) ?>">
            </form>
        </div>

    <?php
        return ob_get_clean();
    }




    /**
     * Undocumented function
     *
     * @return string
     */
    public static function render_css()
    {

        ob_start();
    ?>

        <style>
            :root {
                /* Global container color */
                --sld-ap-background: #fff;
                /* Shading background : Used for hovers and tables */
                --sld-ap-shading: #fafafa;

                /* Color scheme */
                --sld-ap-primary-color: #474747;
                --sld-ap-secondary-color: #757575;
                --sld-ap-accent-color: #4285F4;
                --sld-ap-border-color: #DADCE0;
                --sld-ap-border-radius: 10px;
                /* --sld-ap-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"; */
                --sld-ap-font-family: inherit;


                /* Font sizes */
                --sld-ap-font-size-xs: 12px;
                --sld-ap-font-size-m: 14px;
                --sld-ap-font-size-l: 20px;
                --sld-ap-font-size-xl: 22px;
            }

            /********************

Affiliate Portal CSS

*********************/


            /* Global layout/grid  */


            .sld-ap {
                display: flex;
                font-size: 1rem;
                font-family: var(--sld-ap-font-family);
                gap: 20px;
            }


            .sld-ap h2 {
                font-family: var(--sld-ap-font-family);
                font-size: var(--sld-ap-font-size-xl);
                margin-bottom: 20px;
                font-weight: 400;
            }


            .sld-col-1 {
                flex-shrink: 0;
                flex-basis: 60%;
            }

            .sld-col-1:last-child {
                flex-shrink: 0;
                flex-basis: fit-content;
            }

            @media only screen and (max-width: 760px) {


                .sld-ap {
                    flex-direction: column;
                }

            }

            .sld-ap-form {
                display:flex;
                flex-direction: column;
            }

            .sld-ap-form_box {
                padding: 20px;
                width: 100%;
                border: 1px solid var(--sld-ap-border-color);
                border-radius: 5px;
            }

            .sld-ap-form_group {
                width: 100%;
                margin: 15px 0;
                display: flex;
                flex-direction: column;
            }

            .sld-ap-form_group>label {
                margin: 5px 0;
                font-size: var(--sld-ap-font-size-m);
                color: var(--sld-ap-secondary-color);
                display:flex;
                flex-direction: column;
            }

            .sld-ap-form_group>label input,
            .sld-ap-form_group>label textarea,
            .sld-ap-form_group>label select
            {
                width:100%;
            }
            .sld-ap-form_checkbox input {
                width:auto !important;
            }

            .sld-ap-form_group input,
            .sld-ap-form_group textarea,
            .sld-ap-form_group select {
                display: block;
                border: 1px solid var(--sld-ap-border-color);
                padding: 10px 5px;
                border-radius: 3px;
                background: #fafafd;
            }


            .sld-ap-form_checkbox {
                display: inline-block !important;
                color: var(--sld-ap-primary-color) !important;
                font-size: var(--sld-ap-font-size-m) !important;
            }

            .sld-ap-form_checkbox>input {
                width: 25px;
                display: inline-block;
            }

            label.sld_field-label input[type='radio'] {
                width: 25px;
                border-radius: 50%;
                display: inline-block;
            }

            .sld-ap-form_checkbox a {
                color: blue;
            }

            .sld-ap-form_submit {
                background: var(--sld-ap-accent-color);
                font-size: var(--sld-ap-font-size-m);
                color: #fff;
                padding: 15px 20px;
                border: none !important;
                border-radius: 5px
            }

            .sld-ap-form_group input:focus,
            .sld-ap-form_group input:focus-within,
            .sld-ap-form_group input:focus-visible {
                border: 1px solid var(--sld-ap-accent-color);
                outline: none !important;
                background: #fff;
            }

            .sld-ap-form_tip {
                font-size: var(--sld-ap-font-size-xs);
                color: var(--sld-ap-primary-color);
            }

            .sld-ap-form_notice {
                display: flex;
                gap: 5px;
                margin: 20px 0;
                border: 1px solid var(--sld-ap-border-color);
                color: var(--sld-ap-primary-color);
                line-height: 1.2;
                font-size: var(--sld-ap-font-size-m);
                border-radius: 5px;
                padding: 10px;
                align-items: center;
            }

            .sld-ap-form_notice_icon svg {
                fill: var(--sld-ap-secondary-color);
                width: auto;
                height: 20px;
            }


            .sld-ap-form_notice.error svg {
                fill: darkred;
            }

            .sld-ap-form_notice.error {
                color: darkred;
                background: rgb(255, 248, 249);
            }

            .sld-ap-form_notice.success svg {
                fill: darkgreen;
            }

            .sld-ap-form_notice.success {
                color: darkgreen;
                background: #e0ffe0;
            }

            .sld-ap-form_notice.info svg {
                fill: darkblue;
            }

            .sld-ap-form_notice.info {
                color: darkblue;
                background: #fbfeff;
            }

            .sld-ap-form_group a.forgot-pass {
                font-size: var(--sld-ap-font-size-xs);
                color: blue;
            }


            .sld-ap-form_login {
                background: white;
                font-size: var(--sld-ap-font-size-m);
                color: var(--sld-ap-accent-color);
                border: 1px solid var(--sld-ap-accent-color);
                padding: 15px 20px;
                border-radius: 5px
            }

            .sld-tooltip>svg {
                max-width: 15px;
            }

            /* label with for="is_accept_affiliate_policy"  */
            label[for="is_accept_affiliate_policy"] p.sld_field-description {
                display: inline-block;
            }


            #is_accept_affiliate_policy+p::before {
                content: '* ';
                color: red;
            }

            .sld-ap-login-component {
                height: fit-content;
            }

            /* Stuff Mike added while making "v2" registration form */
            .sld-ap-form_error {
                color: red;
                font-size: var(--sld-ap-font-size-xs);
                margin: 5px 0;
            }
        </style>

<?php
        return ob_get_clean();
    }
}
