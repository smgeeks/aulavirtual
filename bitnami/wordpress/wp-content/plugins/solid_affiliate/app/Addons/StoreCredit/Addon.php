<?php

namespace SolidAffiliate\Addons\StoreCredit;

use SolidAffiliate\Addons\AddonInterface;
use SolidAffiliate\Addons\Core;
use SolidAffiliate\Controllers\PayAffiliatesController;
use SolidAffiliate\Lib\Action_Scheduler;
use SolidAffiliate\Lib\AjaxHandler;
use SolidAffiliate\Lib\ControllerFunctions;
use SolidAffiliate\Lib\Email_Notifications;
use SolidAffiliate\Lib\Formatters;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Lib\GlobalTypes;
use SolidAffiliate\Lib\Links;
use SolidAffiliate\Lib\RandomData;
use SolidAffiliate\Lib\Templates;
use SolidAffiliate\Lib\Translation;
use SolidAffiliate\Lib\URLs;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Lib\VO\AddonDescription;
use SolidAffiliate\Lib\VO\AffiliatePortalViewInterface;
use SolidAffiliate\Lib\VO\Either;
use SolidAffiliate\Lib\VO\ListTableConfigs;
use SolidAffiliate\Lib\VO\RouteDescription;
use SolidAffiliate\Lib\VO\Schema;
use SolidAffiliate\Lib\VO\SchemaEntry;
use SolidAffiliate\Models\Affiliate;
use SolidAffiliate\Models\Payout;
use SolidAffiliate\Models\Referral;
use SolidAffiliate\Models\StoreCreditTransaction;
use SolidAffiliate\Views\Admin\Affiliates\EditView;
use SolidAffiliate\Views\AffiliatePortal\AffiliatePortalTabsView;
use SolidAffiliate\Views\AffiliatePortal\DashboardView;
use SolidAffiliate\Views\Shared\AdminHeader;
use SolidAffiliate\Views\Shared\AdminTabsView;
use SolidAffiliate\Views\Shared\AjaxButton;
use SolidAffiliate\Views\Shared\CardView;
use SolidAffiliate\Views\Shared\SimpleTableView;
use SolidAffiliate\Views\Shared\SolidModalView;
use SolidAffiliate\Views\Shared\WPListTableView;
use WC_Coupon;

/**
 * Store Credit Addon
 * 
 * Gameplan:
 *  [x] Find the users in helpscout who have asked for this.
 *  [x] email them with video, that we are working on this, and if they want to give us their guidance.
 *  [] can also ask for sponsor/money/custom development to get his moving faster and we'll make it to their specifications.
 * 
 * Core Functionality:
 *  [x] You can add/remove store credit to an affiliate manually via admin Affiliate > Edit.
 *  [x] You can use the Pay Affiliates tool to pay affiliates with store credit. (payout method = Store Credit)
 *    [x] A Referral can be paid for with store credit via the Pay Affiliates tool. (payout method = Store Credit)
 *    [x] Issuing bulk store credit via the pay affiliates sends all the affiliates a notification email. (should this be for all types of Payouts? Even manual payouts, admin can choose to send email?)
 *  [x] You can see all current/total store credit for an affiliate(s), and totals.
 *  [x] You can see a list of all the store credit transactions in the admin panel. Store credit rewards + store credit purchases.
 *    [x] Store Credit "rewards": for each store credit addition, the date it was added, and the reason it was added.
 *    [x] Store Credit "purchases": for each store credit purchase, the date it was used to make a purchase and for which order, which products etc.
 *      [x] Store Credit Transaction - look at the admin table and make sure it makes sense. Things like source_id should be a link (?)
 *  [x] Affiliate Portal: An affiliate can see their current store credit balance.
 *  [x] "Total Redeemed" Store Credit needs to be figured out. Currently, it will count towards redeemed if an admin manually reducts the store credit from an affiliate. This is confusing.
 * 
 *  [x] Store Credit should be shown in admin Affiliates > list table
 * 
 *  [] Store Credit can actually be used by an affiliate
 *    [x] Store Credit can be used as part of a standard WooCommerce purchase at checkout.
 *    [x] Changing the cart while store credit is already applied, update the store credit used.
 *    [x] Updating the cart all the way to 0 store credit is already applied, update the store credit used and delete the coupon
 *    [x] Store Credit option is also seen on the /cart page
 *    [x] Store Credit button does some visual stuff when it is clicked. 
 *    [] different copy/button when store credit is already applied.
 *    [x] WooCommerce orders show the store credit used in the order. Currently does this via coupon
 *    [x] Deleting the cart? ()
 *   
 *  [] There should be some settings where they can customize the store credit system.
 *    [] Do you want additional % of the commission to be store credit, to incentivize? ($20 for cash vs. $25 for store credit)
 *    [] Opt-in/opt-out setting for affiliate's (affiliate can opt-out of store credit). + meta-setting to enable this setting in affiliate portal.
 * 
 *  [] V2: Store Credit can be used for Subscription renewals.
 *  [x] Affiliates get an email when they earn store credit.
 *  [] V2: (potentially) Make store credit more valuable than cash payout. (to incentivize store credit as an option)
 *  [] V2: (potentially) Allow affiliates to request store credit vs cash.
 * 
 *  ==================
 *  Misc.
 *  ==================
 *  [] Store Credit Transactions data integrity
 *    [] created_by_user_id only makes sense for 'manual' and 'payout' transactions.
 *    [] source_id is different for each source type. Need to think about this
 *  [x] Referrals and Payouts admin tables should indicate if the referral / payout was paid with store credit.
 *  [] Renovate the Setup Wizard to incorporate the new Store Credit system.
 *  [x] need to fix the Pay Affiliates > Store Credit success message (see PayAffiliatesController:415 its hitting the paypal message)
 *  [x] need to make PayAffiliatesFunctions::handle_store_credit_payout much more robust
 *  [x] need to write *a lot* of store credit tests around the Pay Affiliates system. gg
 *  [] need to fix the form validation issue: https://wordpress.org/support/topic/trigger-update_checkout-without-triggering-form-validation/
 *  [] automatically clean up unused/expired store credit coupons
 *  [] figure out and test for total vs subtotal w/ taxes and fees and shipping etc: https://stackoverflow.com/questions/33919859/woocommerce-whats-the-difference-between-cart-total-and-subtotal
 *  [] need to figure out admin.js + solid-shared.js + AjaxButton.php
 * 
 *  ==================
 *  Customer suggested features
 *  ==================
 *  [] Store Credit can be used for Subscription renewals.
 *  [] Store Credit can be seen by customers from their WooCommerce 'my account' dashboard.
 * 
 *  ================== 
 *  Related work
 *  ================== 
 *  [] Improve the AjaxButton + the shared JS etc.
 *  [] Pay Affiliates improvements:
 *    [] ability to pay individual affiliate
 *    [] ability to pay individual referral(s)
 *    [] ability to pay some affiliates/referrals in store credit, while others in cash.
 *  [] Refer a friend:
 *    [] ability to refer a friend, get instant credit. Ayman pointed to referralcandy.com
 *    [] The refer a friend system is an entirely different system than the affiliate system.
 * 
 * ==================
 * Prior Art
 * ==================
 * [] https://apps.shopify.com/my-store-credit (nice dashaboards; "reason" for store credit transaction; "adjust store credit" form is nice; "store credit widget" with draggable is nice)
 * [] If you want to see other ideas : https://docs.uppromote.com/set-up/payment-setup/store-credit
 * 
 * 
 */

/**
 * @psalm-import-type StoreCreditDataType from \SolidAffiliate\Lib\VO\AffiliatePortalViewInterface
 */
class Addon implements AddonInterface
{
    const ADDON_SLUG = 'store-credit';
    const DOCS_URL = 'https://docs.solidaffiliate.com/store-credit/';
    const ADMIN_PAGE_KEY = 'solid-affiliate-store-credit';
    const DEFAULT_REQUIRED_CAPABILITY = 'read';
    const MENU_TITLE = 'Store Credit';
    const AFFILIATE_PORTAL_TAB_KEY = 'store-credit';

    const NONCE_ADJUST_STORE_CREDIT = 'solid-affiliate-adjust-store-credit';
    const POST_PARAM_ADJUST_STORE_CREDIT = 'solid-affiliate-adjust-store-credit';

    const META_KEY_IS_COUPON_STORE_CREDIT = 'solid_affiliate_is_store_credit_coupon';

    /**
     * This is the function which gets called when the Addon is loaded.
     * This is the entry point for the Addon.
     *
     * Register your Addon by using the "solid_affiliate/addons/addon_descriptions" filter.
     *
     * Then check if your Addon is enabled, and if so do your stuff.
     *
     * @return void
     */
    public static function register_hooks()
    {
        add_filter("solid_affiliate/addons/addon_descriptions", [self::class, "register_addon_description"]);
        add_action("solid_affiliate/addons/before_settings_form/" . self::ADDON_SLUG, [self::class, "settings_message"]);
    }

    /**
     * This is the function which includes a call to Core::is_addon_enabled() to check if the addon is enabled.
     * 
     * Do not put anything in the register_hooks above besides add_filter and add_action calls. 
     *
     * @return void
     */
    public static function register_if_enabled_hooks()
    {
        if (Core::is_addon_enabled(self::ADDON_SLUG)) {
            add_action("solid_affiliate/admin/submenu_pages/after", [self::class, "add_page_to_submenu"]);
            add_filter("solid_affiliate/PostRequestController/routes", [self::class, "register_routes"]);
            add_filter(EditView::AFFILIATE_EDIT_AFTER_FORM_FILTER, [self::class, 'render_affiliate_edit_store_credit_section'], 10, 2);

            // Affiliate Portal
            add_filter(AffiliatePortalTabsView::AFFILIATE_PORTAL_TABS_FILTER, [self::class, 'add_store_credit_tab_to_affiliate_portal']);
            add_filter(AffiliatePortalTabsView::AFFILIATE_PORTAL_TAB_ICON_FILTER, [self::class, 'add_store_credit_icon_to_affiliate_portal'], 10, 2);
            add_filter(DashboardView::AFFILIATE_PORTAL_RENDER_TAB_ACTION, [self::class, 'maybe_render_store_credit_tab_on_affiliate_portal'], 10, 1);

            // Affiliates Admin List Table
            add_filter('solid_affiliate/admin_list_table_configs/Affiliate', [self::class, 'add_store_credit_column_to_affiliates_admin_list_table']);

            // Redeeming store credit through WooCommerce checkout
            add_action('woocommerce_removed_coupon',                        [self::class, 'handle_removed_coupon']);
            add_action('woocommerce_cart_loaded_from_session',            [self::class, 'handle_cart_loaded_from_session']);
            add_action('woocommerce_cart_contents', [self::class, 'add_store_credit_checkout_notice']);
            add_action('woocommerce_before_checkout_form', [self::class, 'add_store_credit_checkout_notice']);
            add_action('woocommerce_checkout_order_processed', [self::class, 'handle_woocommerce_checkout_order_processed'], 10, 2);
            add_filter('woocommerce_cart_totals_coupon_label', [self::class, 'filter_woocommerce_cart_totals_coupon_label'], 10, 2);
            add_filter('woocommerce_coupon_message', [self::class, 'filter_woocommerce_coupon_message'], 10, 3);
        }
    }

    ////////////// start - woo checkout hooks


    /**
     * I believe this happens on every page load where a cart is present.
     * 
     * @param \WC_Cart $cart
     *
     * @return void
     */
    public static function handle_cart_loaded_from_session($cart)
    {
        $coupon = self::store_credit_coupon_from_cart($cart);
        if ($coupon instanceof \WC_Coupon) {

            // If the cart is empty, remove the coupon.
            $cart_total = self::get_cart_applicable_amount($cart);
            if ($cart_total <= 0.0) {
                $cart->remove_coupon($coupon->get_code());
                $coupon->delete(true);
            }

            $maybe_affiliate = Affiliate::current();
            if (is_null($maybe_affiliate)) {
                return;
            }

            // If the cart total is not equal to the amount
            // check to see if we should update the coupon.
            if ($cart_total != $coupon->get_amount() || ($coupon->get_amount() > self::outstanding_store_credit_for_affiliate($maybe_affiliate->id))) {
                // recalculate the store credit amount and update the coupon
                $new_store_credit_amount = self::amount_of_store_credit_to_apply($cart, $maybe_affiliate->id);
                if ($new_store_credit_amount <= 0.0) {
                    $cart->remove_coupon($coupon->get_code());
                    $coupon->delete(true);
                } else {
                    $coupon->set_amount($new_store_credit_amount);
                    $coupon->save();
                }
            }
        }
    }

    /**
     * @param string $coupon_code
     * @return void
     */
    public static function handle_removed_coupon($coupon_code)
    {
        $wc_coupon = new WC_Coupon($coupon_code);
        if (self::is_coupon_store_credit($wc_coupon)) {
            $wc_coupon->delete(true);
            wc_add_notice(__('Store credit removed from cart.', 'solid-affiliate'), 'notice');
        }
    }


    /**
     * @param int $order_id
     * @param object $data
     * 
     * @return void
     */
    public static function handle_woocommerce_checkout_order_processed($order_id, $data)
    {
        self::deduct_store_credit_for_order($order_id);
    }

    /**
     * @param int $order_id
     * @return Either<StoreCreditTransaction>
     */
    public static function deduct_store_credit_for_order($order_id)
    {
        /** @var StoreCreditTransaction $null_transaction */
        $null_transaction = null;
        $order = new \WC_Order($order_id);
        if (!($order instanceof \WC_Order)) {
            return new Either(['Order not found.'], $null_transaction, false);
        }

        $user_id = $order->get_user_id();
        $maybe_affiliate = Affiliate::find_where(['user_id' => $user_id]);
        if (is_null($maybe_affiliate)) {
            return new Either(['Affiliate not found.'], $null_transaction, false);
        }

        $maybe_existing_transaction = StoreCreditTransaction::find_where(['order_id' => $order_id]);
        if (!is_null($maybe_existing_transaction)) {
            return new Either(['Transaction already exists.'], $null_transaction, false);
        }

        // Grab an array of coupons used
        if (version_compare(WC()->version, '3.7.0', '>=')) {
            $coupon_codes = Validators::array_of_string($order->get_coupon_codes());
        } else {
            /** * @psalm-suppress DeprecatedMethod */
            $coupon_codes = Validators::array_of_string($order->get_used_coupons());
        }

        foreach ($coupon_codes as $coupon_code) {
            $wc_coupon = new WC_Coupon($coupon_code);
            if (self::is_coupon_store_credit($wc_coupon)) {
                $affiliate = Affiliate::current();
                if (is_null($affiliate)) {
                    continue;
                }

                /////////////////////////////////////////////
                // deduct the credit and create a transaction
                $amount = (float)$wc_coupon->get_amount();
                return self::remove_store_credit_from_affiliate($affiliate->id, $amount, $order, __("Store Credit used towards Order #", 'solid-affiliate') . $order_id);
            }
        }

        return new Either(['No store credit coupons found.'], $null_transaction, false);
    }

    /**
     * @param string $coupon_label
     * @param \WC_Coupon $wc_coupon
     * 
     * @return string
     */
    public static function filter_woocommerce_cart_totals_coupon_label($coupon_label, $wc_coupon)
    {
        if (self::is_coupon_store_credit($wc_coupon)) {
            return __('Store Credit', 'solid-affiliate');
        } else {
            return $coupon_label;
        }
    }

    /**
     * @param string $msg
     * @param int $msg_code
     * @param \WC_Coupon $wc_coupon
     * 
     * @return string
     */
    public static function filter_woocommerce_coupon_message($msg, $msg_code, $wc_coupon)
    {
        if (self::is_coupon_store_credit($wc_coupon)) {
            switch ($msg_code) {
                case WC_Coupon::WC_COUPON_SUCCESS:
                    return __('Store credit applied successfully.', 'woocommerce');
                case WC_Coupon::WC_COUPON_REMOVED:
                    return __('Store credit removed successfully.', 'woocommerce');
                default:
                    return $msg;
            }
        } else {
            return $msg;
        }
    }

    /**
     * Returns amount of the cart we are using in order
     * to calculate the proper amount of store credit to apply.
     * 
     * This will later consider things such as taxes, shipping, etc.
     * Maybe once day we'll have more settings.
     * 
     * @param \WC_Cart $cart
     *
     * @return float
     */
    public static function get_cart_applicable_amount($cart)
    {
        $cart_subtotal = $cart->get_subtotal();
        return $cart_subtotal;
    }

    /**
     * @param \WC_Coupon $coupon
     * @return boolean
     */
    public static function is_coupon_store_credit($coupon)
    {
        return (bool)get_post_meta((int)$coupon->get_id(), self::META_KEY_IS_COUPON_STORE_CREDIT, true);
    }

    /**
     * @return void
     */
    public static function add_store_credit_checkout_notice()
    {
        $maybe_affiliate = Affiliate::current();
        if (is_null($maybe_affiliate)) {
            return;
        }
        $available_credit_amount = self::outstanding_store_credit_for_affiliate($maybe_affiliate->id);

        if ($available_credit_amount <= 0.0) {
            return;
        }

        $available_credit_amount_formatted = Formatters::money($available_credit_amount);

        $use_store_credit_btn = AjaxButton::render(AjaxHandler::AJAX_APPLY_STORE_CREDIT_TO_CART, __('Apply store credit', 'solid-affiliate'), ['affiliate_id' => $maybe_affiliate->id]);

        wc_print_notice(
            sprintf(__('You have %1$s of store credit available for use during this checkout.', 'solid-affiliate'), $available_credit_amount_formatted) . $use_store_credit_btn,
            'notice'
        );
    }

    /**
     * @return Either<string> - The store credit coupon code
     */
    public static function apply_store_credit_to_current_woocommerce_cart()
    {
        //////////////////////////////////////////////////////////////////////////
        // Get the cart, affiliate, and user
        $cart = WC()->cart;
        $affiliate = Affiliate::find_where(['user_id' => get_current_user_id()]);
        if (is_null($affiliate)) {
            return new Either(['No affiliate found.'], '', false);
        }
        $user = $affiliate->user();
        if (!$user) {
            return new Either(['No user found.'], '', false);
        }
        //////////////////////////////////////////////////////////////////////////

        $available_credit_amount = self::outstanding_store_credit_for_affiliate($affiliate->id);
        if ($available_credit_amount <= 0.0) {
            return new Either(['No store credit available.'], '', false);
        }


        //////////////////////////////////////////////////////
        // Check if the cart has a store credit coupon applied.

        $does_cart_already_contain_store_credit_coupon = self::does_cart_contain_store_credit($cart);

        if ($does_cart_already_contain_store_credit_coupon) {
            return new Either(['You already have a store credit coupon applied.'], '', false);
        }
        //////////////////////////////////////////////////////

        $amount_of_store_credit_to_apply = self::amount_of_store_credit_to_apply($cart, $affiliate->id);
        if ($amount_of_store_credit_to_apply <= 0.0) {
            return new Either(['No store credit to apply, the cart amount is already fully discounted.'], '', false);
        }

        $coupon_expiration_date = date('Y-m-d-s', strtotime('+3 days', (int)current_time('timestamp')));
        $user_emails = [$user->user_email];

        $coupon_code = 'STORE-CREDIT' .  '-' . RandomData::string(6);

        $coupon_data = [
            'discount_type'    => 'fixed_cart',
            'coupon_amount'    => $amount_of_store_credit_to_apply,
            'individual_use'   => 'no',
            'usage_limit'      => '1',
            'usage_count'      => '0',
            'expiry_date'      => $coupon_expiration_date,
            'apply_before_tax' => 'yes',
            'free_shipping'    => 'no',
            'customer_email'   => $user_emails,
            self::META_KEY_IS_COUPON_STORE_CREDIT => true,
        ];

        // TODO find any existing store credit coupons for this affiliate and delete them.
        self::delete_existing_store_credit_coupons_for_email($user->user_email);


        $coupon = array(
            'post_title'   => $coupon_code,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => 1,
            'post_type'    => 'shop_coupon',
            'meta_input'   => $coupon_data
        );

        $new_coupon_id = wp_insert_post($coupon);

        /**
         * @psalm-suppress DocblockTypeContradiction
         */
        if ($new_coupon_id instanceof \WP_Error) {
            $error_msg = $new_coupon_id->get_error_message();
            return new Either([$error_msg], '', false);
        }

        $new_coupon = new \WC_Coupon($new_coupon_id);
        $coupon_code = $new_coupon->get_code();

        $maybe_added_discount = $cart->add_discount($coupon_code);
        if (!$maybe_added_discount) {
            return new Either(['Could not add coupon to cart.'], '', false);
        } else {
            return new Either([''], $coupon_code, true);
        }
    }
    ////////////// end - woo checkout hooks

    /**
     * Get coupons by customer email and a specific meta key.
     *
     * @param string $customer_email The customer email to search for.
     * @return \WC_Coupon[] An array of matching WC_Coupon objects.
     */
    public static function get_existing_store_credit_coupons_for_email($customer_email)
    {
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'shop_coupon',
            'post_status'    => 'publish',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => 'customer_email',
                    'value'   => $customer_email,
                    'compare' => 'LIKE', // Because customer_email is an array.
                ),
                array(
                    'key'   => 'solid_affiliate_is_store_credit_coupon',
                    'value' => true,
                ),
                array(
                    'key'     => '_usage_count',
                    'value'   => '0',
                    'compare' => '='
                ),
            ),
        );

        $coupons = get_posts($args);

        // Convert WP_Post objects to WC_Coupon objects.
        $wc_coupons = array_map(function ($post) {
            /**
             * @psalm-suppress PossiblyInvalidPropertyFetch
             */
            return new WC_Coupon($post->ID);
        }, $coupons);

        return $wc_coupons;
    }

    /**
     * @param string $customer_email
     * 
     * @return void
     */
    public static function delete_existing_store_credit_coupons_for_email($customer_email)
    {
        $coupons = self::get_existing_store_credit_coupons_for_email($customer_email);
        foreach ($coupons as $coupon) {
            $coupon->delete(true);
        }
    }



    /**
     * @param \WC_Cart $cart
     * @param int $affiliate_id
     * 
     * @return float
     */
    public static function amount_of_store_credit_to_apply($cart, $affiliate_id)
    {
        $available_credit_amount = self::outstanding_store_credit_for_affiliate($affiliate_id);

        $cart_total = self::get_cart_applicable_amount($cart);
        return (float)min($available_credit_amount, $cart_total);
    }


    /**
     * @param \WC_Cart $cart
     * 
     * @return bool
     */
    public static function does_cart_contain_store_credit($cart)
    {
        $currently_applied_coupons = $cart->get_applied_coupons();
        $does_cart_already_contain_store_credit_coupon = !empty(array_filter($currently_applied_coupons, function ($coupon_code) {
            return self::is_coupon_store_credit(new \WC_Coupon($coupon_code));
        }));

        return $does_cart_already_contain_store_credit_coupon;
    }

    /**
     * Undocumented function
     *
     * @param \WC_Cart $cart
     * @return \WC_Coupon|null
     */
    public static function store_credit_coupon_from_cart($cart)
    {
        $currently_applied_coupons = $cart->get_applied_coupons();
        $coupons = array_filter($currently_applied_coupons, function ($coupon_code) {
            return self::is_coupon_store_credit(new \WC_Coupon($coupon_code));
        });

        if (empty($coupons)) {
            return null;
        } else {
            return new \WC_Coupon(array_shift($coupons));
        }
    }

    const SETTING_KEY_NOTIFICATION_EMAIL_SUBJECT_AFFILIATE_NEW_STORE_CREDIT = 'notification_email_subject_affiliate_new_store_credit';
    const SETTING_KEY_NOTIFICATION_EMAIL_BODY_AFFILIATE_NEW_STORE_CREDIT = 'setting_key_notification_email_body_affiliate_new_store_credit';
    const SETTING_KEY_IS_STORE_CREDIT_TRANSACTION_NOTIFICATION_EMAIL_ENABLED = 'setting_key_is_store_credit_transaction_notification_email_enabled';

    /**
     * @param ListTableConfigs $list_table_configs
     * @return ListTableConfigs
     */
    public static function add_store_credit_column_to_affiliates_admin_list_table($list_table_configs)
    {

        //////////////////////////////////////////////////////
        // add the store_credit computed column
        $computed_columns = $list_table_configs->computed_columns;
        $computed_columns[] = [
            'column_name' => __('store_credit', 'solid-affiliate'),
            'function' =>
            /** 
             * @param Affiliate $item 
             * @return string
             **/
            function ($item) {
                $amount = self::outstanding_store_credit_for_affiliate($item->id);
                return Formatters::money($amount);
            }
        ];
        $list_table_configs->computed_columns = $computed_columns;

        //////////////////////////////////////////////////////
        // add the store_credit column name override
        $list_table_configs->column_name_overrides['store_credit'] = __('Store Credit', 'solid-affiliate');

        return $list_table_configs;
    }

    /**
     * The returned AddonDescription is used by \SolidAffiliate\Addons\Core
     * to display the addon in the admin panel, handle the settings, etc.
     *
     * @param AddonDescription[] $addon_descriptions
     * @return AddonDescription[]
     */
    public static function register_addon_description($addon_descriptions)
    {
        $settings_schema = new Schema(["entries" => [
            self::SETTING_KEY_IS_STORE_CREDIT_TRANSACTION_NOTIFICATION_EMAIL_ENABLED => new SchemaEntry(array(
                'type' => 'bool',
                'display_name' => __('Enable store credit transaction notification emails', 'solid-affiliate'),
                'user_default' => true,
                'form_input_description' => __('When enabled, an email notification will be sent to your affiliate whenever they receive store credit, or an admin deducts their store credit.', 'solid-affiliate'),
                'required' => false,
                'show_on_edit_form' => true,
            )),
            self::SETTING_KEY_NOTIFICATION_EMAIL_SUBJECT_AFFILIATE_NEW_STORE_CREDIT => new SchemaEntry(array(
                'type' => 'varchar',
                'length' => 255,
                'display_name' => __('Email Subject', 'solid-affiliate'),
                'user_default' => 'Store Credit Earned',
                'settings_group' => 'Affiliate - Store Credit Notification Email',
                'settings_tab' => 'Store Credit Emails',
                'show_on_edit_form' => true,
                'form_input_description' => __('Enter the subject line for this email.', 'solid-affiliate'),
                'required' => false
            )),
            self::SETTING_KEY_NOTIFICATION_EMAIL_BODY_AFFILIATE_NEW_STORE_CREDIT => new SchemaEntry(array(
                'type' => 'wp_editor',
                'display_name' => __('Email Body', 'solid-affiliate'),
                'user_default' => Email_Notifications::get_email_template_affiliate_new_store_credit(),
                'settings_group' => 'Affiliate - Referral Notification Email',
                'settings_tab' => 'Store Credit Emails',
                'show_on_edit_form' => true,
                'form_input_description' => __("Enter the email to send when an Affiliate earns Store Credit. HTML is accepted. Available template tags:", 'solid-affiiate') . Templates::tags_to_documentation_html(['Affiliate', 'Store Credit Transaction']),
                'required' => false
            )),
        ]]);

        $description = new AddonDescription([
            'slug' => self::ADDON_SLUG,
            'name' => __('Store Credit', 'solid-affiliate'),
            'description' => __("Enable the Store Credit functionality, allowing affiliates to be rewarded in and then redeem store credit.", 'solid-affiliate'),
            'author' => 'Solid Affiliate',
            'graphic_src' => '',
            'settings_schema' => $settings_schema,
            'documentation_url' => self::DOCS_URL,
            'enabled_by_default' => true
        ]);

        $addon_descriptions[] = $description;
        return $addon_descriptions;
    }

    /**
     * Add the Data Export Addon submenu page.
     *
     * @param string $menu_slug
     *
     * @return void
     */
    public static function add_page_to_submenu($menu_slug)
    {
        $hook_suffix = add_submenu_page(
            $menu_slug,
            'Solid Affiliate - ' . self::MENU_TITLE,
            __(self::MENU_TITLE, 'solid-affiliate'),
            'manage_options',
            self::ADMIN_PAGE_KEY,
            function () {
                echo AdminHeader::render_from_get_request($_GET);
                echo (self::admin_root());
            }
        );

        // Only add screen options to the index page.
        if (!isset($_GET['action']) && isset($_GET['tab']) && $_GET['tab'] == 'store_credit_transactions') {
            add_action("load-$hook_suffix", [StoreCreditTransactionsListTable::class, 'add_screen_options']);
        }
        StoreCreditTransactionsListTable::add_screen_options();
    }

    /**
     * Register a POST route for each resource download.
     *
     * @param RouteDescription[] $routes
     *
     * @return RouteDescription[]
     */
    public static function register_routes($routes)
    {
        $download_routes = array(
            new RouteDescription([
                'post_param_key' => self::POST_PARAM_ADJUST_STORE_CREDIT,
                'nonce' => self::NONCE_ADJUST_STORE_CREDIT,
                'callback' => function () {
                    self::POST_handle_adjust_store_credit();
                }
            ]),
        );

        return array_merge($routes, $download_routes);
    }

    /**
     * @return void
     */
    public static function POST_handle_adjust_store_credit()
    {
        $param_keys = [
            "affiliate_id",
            "amount",
            "description",
            "is_send_affiliate_email_notification",
            "type"
        ];

        $schema = self::schema_adjust_store_credit();
        $eitherFields = ControllerFunctions::extract_and_validate_POST_params($_POST, $param_keys, $schema);

        if ($eitherFields->isLeft) {
            ControllerFunctions::handle_redirecting_and_exit('REDIRECT_BACK', $eitherFields->left);
        } else {
            // TODO Do things with the validated data.
            $amount = (float)$eitherFields->right['amount'];
            $reason = (string)$eitherFields->right['description'];
            $type = (string)$eitherFields->right['type'];
            $affiliate_id = (int)$eitherFields->right['affiliate_id'];


            $formatted_amount = Formatters::money($amount);
            $source = StoreCreditTransaction::SOURCE_MANUAL;
            $sign = ($type === StoreCreditTransaction::TYPE_DEBIT) ? '+' : '-';
            $description = "Manual adjustment of {$sign}{$formatted_amount}. Reason given: $reason."; // TODO Reason given

            if ($type === StoreCreditTransaction::TYPE_DEBIT) {
                $either_transaction = self::add_store_credit_to_affiliate($affiliate_id, $amount, $source, $description);
            } else {
                $either_transaction = self::remove_store_credit_from_affiliate($affiliate_id, $amount, $source, $description);
            }

            if ($either_transaction->isLeft) {
                // TODO
            } else {
                if ($eitherFields->right['is_send_affiliate_email_notification']) {
                    Email_Notifications::async_email_store_credit_transaction_notification($either_transaction->right->id);
                }
            }
        }
    }

    /**
     * The message to be displayed on the settings page above the settings form.
     *
     * @return void
     */
    public static function settings_message()
    {
    }

    /**
     * The POST action that handles downloading Affiliates as CSV.
     *
     * @return void
     */
    // public static function POST_download_affiliate_csv()
    // {
    //     self::enforce_capability();
    //     self::handle_export(AffiliateExport::csv_export());
    //     exit();
    // }


    /**
     * The list page for the Data Export UI.
     *
     * @return string
     */
    public static function admin_root()
    {
        $html = "";

        $html .= self::page_heading();

        $html .= self::render_admin_store_credit_page_body();

        return $html;
    }


    /**
     * Returns the HTML for the Store Credit page heading.
     *
     * @return string
     */
    private static function page_heading()
    {
        ob_start();
?>
        <div class="wrap">
            <h1></h1>
            <div class="sld-addon-hero store-credit">
                <a class="button goback" href='admin.php?page=solid-affiliate-addons'>
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5575 1.5575L4.5 0.5L0 5L4.5 9.5L5.5575 8.4425L2.1225 5L5.5575 1.5575Z" fill="#fff" />
                    </svg>
                    <?php _e('All Addons', 'solid-affiliate') ?>
                </a>
                <h2><?php _e('Store credit', 'solid-affiliate') ?></h2>
                <p><?php _e('Enable the Store Credit functionality, allowing affiliates to be rewarded in and then redeem store credit.', 'solid-affiliate') ?></p>
            </div>

        <?php
        return ob_get_clean();
    }

    /**
     * Returns the HTML that links to the Data Export page.
     *
     * @return string
     */
    private static function link_to_admin_page()
    {
        return sprintf('<a href="%1$s">%2$s</a>', admin_url('admin.php?page=' . self::ADMIN_PAGE_KEY), __(self::MENU_TITLE, 'solid-affiliate'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // START - Logic
    // Helper Functions. Maybe move this out of this file
    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Calculates the total outstanding store credit. For
     * example, if there are 30 affiliate with $50.00 each
     * in credit, this would return 30 * $50.00 = $1,500.00
     * @param int|null $affiliate_id
     * 
     * @return float
     */
    public static function total_outstanding_store_credit($affiliate_id = null)
    {
        if ($affiliate_id) {
            $transactions = StoreCreditTransaction::where([
                'affiliate_id' => $affiliate_id
            ]);
        } else {
            $transactions = StoreCreditTransaction::all();
        }


        return self::sum_transactions($transactions);
    }

    /**
     * Calculates the total redeemed store credit.
     * @param int|null $affiliate_id
     * 
     * @return float
     */
    public static function total_redeemed_store_credit($affiliate_id = null)
    {
        $sources_which_count_towards_redeemed = [
            StoreCreditTransaction::SOURCE_WOOCOMMERCE_PURCHASE,
            StoreCreditTransaction::SOURCE_WOOCOMMERCE_SUBSCRIPTION_RENEWAL
        ];

        $where_clause = [
            'type' => StoreCreditTransaction::TYPE_CREDIT,
            'source' => [
                'operator' => 'IN',
                'value' => $sources_which_count_towards_redeemed
            ]
        ];

        if ($affiliate_id) {
            $where_clause['affiliate_id'] = $affiliate_id;
        }

        $transactions = StoreCreditTransaction::where($where_clause);

        // need to flip the sign
        return 0.0 - self::sum_transactions($transactions);
    }

    /**
     * Returns the total outstanding store credit for
     * an affiliate.
     *
     * @param int $affiliate_id
     * @return float
     */
    public static function outstanding_store_credit_for_affiliate($affiliate_id)
    {
        $transactions = StoreCreditTransaction::where([
            'affiliate_id' => $affiliate_id,
        ]);

        return self::sum_transactions($transactions);
    }

    /**
     * Returns the total redeemed store credit for
     * an affiliate.
     *
     * @param int $affiliate_id
     * @return float
     */
    public static function redeemed_store_credit_for_affiliate($affiliate_id)
    {
        return self::total_redeemed_store_credit($affiliate_id);
    }

    /**
     * @param StoreCreditTransaction[] $store_credit_transactions
     * @return float
     */
    public static function sum_transactions($store_credit_transactions)
    {
        $total = array_reduce(
            $store_credit_transactions,
            /**
             * @param float $total
             * @param StoreCreditTransaction $transaction 
             */
            function ($total, $transaction) {
                return $transaction->type == 'debit' ? $total + $transaction->amount : $total - $transaction->amount;
            },
            0.0
        );

        return $total;
    }


    /**
     * Examples:
     *  add_store_credit_to_affiliate(1, 50.00, 'manual');
     *  add_store_credit_to_affiliate(1, 100.00, $payout);
     *
     * @param int $affiliate_id
     * @param float $amount
     * @param 'manual'|Payout $source
     * @param string|null $description
     * 
     * @return Either<StoreCreditTransaction> - the Store Credit transaction ID TODO
     */
    public static function add_store_credit_to_affiliate($affiliate_id, $amount, $source, $description = '')
    {
        return self::adjust_store_credit_for_affiliate($affiliate_id, StoreCreditTransaction::TYPE_DEBIT, $amount, $source, $description);
    }

    /**
     * Examples:
     *  remove_store_credit_from_affiliate(1, 50.00, 'manual');
     *  remove_store_credit_from_affiliate(1, 100.00, $payout);
     *
     * @param int $affiliate_id
     * @param float $amount
     * @param 'manual'|Payout|\WC_Order $source
     * @param string|null $description
     * 
     * @return Either<StoreCreditTransaction> - the Store Credit transaction ID TODO
     */
    public static function remove_store_credit_from_affiliate($affiliate_id, $amount, $source, $description = '')
    {
        return self::adjust_store_credit_for_affiliate($affiliate_id, StoreCreditTransaction::TYPE_CREDIT, $amount, $source, $description);
    }

    /**
     * Examples:
     *  adjust_store_credit_for_affiliate(1, 'credit', 50.00, 'manual');
     *  remove_store_credit_to_affiliate(1, 100.00, $payout); // TODO how to handle this?
     *
     * @param int $affiliate_id
     * @param StoreCreditTransaction::TYPE_* $type
     * @param float $amount
     * @param 'manual'|Payout|\WC_Order $source
     * @param string|null $description
     * 
     * @return Either<StoreCreditTransaction> - the Store Credit transaction ID TODO
     */
    public static function adjust_store_credit_for_affiliate($affiliate_id, $type, $amount, $source, $description = '')
    {
        $args = [
            'affiliate_id' => $affiliate_id,
            'amount' => $amount,
            'created_by_user_id' => get_current_user_id(),
            'description' => $description,
            'type' => $type
        ];

        if ($source == StoreCreditTransaction::SOURCE_MANUAL) {
            $args['source'] = StoreCreditTransaction::SOURCE_MANUAL;
        }

        if ($source instanceof Payout) {
            $args['source'] = StoreCreditTransaction::SOURCE_PAYOUT;
            $args['source_id'] = $source->id;
        }

        if ($source instanceof \WC_Order) {
            $args['source'] = StoreCreditTransaction::SOURCE_WOOCOMMERCE_PURCHASE;
            $args['source_id'] = $source->get_id();
        }

        $either_transaction = StoreCreditTransaction::insert($args);

        return $either_transaction;
    }

    /**
     * Helper method to 'pay' an affiliate for a referral with store credit.
     * 
     * Side effects:
     *   - Adds a store credit transaction to the database.
     *   - Creates a Payout record for the affiliate with method = 'store_credit'.
     *   - Marks the referral as paid.
     * 
     * Examples:
     *
     * @param int $affiliate_id
     * @param int $referral_id
     * 
     * @return Either<StoreCreditTransaction> - the Store Credit transaction ID
     */
    public static function pay_affiliate_for_referral_with_store_credit($affiliate_id, $referral_id)
    {
        /** @var StoreCreditTransaction */
        $null_either = null; // This is a mega hack. I need to figure out a better way to do the Either thing.

        // TODO data integrity check
        // - make sure the affiliate and referral exist
        // - make sure the referral is unpaid

        $referral = Referral::find($referral_id);
        $affiliate = Affiliate::find($affiliate_id);
        if (is_null($referral) || is_null($affiliate) || $referral->status != Referral::STATUS_UNPAID) {
            return new Either(['Referral not found or Affiliate not found or Referral status not unpaid.'], $null_either, false);
        }

        // Create a Payout
        // TODO creating a payout should be handled in add_store_credit_to_affiliate(). We always should make a payout
        $either_payout = Payout::insert([
            'affiliate_id' => $affiliate_id,
            'amount' => $referral->commission_amount,
            'payout_method' => Payout::PAYOUT_METHOD_STORE_CREDIT,
            'created_by_user_id' => get_current_user_id(),
            'status' => Payout::STATUS_PAID,
        ]);

        if ($either_payout->isLeft) {
            return new Either($either_payout->left, $null_either, false);
        }

        // Create a Store Credit transaction
        $either_transaction = self::add_store_credit_to_affiliate(
            $affiliate_id,
            $referral->commission_amount,
            $either_payout->right
        );
        if ($either_transaction->isLeft) {
            Payout::delete($either_payout->right->id);
            return new Either($either_transaction->left, $null_either, false);
        }

        // Update the Referral
        $either_referral = Referral::updateInstance($referral, [
            'status' => Referral::STATUS_PAID,
            'payout_id' => $either_payout->right->id,
        ]);
        if ($either_referral->isLeft) {
            Payout::delete($either_payout->right->id);
            StoreCreditTransaction::delete($either_transaction->right->id);
            return new Either($either_referral->left, $null_either, false);
        }

        return $either_transaction;
    }

    /**
     * @param int $affiliate_id
     * @param int $page - for paginating store credit transactions
     * 
     * @return StoreCreditDataType
     */
    public static function affiliate_portal_store_credit_data_for_affiliate_id($affiliate_id, $page)
    {
        $where_query = [
            'affiliate_id' => $affiliate_id,
            'order_by' => 'id',
            'order' => 'DESC'
        ];
        $store_credit_transactions = StoreCreditTransaction::paginate(
            ['limit' => GlobalTypes::AFFILIATE_PORTAL_PAGINATION_PER_PAGE, 'page' => $page],
            $where_query
        );

        $total_store_credit_transactions = StoreCreditTransaction::count([
            'affiliate_id' => $affiliate_id,
        ]);

        return [
            'is_enabled' => Core::is_addon_enabled(self::ADDON_SLUG),
            'outstanding_store_credit' => self::outstanding_store_credit_for_affiliate($affiliate_id),
            'redeemed_store_credit' => self::redeemed_store_credit_for_affiliate($affiliate_id),
            'store_credit_transactions' => $store_credit_transactions,
            'total_store_credit_transactions' => $total_store_credit_transactions,
        ];
    }
    /**
     * Returns the HTML representing the Affiliate Landing Pages icon shown on the Affiliate Portal if $tab_key passed in by the filter is the Affiliate Landing Pages tab.
     *
     * @param string $default_icon
     * @param string $tab_key
     *
     * @return string
     */
    public static function add_store_credit_icon_to_affiliate_portal($default_icon, $tab_key)
    {
        if ($tab_key === self::AFFILIATE_PORTAL_TAB_KEY) {
            return '<svg class="sld-ap-nav_menu-icon" width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.6 3.6H10.8C10.8 1.611 9.189 0 7.2 0C5.211 0 3.6 1.611 3.6 3.6H1.8C0.81 3.6 0 4.41 0 5.4V16.2C0 17.19 0.81 18 1.8 18H12.6C13.59 18 14.4 17.19 14.4 16.2V5.4C14.4 4.41 13.59 3.6 12.6 3.6ZM5.4 7.2C5.4 7.695 4.995 8.1 4.5 8.1C4.005 8.1 3.6 7.695 3.6 7.2V5.4H5.4V7.2ZM7.2 1.8C8.19 1.8 9 2.61 9 3.6H5.4C5.4 2.61 6.21 1.8 7.2 1.8ZM10.8 7.2C10.8 7.695 10.395 8.1 9.9 8.1C9.405 8.1 9 7.695 9 7.2V5.4H10.8V7.2Z"/>
                    </svg>
                    ';
        } else {
            return $default_icon;
        }
    }

    /**
     * Returns the array of tuples representing all the tabs in the Affiliate Portal with the Affiliate Landing Pages tab at the end of the array.
     *
     * @param array<array{0: string, 1: string}> $tab_tuples
     *
     * @return array<array{0: string, 1: string}>
     */
    public static function add_store_credit_tab_to_affiliate_portal($tab_tuples)
    {
        array_push($tab_tuples, [self::AFFILIATE_PORTAL_TAB_KEY, __('Store Credit', 'solid-affiliate')]);
        return $tab_tuples;
    }

    /**
     * Returns the HTML for the Affiliate Landing Pages tab in the Affiliate Portal if the $current_tab passed in by the filter is the Affiliate Landing Pages tab.
     *
     * @param AffiliatePortalViewInterface $Itab
     *
     * @return void
     */
    public static function maybe_render_store_credit_tab_on_affiliate_portal($Itab)
    {
        ?>
            <div x-show="current_tab === '<?php echo (self::AFFILIATE_PORTAL_TAB_KEY) ?>'">
                <?php echo  self::_render_affiliate_portal_tab($Itab); ?>
            </div>
        <?php
    }

    /**
     * Returns the HTML to be displayed on the Affiliate Landing Pages tab in the Affiliate Portal.
     *
     * @param AffiliatePortalViewInterface $Itab
     *
     * @return string
     */
    private static function _render_affiliate_portal_tab($Itab)
    {
        $affiliate_id = $Itab->affiliate->id;

        $total_outstanding_card = self::render_total_outstanding_store_credit($affiliate_id);

        /////////////////////////////////////////////
        // Store Credit Transaction table
        $store_credit_transactions_headers = Translation::translate_array(['Amount', 'Description', 'Date']);

        $store_credit_transactions_rows = array_map(function ($transaction) {
            $amount_formatted = Formatters::store_credit_amount($transaction->amount, $transaction->type);
            $formatted_date = date('F j, Y', strtotime($transaction->created_at));
            return [$amount_formatted, $transaction->description, $formatted_date];
        }, $Itab->store_credit_data['store_credit_transactions']);

        $store_credit_transactions_total_count = $Itab->store_credit_data['total_store_credit_transactions'];

        $store_credit_transactions_table = SimpleTableView::render(
            $store_credit_transactions_headers,
            $store_credit_transactions_rows,
            ['total_count' => $store_credit_transactions_total_count],
            'No data to display.',
            'sld-ap-table'
        );
        /////////////////////////////////////////////
        ob_start();
        ?>
            <div class='solid-affiliate-affiliate-portal_store-credit'>
                <h2 class="sld-ap-title"><?php _e('Store Credit', 'solid-affiliate') ?></h2>
                <p class="sld-ap-description"><?php _e('Here is where you can see your store credit balance, redeem your store credit, and see all your store credit transactions.', 'solid-affiliate') ?></p>
                <?php echo $total_outstanding_card ?>
                <p><strong><?php _e('You can redeem your store credit at checkout.', 'solid-affiliate') ?></strong> <?php _e('Simply add products on this site to your cart, and at checkout you will see an option to apply store credit.', 'solid-affiliate') ?></p>
                <h4><?php _e('Past Store Credit Transactions', 'solid-affiliate') ?></h4>
                <?php echo ($store_credit_transactions_table) ?>
            </div>
        <?php
        return ob_get_clean();
    }

    //////////////////////////////////////////////////////////////////  
    // Start - Redeeming Store Credit
    //////////////////////////////////////////////////////////////////  

    //////////////////////////////////////////////////////////////////  
    // End - Redeeming Store Credit
    //////////////////////////////////////////////////////////////////  


    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // End - Logic
    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////


    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // Start - Views 
    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public static function render_admin_store_credit_page_body()
    {

        $default_tab = null;
        $tab = isset($_GET['tab']) ? (string)$_GET['tab'] : $default_tab;

        ob_start();
        ?>
            <!-- Our admin page content should all be inside .wrap -->
            <div class="wrap">
                <h2></h2>
                <!-- Print the page title -->
                <!-- Here are our tabs -->
                <?php echo AdminTabsView::render(
                    self::ADMIN_PAGE_KEY,
                    [
                        ['overview', __(' Overview', 'solid-affiliate')],
                        ['store_credit_transactions', __('Store credit transactions', 'solid-affiliate')],
                        ['settings', __('Settings', 'solid-affiliate')],
                    ],
                    $tab
                )
                ?>

                <div class="tab-content">
                    <?php switch ($tab):
                        case 'overview':
                            echo self::render_admin_store_credit_overview();
                            break;
                        case 'store_credit_transactions':
                            echo self::render_admin_store_credit_transactions_list_table();
                            break;
                        case 'settings':
                            $settings_url = Core::url_for_addon_settings(self::ADDON_SLUG);
                            $link = Links::render($settings_url, 'Settings');
                            echo "<br><br>" . "All settings for this addon can be found within Addons > Store Credit > $link";
                            break;
                        default:
                            echo self::render_admin_store_credit_overview();
                            break;
                    endswitch; ?>
                </div>
            </div>

    <?php
        $res = ob_get_clean();
        if ($res) {
            return $res;
        } else {
            return __("Error rendering store credit page.", 'solid-affiliate');
        }
    }

    /**
     * @return string
     */
    public static function render_admin_store_credit_overview()
    {
        $total_outstanding_card = self::render_total_outstanding_store_credit();
        $total_redeemed_card = self::render_total_redeemed_store_credit();

        $pay_affiliates_link = Links::render(
            URLs::admin_path(PayAffiliatesController::ADMIN_PAGE_KEY),
            'Pay Affiliates'
        );

        $header_message_1 = "<p>Store Credit is enabled. Your affiliates can earn store credit and then use the store credit on any order at checkout. The affiliates must be logged in to see and apply their available store credit at checkout. You can award store credit via the {$pay_affiliates_link} tool (recommended), or manually as an admin on any Affiliate > Edit screen.</p>";
        $header_message_2 = "<p>Email notifications will be sent to affiliates whenever they earn store credit. You can disable this in the addon settings.</p>";
        $header_message_3 = "<p>You can use the Store Credit Transactions tab above to view every logged transaction.</p>";
        return '<div class="sld-store-credit-admin-overview">' . '<h2> Overview </h2>' . $header_message_1 . $header_message_2 . $header_message_3 . $total_outstanding_card . $total_redeemed_card . '</div>';
    }

    /**
     * @param int|null $affiliate_id
     * @return string
     */
    public static function render_total_redeemed_store_credit($affiliate_id = null)
    {
        $total = self::total_redeemed_store_credit($affiliate_id);
        $total_formatted = Formatters::money($total);
        $total_formatted = '<span class="sld-store-credit-transaction-type sld-credit">' . $total_formatted . '</span>';

        $total_redeemed_card = CardView::render(__('Total Redeemed Store Credit', 'solid-affiliate'), $total_formatted);
        return $total_redeemed_card;
    }

    /**
     * @param int|null $affiliate_id
     * @return string
     */
    public static function render_total_outstanding_store_credit($affiliate_id = null)
    {
        $total = self::total_outstanding_store_credit($affiliate_id);
        $total_formatted = Formatters::money($total);
        $total_formatted = '<span class="sld-store-credit-transaction-type sld-debit">' . $total_formatted . '</span>';

        $total_outstanding_card = CardView::render(__('Total Outstanding Store Credit', 'solid-affiliate'), $total_formatted);
        return $total_outstanding_card;
    }

    /**
     * @return string
     */
    public static function render_admin_store_credit_transactions_list_table()
    {
        $list_table = new StoreCreditTransactionsListTable();
        $o = WPListTableView::render(self::ADMIN_PAGE_KEY, $list_table, __('Store Credit Transactions', 'solid-affiliate'), false);
        return $o;
    }

    /**
     * @return string
     */
    public static function render_transaction_logs()
    {
        // StoreCreditTransaction::data_for_logs();

        $list_table = new StoreCreditTransactionsListTable();
        $o = '<h2>Store Credit Transactions</h2>';
        $o .= WPListTableView::render(self::ADMIN_PAGE_KEY, $list_table, __('Store Credit Transactions', 'solid-affiliate'), false);
        return $o;
    }

    /**
     * Return to the AFFILIATE_EDIT_AFTER_FORM_FILTER filter to be rendered on the Affiliate Edit page.
     * Displays the relevant forms, table data, and settings to the admin.
     *
     * @param array<string> $panels
     * @param Affiliate|null $affiliate
     *
     * @return array<string>
     */
    public static function render_affiliate_edit_store_credit_section($panels, $affiliate)
    {
        if (is_null($affiliate)) {
            return $panels;
        }

        $header = '<h2 id="edit-affiliate-store_credit">' . __('Store Credit', 'solid-affiliate') . '</h2>';

        /////////////////////////////////////////////
        // Stats Section
        $stats_section_header = '<p>' . __('An overview of store credit statistics of this affiliate.', 'solid-affiliate') . '</p>';
        $total_outstanding_card = self::render_total_outstanding_store_credit($affiliate->id);
        $total_redeemed_card = self::render_total_redeemed_store_credit($affiliate->id);
        /////////////////////////////////////////////


        /////////////////////////////////////////////
        // Transactions Section
        $transactions_section_header = '<h4>' . __('Store credit transactions for this affiliate', 'solid-affiliate') . '</h4>';
        $transactions_count = StoreCreditTransaction::count([
            'affiliate_id' => $affiliate->id,
        ]);
        if ($transactions_count > 0) {
            $base_link = URLs::index(StoreCreditTransaction::class);
            $transactions_url = add_query_arg(
                [
                    's' => $affiliate->id,
                    'tab' => 'store_credit_transactions',
                ],
                $base_link
            );

            $see_all_store_credit_transactions_link = Links::render($transactions_url, "See All {$transactions_count} Store Credit Transactions For This Affiliate");
        } else {
            $see_all_store_credit_transactions_link = '<p>' . __('No store credit transactions found for this affiliate.', 'solid-affiliate') . '</p>';
        }
        /////////////////////////////////////////////


        /////////////////////////////////////////////
        // Actions Section
        $actions_section_header = '<h4>' . __('Store Credit Actions', 'solid-affiliate') . '</h4>';

        $modal_button_content = '<button class="button button-primary">' . __('Adjust store credit for this affiliate', 'solid-affiliate') . '</button>';
        $modal_title = __('Adjust Store Credit for this Affiliate', 'solid-affiliate');

        $schema = self::schema_adjust_store_credit();

        $form_item = (object)['affiliate_id' => $affiliate->id];
        $modal_form = FormBuilder::build_form($schema, 'edit', $form_item, false);
        $modal_content = '<form action="" method="post" id="' . self::POST_PARAM_ADJUST_STORE_CREDIT . '">' .
            $modal_form .
            wp_nonce_field(self::NONCE_ADJUST_STORE_CREDIT) .
            '<br><br>' .
            '<input type="submit" name="' . self::POST_PARAM_ADJUST_STORE_CREDIT . '" id="" class="button button-primary" value="Submit">' .
            '</form>';
        $adjust_store_credit_modal_button = SolidModalView::render($modal_button_content, $modal_title, $modal_content);
        /////////////////////////////////////////////

        $store_credit_panel = $header . $stats_section_header . $total_outstanding_card . $total_redeemed_card . '<br><br><hr>' . $transactions_section_header . $see_all_store_credit_transactions_link . '<br><br><hr>' . $actions_section_header . $adjust_store_credit_modal_button;

        array_unshift($panels, $store_credit_panel);
        return $panels;
    }

    /**
     * @return Schema<"affiliate_id"|"amount"|"description"|"is_send_affiliate_email_notification"|"type">
     */
    public static function schema_adjust_store_credit()
    {
        $addon_settings_url = Core::url_for_addon_settings(self::ADDON_SLUG);
        $addon_settings_link = Links::render($addon_settings_url, 'Edit Email');

        $schema = new Schema([
            'entries' => [
                'affiliate_id' => new SchemaEntry([
                    'type' => 'bigint',
                    'form_input_type_override' => 'affiliate_select',
                    'length' => 20,
                    'required' => true,
                    'display_name' => __('Affiliate', 'solid-affiliate'),
                    'show_on_new_form' => true,
                    'show_on_edit_form' => true,
                ]),
                'type' => new SchemaEntry([
                    'type' => 'varchar',
                    'length' => 255,
                    'required' => true,
                    'display_name' => __('Add or Remove credit?', 'solid-affiliate'),
                    'is_enum' => true,
                    'enum_options' => [
                        [StoreCreditTransaction::TYPE_DEBIT, 'Add'],
                        [StoreCreditTransaction::TYPE_CREDIT, 'Remove'],
                    ],
                    'show_on_new_form' => true,
                    'show_on_edit_form' => true,
                ]),
                'amount' => new SchemaEntry(array(
                    'type' => 'float',
                    'required' => true,
                    'display_name' => __('Amount', 'solid-affiliate'),
                    'show_on_new_form' => true,
                    'show_on_edit_form' => true,
                )),
                'description' => new SchemaEntry(array(
                    'type' => 'text',
                    'form_input_type_override' => 'textarea',
                    'display_name' => __('Reason', 'solid-affiliate'),
                    'show_on_new_form' => true,
                    'show_on_edit_form' => true,
                )),
                'is_send_affiliate_email_notification' => new SchemaEntry([
                    'type' => 'bool',
                    'default' => true,
                    'user_default' => true,
                    'display_name' => "Send affiliate an email notification $addon_settings_link",
                    'show_on_new_form' => true,
                    'show_on_edit_form' => true,
                ]),
            ]
        ]);

        return $schema;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////
    // End - Views
    ///////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////
}
