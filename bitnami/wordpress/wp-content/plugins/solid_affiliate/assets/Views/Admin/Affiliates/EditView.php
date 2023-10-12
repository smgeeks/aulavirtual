<?php

namespace SolidAffiliate\Views\Admin\Affiliates;

use SolidAffiliate\Controllers\AffiliatesController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Lib\URLs;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Models\Affiliate;
use SolidAffiliate\Models\AffiliatePortal;

class EditView
{
    const AFFILIATE_EDIT_AFTER_FORM_FILTER = 'solid_affiliate/affiliates/edit/after_form';

    /**
     * @param object $item
     * @return string
     */
    public static function render($item)
    {
        $singular = __('Affiliate', 'solid-affiliate');
        $form_id = 'affiliates-edit';
        $schema = Affiliate::schema_with_custom_registration_data();
        $nonce = AffiliatesController::NONCE_SUBMIT_AFFILIATE;
        $submit_action = AffiliatesController::POST_PARAM_SUBMIT_AFFILIATE;


        $wp_user = get_userdata((int)$item->user_id);
        $form = FormBuilder::render_crud_form_edit($schema, $submit_action, $nonce, $form_id, $singular, $item);
        $affiliate = Affiliate::find((int)$item->id);

        ob_start();
?>
        <div class="wrap">
            <h1><?php echo sprintf(__('Update %1$s', 'solid-affiliate'), $singular); ?></h1>
            <p>Use the forms below to update your affiliate account information, adjust their store credit balance, assign custom slugs and more.</p>
            <div class='sld-flex-wrap'>
                <div class='major-panel'>
                    <?php echo AffiliatePortal::render_affiliate_portal_preview_section_on_affiliate_edit($affiliate); ?>

                    <div class="edit-affiliate-navigation">
                        <ul>
                            <!-- This is handled dynamically by JS now on page load -->
                            <!-- <li><a href="#edit-affiliate-account_details">Account details</a></li>
                        <li><a href="#edit-affiliate-landing_pages">Affiliate Landing Pages</a></li> -->
                        </ul>
                    </div>

                </div>

                <div class='minor-panel'>
                    <div class="sld-card medium setting">
                            <h2 id="edit-affiliate-account_details">Account details</h2>
                        <div class='edit-affiliate-shortinfo'>

                            <?php
                            if ($wp_user) {
                                echo self::render_user_information($wp_user);
                            }
                            if ($affiliate instanceof Affiliate) {
                                echo self::_render_default_affiliate_link($affiliate);
                            }
                            ?>
                        </div>
                        <?php echo $form ?>
                        <?php
                        echo self::render_per_affiliate_per_product_commissions_component((int)$item->id);
                        ?>
                    </div>
                    <?php
                    $data_panels = Validators::array_of_string(apply_filters(self::AFFILIATE_EDIT_AFTER_FORM_FILTER, [], $affiliate));
                    foreach ($data_panels as $panel) {
                    ?>
                        <div class='sld-card medium setting sld-affiliate-edit-data-panel'>
                            <?php
                            echo $panel;
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     *
     * @param \WP_User $wp_user
     * 
     * @return string
     */
    public static function render_user_information($wp_user)
    {
        ob_start();
    ?>
        <div class="edit-affiliate-shortinfo_card">
            <p>
                <strong><?php _e('Information of the WordPress User associated with this Affiliate account', 'solid-affiliate') ?></strong>
            <ul>
                <li><strong><?php _e('Email', 'solid-affiliate') ?>:</strong> <?php echo ($wp_user->user_email) ?></li>
                <li><strong><?php _e('Username', 'solid-affiliate') ?>:</strong> <?php echo ($wp_user->user_login) ?></li>
                <li><strong><?php _e('Name', 'solid-affiliate') ?>:</strong> <?php echo ($wp_user->first_name . ' ' . $wp_user->last_name) ?></li>
                <li><strong><?php _e('Profile', 'solid-affiliate') ?>:</strong>
                    <?php echo sprintf('<a href="%s" data-id="%d" title="%s">%s</a>', get_edit_user_link($wp_user->ID), $wp_user->ID, __('Edit User', 'solid-affiliate'), __('Edit User', 'solid-affiliate')); ?>
                </li>
            </ul>
            </p>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     *
     * @param Affiliate $affiliate
     *
     * @return string
     */
    private static function _render_default_affiliate_link($affiliate)
    {
        ob_start();
    ?>
        <div class="edit-affiliate-shortinfo_card">
            <p>
                <strong><?php _e('Default Affiliate Link for this Affiliate', 'solid-affiliate') ?></strong>
            </p>
            <code><?php echo URLs::default_affiliate_link($affiliate) ?></code>
            <p>
                <?php _e('The affiliate will see this link as their default link in their Affiliate Portal.', 'solid-affiliate') ?>
                <?php _e('This is configured by the Referral Variable, Default Affiliate Link URL, and Affiliate Slug Display Format settings.', 'solid-affiliate') ?>
                <?php _e('In addition to this default link, an Affiliate can add their custom slugs or ID to any URL to create an Affiliate Link.', 'solid-affiliate') ?>
            </p>
        </div>
    <?php
        return ob_get_clean();
    }


    /**
     *
     * @param int $affiliate_id
     * 
     * @return string
     */
    public static function render_per_affiliate_per_product_commissions_component($affiliate_id)
    {
        $affiliate = Affiliate::find($affiliate_id);

        if (!($affiliate instanceof Affiliate)) {
            return sprintf(__('No Affiliate with ID %1$s found.', 'solid-affiliate'), $affiliate_id);
        }
        ob_start();
    ?>
<?php
        return ob_get_clean();
    }
}
