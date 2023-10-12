<?php

namespace SolidAffiliate\Lib;


/**
 * @psalm-import-type EnumOptionsReturnType from \SolidAffiliate\Lib\VO\SchemaEntry
 *
 * @psalm-type UserId = int
 * @psalm-type MikeUser = array{user_id: int, name: string}
 * @psalm-type LoginCredentials = array{user_email: string, user_pass: string}
 * @psalm-type PaginationArgs = array{current_page?: int, total_count: int, params_to_add_to_url?: array<string, string>}
 * @psalm-type BulkPayoutCSVMapping = array<int, array{payment_email: string,commission_amount: float}> $mapping
 */
class GlobalTypes
{
    const PAGINATION_PARAM = 'sld_paged';
    const AFFILIATE_PORTAL_PAGINATION_PER_PAGE = 10;
    const AFFILIATE_PORTAL_HTML_ID = 'solid-affiliate-affiliate-portal_dashboard';
    const DATE_RANGE_ENUM_OPTIONS = [
        ['today', 'Today'],
        ['yesterday', 'Yesterday'],
        ['this_week', 'This Week'],
        ['last_week', 'Last Week'],
        ['this_month', 'This Month'],
        ['last_month', 'Last Month'],
        ['this_quarter', 'This Quarter'],
        ['last_quarter', 'Last Quarter'],
        ['this_year', 'This Year'],
        ['last_year', 'Last Year'],
        ['all_time', 'All Time'],
        ['custom', 'Custom']
    ];

    const BEFORE_REFUND_GRACE_PERIOD_DATE_RAGE_ENUM_OPTION_KEY = 'before_refund_grace_period';

    const PAYPAL_DEVELOPER_PORTAL_URL = "https://developer.paypal.com/developer/applications/";
    const PAYPAL_DEFAULT_PAYOUT_NOTE = "Payout from Solid Affiliate";

    //////////////////////////////////
    // Referral Rate + Referral Type selection
    //////////////////////////////////
    /**
     * @return string
     */
    public static function REFERRAL_RATE_DESCRIPTION()
    {
        return "<div class='solid-affiliate-referral-rate-demo mono'>" . __("Example:", 'solid-affiliate') . " " . "<span class='solid-affiliate-referral-rate-demo_example-amount'>$50.00</span>" . " " . __("purchase", 'solid-affiliate') . " x (<strong><span class='solid-affiliate-referral-rate-demo_rate'>20%" . " " . __("percentage", 'solid-affiliate') . "</span></strong>" . " " . __("referral rate", 'solid-affiliate') . ") = <span class='solid-affiliate-referral-rate-demo_commission'>$10.00</span>" . " " . __("commission", 'solid-affiliate') . "</div> <p>" .
         __("This is the default referral rate, used when calculating referral amounts. When Referral Rate Type is set to <code>Percentage (%)</code> this number is interpreted as a percentage. When Referral Rate Type is set to <code>Flat</code> this number is interpreted as a float amount of whichever currency you are using.", 'solid-affiliate'). "</p>";
    }

    /**
     * Returns the translated version of the DATE_RANGE_ENUM_OPTIONS const for UI drop downs.
     *
     * @return EnumOptionsReturnType
     */
    public static function translated_DATE_RANGE_ENUM_OPTIONS()
    {
        return array_map(
            function ($tuple) {
                return [$tuple[0], __($tuple[1], 'solid-affiliate')];
            },
            self::DATE_RANGE_ENUM_OPTIONS
        );
    }
}
