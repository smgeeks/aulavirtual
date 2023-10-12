<?php

namespace SolidAffiliate\Views\AffiliatePortal;

use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Lib\License;
use SolidAffiliate\Lib\Links;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Lib\VO\AffiliatePortal\AffiliatePortalRegistrationViewInterface;
use SolidAffiliate\Views\Shared\AdminHeader;

class RegistrationView
{

    const FORM_ID = 'solid-affiliate-affiliate-portal_new_affiliate';

    /**
     * @param AffiliatePortalRegistrationViewInterface $Iregistration
     *
     * @return string
     */
    public static function render($Iregistration)
    {
        // get the current user's email address if they are logged in
        $maybe_email = (string)$Iregistration->form_values['user_email']; 
        if (!empty($maybe_email) && get_user_by('email', $maybe_email)) {
            $already_logged_in_notice = '<div class="sld-ap-form_notice info">
                            <div class="sld-ap-form_notice_icon">
                    <svg width="16" height="11" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.1641 4.72266C11.8125 5.07422 11.3789 5.25 10.8633 5.25C10.3477 5.25 9.90234 5.07422 9.52734 4.72266C9.17578 4.34766 9 3.90234 9 3.38672C9 2.87109 9.17578 2.42578 9.52734 2.05078C9.90234 1.67578 10.3477 1.48828 10.8633 1.48828C11.3789 1.48828 11.8125 1.67578 12.1641 2.05078C12.5391 2.42578 12.7266 2.87109 12.7266 3.38672C12.7266 3.90234 12.5391 4.34766 12.1641 4.72266ZM6.82031 3.84375C6.39844 4.28906 5.87109 4.51172 5.23828 4.51172C4.62891 4.51172 4.10156 4.28906 3.65625 3.84375C3.21094 3.39844 2.98828 2.87109 2.98828 2.26172C2.98828 1.62891 3.21094 1.10156 3.65625 0.679688C4.10156 0.234375 4.62891 0.0117188 5.23828 0.0117188C5.87109 0.0117188 6.39844 0.234375 6.82031 0.679688C7.26562 1.10156 7.48828 1.62891 7.48828 2.26172C7.48828 2.87109 7.26562 3.39844 6.82031 3.84375ZM8.15625 7.32422C9.11719 6.94922 10.0195 6.76172 10.8633 6.76172C11.7305 6.76172 12.6328 6.94922 13.5703 7.32422C14.5312 7.69922 15.0117 8.19141 15.0117 8.80078V10.4883H6.75V8.80078C6.75 8.19141 7.21875 7.69922 8.15625 7.32422ZM5.23828 5.98828C5.75391 5.98828 6.35156 6.05859 7.03125 6.19922C5.83594 6.85547 5.23828 7.72266 5.23828 8.80078V10.4883H0V8.625C0 8.08594 0.316406 7.60547 0.949219 7.18359C1.60547 6.76172 2.32031 6.45703 3.09375 6.26953C3.89062 6.08203 4.60547 5.98828 5.23828 5.98828Z" />
                    </svg>
                </div>
                
            ' . sprintf(__( 'You are currently logged in as %1$s.', 'solid-affiliate'), $maybe_email) . '</div>';
        } else {
            $already_logged_in_notice = '';
        }
        

        $maybe_deactivated_component = AdminHeader::render_solid_affiliate_is_deactivated_component();
        if (!empty($maybe_deactivated_component) && !License::is_on_keyless_free_trial()) {
            return $maybe_deactivated_component;
        }

        $affiliate_word = Validators::str(Settings::get(Settings::KEY_AFFILIATE_WORD_ON_PORTAL_FORMS));
        $register_cta = __('Register as', 'solid-affiliate') . ' ' . $affiliate_word;

        // get wordpress login url/link
        $login_link = Links::render(wp_login_url(), __('Login', 'solid-affiliate'));
        // but translated. the interior of the div should be a translated string using __() and sprintf()
        $login_error_html = "<div id='sld-user_login-error' class='sld-ap-form_error'>" . sprintf(__('An account with this username aready exists. Please %1$s first if it is your account.', 'solid-affiliate'), $login_link) . "</div>";
        $email_error_html = "<div id='sld-user_email-error' class='sld-ap-form_error'>" . sprintf(__('An account with this email aready exists. Please %1$s first if it is your account.', 'solid-affiliate'), $login_link) . "</div>";


        ob_start();
?>
        <?php echo (LoginView::render_css()) ?>
        <script>
            // make an ajax request to generate a new nonce and then replace the value of the nonce field in the form
            jQuery(document).ready(function($) {
                var data = {
                    'action': 'sld_affiliate_generate_registration_nonce',
                };

               jQuery.post(window.SolidAffiliate.ajaxurl, data, function(response) {
                    if (response.success) {
                       jQuery('#<?php echo self::FORM_ID ?> #_wpnonce').val(response.data);
                    }
                });

                /////////////////////////////////////////////////
                // Function to check if a field is already in use
                function checkIfFieldIsAlreadyInUse(field, value) {
                    return jQuery.ajax({
                        url: window.SolidAffiliate.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'sld_affiliate_check_if_field_is_already_in_use',
                            field: field,
                            value: value,
                        }
                    });
                }

                // Add an event handler to do something whenever the name="user_email" or "user_login" field is changed.
               jQuery('#<?php echo self::FORM_ID ?> input[name="user_email"], #<?php echo self::FORM_ID ?> input[name="user_login"]').on('change', function() {
                    let $this = jQuery(this);
                    let newValue = $this.val();
                    let field = $this.attr('name') === 'user_email' ? 'email' : 'login';
                    let errorId = 'sld-user_' + field + '-error';

                    // check if the field is already in use
                    checkIfFieldIsAlreadyInUse(field, newValue).done(function(response) {
                        if (response.success) {
                            jQuery('#<?php echo self::FORM_ID ?> #' + errorId).remove();
                        } else {
                            if (jQuery('#<?php echo self::FORM_ID ?> #' + errorId).length) {
                                return;
                            }
                            $this.after(field === 'email' ? <?php echo json_encode($email_error_html) ?> : <?php echo json_encode($login_error_html) ?>);
                        }
                    }).fail(function(error) {
                        jQuery('#<?php echo self::FORM_ID ?> #' + errorId).remove();
                    });
                });

            });
        </script>

        <div class="sld-col-1 sld-ap-form_box sld-ap-registration-component">
            <?php echo($already_logged_in_notice) ?>
            <?php echo ($Iregistration->notices) ?>

            <h2><?php echo ($register_cta) ?></h2>

            <form action="" method="post" class="sld-ap-form" id="<?php echo self::FORM_ID ?>">
                <?php echo (FormBuilder::build_form(
                    $Iregistration->schema,
                    'non_admin_new',
                    (object)$Iregistration->form_values,
                    false,
                    false,
                    false
                )); ?>
                <?php if ((bool)Settings::get(Settings::KEY_IS_RECAPTCHA_ENABLED_FOR_AFFILIATE_REGISTRATION)) { ?>
                    <?php echo FormBuilder::render_solid_g_recaptcha_v2(); ?>
                    <br />
                <?php } ?>
                <?php wp_nonce_field($Iregistration->form_nonce) ?>
                <input type="submit" name="<?php echo ($Iregistration->submit_action) ?>" id="<?php echo ($Iregistration->submit_action) ?>" class="sld-ap-form_submit" value="<?php echo ($register_cta) ?>">
            </form>

            <div class="sld-ap-form_notice">
                <div class="sld-ap-form_notice_icon">
                    <svg width="16" height="11" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.1641 4.72266C11.8125 5.07422 11.3789 5.25 10.8633 5.25C10.3477 5.25 9.90234 5.07422 9.52734 4.72266C9.17578 4.34766 9 3.90234 9 3.38672C9 2.87109 9.17578 2.42578 9.52734 2.05078C9.90234 1.67578 10.3477 1.48828 10.8633 1.48828C11.3789 1.48828 11.8125 1.67578 12.1641 2.05078C12.5391 2.42578 12.7266 2.87109 12.7266 3.38672C12.7266 3.90234 12.5391 4.34766 12.1641 4.72266ZM6.82031 3.84375C6.39844 4.28906 5.87109 4.51172 5.23828 4.51172C4.62891 4.51172 4.10156 4.28906 3.65625 3.84375C3.21094 3.39844 2.98828 2.87109 2.98828 2.26172C2.98828 1.62891 3.21094 1.10156 3.65625 0.679688C4.10156 0.234375 4.62891 0.0117188 5.23828 0.0117188C5.87109 0.0117188 6.39844 0.234375 6.82031 0.679688C7.26562 1.10156 7.48828 1.62891 7.48828 2.26172C7.48828 2.87109 7.26562 3.39844 6.82031 3.84375ZM8.15625 7.32422C9.11719 6.94922 10.0195 6.76172 10.8633 6.76172C11.7305 6.76172 12.6328 6.94922 13.5703 7.32422C14.5312 7.69922 15.0117 8.19141 15.0117 8.80078V10.4883H6.75V8.80078C6.75 8.19141 7.21875 7.69922 8.15625 7.32422ZM5.23828 5.98828C5.75391 5.98828 6.35156 6.05859 7.03125 6.19922C5.83594 6.85547 5.23828 7.72266 5.23828 8.80078V10.4883H0V8.625C0 8.08594 0.316406 7.60547 0.949219 7.18359C1.60547 6.76172 2.32031 6.45703 3.09375 6.26953C3.89062 6.08203 4.60547 5.98828 5.23828 5.98828Z" />
                    </svg>
                </div>
                <?php if ($Iregistration->affiliate_approval_required) { ?>
                    <div class="sld-ap-form_notice_text">
                        <?php _e("Your account will need to be approved before you can earn Referrals. You'll receive an email once it's approved.", 'solid-affiliate') ?>
                    </div>
                <?php } else { ?>
                    <div class="sld-ap-form_notice_text">
                        <?php _e('Your account will be automatically Approved, and you can start earning Referrals immediately.', 'solid-affiliate') ?>
                    </div>
                <?php }; ?>
            </div>
        </div>

<?php
        return ob_get_clean();
    }
}
