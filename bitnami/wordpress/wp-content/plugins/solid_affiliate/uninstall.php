<?php

/**
 * Uninstall Solid Affiliate
 *
 * @package     Solid Affiliate
 * @subpackage  Uninstall
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

use SolidAffiliate\Addons\Core;
use SolidAffiliate\Lib\Roles;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Models\Affiliate;
use SolidAffiliate\Models\AffiliateGroup;
use SolidAffiliate\Models\AffiliateProductRate;
use SolidAffiliate\Models\BulkPayout;
use SolidAffiliate\Models\Creative;
use SolidAffiliate\Models\Payout;
use SolidAffiliate\Models\Referral;
use SolidAffiliate\Models\Visit;

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Load Solid Affiliate
include_once('plugin.php');

if ((bool)Settings::get(Settings::KEY_IS_REMOVE_DATA_ON_UNINSTALL)) {
    // [] remove Custom Tables
    global $wpdb;

    $tables = [
        Affiliate::TABLE,
        Referral::TABLE,
        AffiliateGroup::TABLE,
        Creative::TABLE,
        AffiliateProductRate::TABLE,
        Visit::TABLE,
        Payout::TABLE,
        BulkPayout::TABLE
    ];
    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
    };

    // [] Custom roles/capabilities
    Roles::remove_affiliate_related_custom_roles();

    // [] Anything in wp_options
    delete_option(Settings::OPTIONS_KEY);
    delete_option(Core::OPTIONS_KEY_ENABLED_ADDONS);
}
