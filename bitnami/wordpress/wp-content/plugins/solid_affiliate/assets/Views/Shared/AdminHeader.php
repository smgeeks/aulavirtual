<?php

namespace SolidAffiliate\Views\Shared;

use SolidAffiliate\Addons\Core;
use SolidAffiliate\Addons\SolidSearch\Addon as SolidSearchAddon;
use SolidAffiliate\Controllers\PayAffiliatesController;
use SolidAffiliate\Lib\Formatters;
use SolidAffiliate\Models\Affiliate;
use SolidAffiliate\Models\AffiliateGroup;
use SolidAffiliate\Models\AffiliateProductRate;
use SolidAffiliate\Models\Creative;
use SolidAffiliate\Models\Payout;
use SolidAffiliate\Models\Referral;
use SolidAffiliate\Models\Visit;

use SolidAffiliate\Addons\DataExport\Addon as DataExportAddon;
use SolidAffiliate\Addons\StoreCredit\Addon as StoreCreditAddon;
use SolidAffiliate\Controllers\AdminReportsController;
use SolidAffiliate\Controllers\CommissionRatesController;
use SolidAffiliate\Lib\License;
use SolidAffiliate\Lib\Links;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Lib\SolidSearch;
use SolidAffiliate\Lib\URLs;

class AdminHeader
{

  /**
   * Undocumented function

   * @param string $title 

   * @return string
   */
  public static function render($title)
  {

    $deactivated_component = self::render_solid_affiliate_is_deactivated_component();
    ob_start();
?>
    <div class="sld-header">
      <div class="wrapper">
        <img height="20" src="https://solidaffiliate.com/brand/logo.svg" alt="Solid Affiliate">
        <span><?php _e($title, 'solid-affiliate') ?></span>
        <div class="sld-header_solid-search-container">
          <?php echo SolidSearch::render_solid_search_component(); ?>
        </div>
      </div>
    </div>
    <?php echo ($deactivated_component); ?>
    <div class="wrap-space"></div>
<?php
    return ob_get_clean();
  }

  /**
   * @param array $get
   * @param array<string, string>|null $additional_page_map
   * @return string
   */
  public static function render_from_get_request($get, $additional_page_map = null)
  {
    $page = isset($get['page']) ? (string)$get['page'] : '';
    $action = isset($get['action']) ? (string)$get['action'] : '';

    $page_map = [
      'solid-affiliate-admin-dashboard' => 'Dashboard',
      'solid-affiliate-admin-dashboard-v2' => 'Dashboard',
      'solid-affiliate-admin' => 'Dashboard',

      Affiliate::ADMIN_PAGE_KEY => 'Affiliates',
      AffiliateGroup::ADMIN_PAGE_KEY => 'Affiliate Groups',
      AffiliateProductRate::ADMIN_PAGE_KEY => 'Affiliate Product Rates',
      Creative::ADMIN_PAGE_KEY => 'Creatives',
      Payout::ADMIN_PAGE_KEY => 'Payouts',
      Referral::ADMIN_PAGE_KEY => 'Referrals',
      Visit::ADMIN_PAGE_KEY => 'Visits',
      Core::ADDONS_PAGE_SLUG => 'Addons',
      DataExportAddon::ADMIN_PAGE_KEY => 'Data Export',
      StoreCreditAddon::ADMIN_PAGE_KEY => 'Store Credit',

      PayAffiliatesController::ADMIN_PAGE_KEY => 'Pay Affiliates',
      AdminReportsController::ADMIN_PAGE_KEY => 'Reports',
      CommissionRatesController::ADMIN_PAGE_KEY => 'Commission Rates',
      Settings::ADMIN_PAGE_KEY => 'Settings',
      'solid-affiliate-license-key-options' => 'License Key',
    ];

    if ($additional_page_map) {
      $page_map = array_merge($page_map, $additional_page_map);
    }

    $action_map = [
      'edit' => 'Edit',
      'new' => 'New',
      'add' => 'Add',
      'delete' => 'Delete',
      'view' => 'View',
    ];

    // todo maybe handle the id param for edit


    if (isset($page_map[$page])) {
      $url = URLs::admin_path($page);
      $link = Links::render($url, $page_map[$page]);
      $title = '<span class="breadcrumb-item">' . $link . '</span>';
    } else {
      $title = '';
    }

    // $title = isset($page_map[$page]) ? '<span class="breadcrumb-item">' . $link . '</span>' : '';
    $title = isset($action_map[$action]) ? $title . ' <span class="breadcrumb-caret">&rsaquo;</span> ' . '<span class="breadcrumb-item">' . $action_map[$action] . '</span>' : $title;
    $title = isset($get['tab']) ? $title . ' <span class="breadcrumb-caret">&rsaquo;</span> ' . '<span class="breadcrumb-item">' . Formatters::humanize((string)$get['tab']) . '</span>' : $title;
    $title = isset($get['id']) ? $title . ' <span class="breadcrumb-caret">&rsaquo;</span> ' . '<span class="breadcrumb-item">' . (is_array($_GET['id']) ? 'multiple' : '#' . (string)$get['id']) . '</span>' : $title;


    // if page param is set
    if (isset($get['page'])) {
      return self::render($title);
    } else {
      return '';
    }
  }

  /**
   * Potentially renders the deactivated component.
   * @return string
   */
  public static function render_solid_affiliate_is_deactivated_component()
  {
    if (License::is_on_keyless_free_trial() && !License::is_solid_affiliate_activated_and_not_expired()) {
      $keyless_trial_ends_at = License::get_keyless_free_trial_end_timestamp();
      $expires_in = human_time_diff($keyless_trial_ends_at, time());
      return "<div class='sld-notice has-action trial' style='width: calc(100% - 20px);'>" . "<div class='content'><div class='lead'>You are currently on your free trial</div>" . " <div class='plain'>which expires in <b>" . $expires_in . "</b>. <a href='https://solidaffiliate.com/pricing' target='_blank'>Purchase and activate</a> this plugin as soon as possible. It will not function once this trial expires.</div></div>" . "<div class='actions'>" . "<a class='sld-button small primary' href='" . admin_url('admin.php?page=solid-affiliate-license-key-options') . "'>" . __("Manage Your License", 'solid-affiliate') . "</a>" . "</div>" . "</div>";
    }

    $deactivated_component = '';
    if (License::is_solid_affiliate_activated()) {
      if (License::is_solid_affiliate_activated_but_expired()) {
        $deactivated_component .= "<div class='sld-notice has-action trial' style='width: calc(100% - 20px);'>"
          . "<div class='content'>" . "<div class='lead'>" . __("Solid Affiliate is expired.", 'solid-affiliate') . "</div><div class='plain'>" . __("Please renew Solid Affiliate for the plugin to function correctly. You will not receive any security updates, nor be able to add affiliates while your license is expired.", 'solid-affiliate') . "</div></div>" . "<div class='actions'>" . "<a class='sld-button small primary' href='" . admin_url('admin.php?page=solid-affiliate-license-key-options') . "'>" . __("Manage your license", 'solid-affiliate') . "</a>" . "</div>" . "</div>";
      }
    } else {
      $deactivated_component .= "<div class='sld-notice has-action trial'>" . "<p>" . "<b>" . __("Solid Affiliate is not activated.", 'solid-affiliate') . "</b> " . __("Please activate Solid Affiliate for the plugin to function correctly. You will not receive any security updates, nor be able to add affiliates while the plugin is deactivated.", 'solid-affiliate') . "</p>" . "<p>" . "<a href='" . admin_url('admin.php?page=solid-affiliate-license-key-options') . "'>" . __("Activate Solid Affiliate with a valid license key", 'solid-affiliate') . "</a>" . "</p>" . "</div>";
    }

    return $deactivated_component;
  }
}
