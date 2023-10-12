<?php

namespace SolidAffiliate\Views\AffiliatePortal;

use SolidAffiliate\Controllers\AffiliatesController;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Lib\URLs;
use SolidAffiliate\Lib\Validators;

/**
 * @psalm-import-type EnumOptionsReturnType from \SolidAffiliate\Lib\VO\SchemaEntry
 */
class AffiliatePortalTabsView
{
    const AFFILIATE_PORTAL_TABS_FILTER = 'solid_affiliate/affiliate_portal/tabs';
    const AFFILIATE_PORTAL_TAB_ICON_FILTER = 'solid_affiliate/affiliate_portal/tab_icon';


    /**
     * Undocumented function
     * 
     * @param array<array{0:string, 1:string}> $tab_tuples - examples: [['settings', 'Admin Settings'], ['extra', 'Extra Settings']]
     * @param string|null $current_tab
     * @param string|null $add_element_id_to_tab_url
     * @param array{'is_admin_preview':bool, 'affiliate_id':int}|null $is_admin_preview_tuple
     * 
     * @return string
     */
    public static function render($tab_tuples, $current_tab, $add_element_id_to_tab_url = null, $is_admin_preview_tuple = null)
    {
        $tab_tuples = self::get_filtered_tab_tuples($tab_tuples);

        ////////////////////////////
        // remove any tabs that are hidden in the settings
        $tabs_to_hide = Validators::array_of_string(Settings::get(Settings::KEY_AFFILIATE_PORTAL_TABS_TO_HIDE));
        // $tab_tuples = array_filter($tab_tuples, function ($tuple) use ($tabs_to_hide) {
        //     $tab_key = $tuple[0];
        //     return !in_array($tab_key, $tabs_to_hide);
        // });
        ////////////////////////////


        $is_admin_preview = $is_admin_preview_tuple && $is_admin_preview_tuple['is_admin_preview'];
    
        ob_start();
?>
        <div class="sld-ap-nav">
            <ul class="sld-ap-nav_menu">
                <!-- Here are our tabs -->
                <?php
                // Just set the first tab as the default.
                // TODO make Default tab be active in the render (ajax)
                // $default_tab_key = $tab_tuples[0][0];

                foreach ($tab_tuples as $tuple) {
                    list($tab_key, $tab_display_value) = $tuple;
                    $icon = self::get_tab_icon($tab_key);
                    $href_url = "?tab={$tab_key}";
                    if ($is_admin_preview && $is_admin_preview_tuple['affiliate_id']) {
                        $href_url = $href_url . '&page=solid-affiliate-affiliates&action=' . AffiliatesController::ADMIN_PREVIEW_AFFILIATE_PORTAL_ACTION . '&id='. $is_admin_preview_tuple['affiliate_id'];
                    }
                    if (URLs::get_permalink_format_setting() === 'plain' && !$is_admin_preview) {
                        $href_url = add_query_arg('tab', $tab_key, get_permalink());
                    }
                    // We do this so the browser doesn't jump back to the top with every tab click.
                    if (Settings::get(Settings::KEY_AFFILIATE_PORTAL_IS_ANCHOR_TAG_ENABLED) && !$is_admin_preview) {
                        $href_url = is_null($add_element_id_to_tab_url) ? $href_url : ($href_url . '#' . $add_element_id_to_tab_url);
                    }

                    // We need to add the "sld-hidden-tab" class to the li element if the tab is hidden.
                    // This is because the tab is still rendered, but we don't want it to be visible.
                    $is_tab_hidden = in_array($tab_key, $tabs_to_hide);
                    $li_class = $is_tab_hidden ? 'sld-hide-tab' : '';
                ?>
                    <li class="<?php echo($li_class) ?>">
                        <a id="sld-ap-nav_menu-link_<?php echo $tab_key ?>" href="<?php echo $href_url; ?>" :class="{ 'active': current_tab === '<?php echo $tab_key ?>' }" @click.prevent="if (current_tab != '<?php echo $tab_key ?>') { current_tab = '<?php echo $tab_key ?>'; window.updateTabAndPageInURL('<?php echo $tab_key ?>')};">
                            <?php echo ($icon) ?>
                            <span class="sld-ap-nav_menu-title"><?php echo $tab_display_value ?></span>
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>

<?php
        return ob_get_clean();
    }

    /**
     * Undocumented function
     * 
     * @param string $tab_key
     *
     * @return string
     */
    public static function get_tab_icon($tab_key)
    {
        switch ($tab_key) {
            case 'dashboard':
                return '<svg class="sld-ap-nav_menu-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.888916 8.79012H7.2099V0.888885H0.888916V8.79012ZM0.888916 15.1111H7.2099V10.3704H0.888916V15.1111ZM8.79015 15.1111H15.1111V7.20987H8.79015V15.1111ZM8.79015 0.888885V5.62963H15.1111V0.888885H8.79015Z"/>
                </svg>';
            case 'referrals':
                return '
                    <svg class="sld-ap-nav_menu-icon" width="16" height="11" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.1641 4.72266C11.8125 5.07422 11.3789 5.25 10.8633 5.25C10.3477 5.25 9.90234 5.07422 9.52734 4.72266C9.17578 4.34766 9 3.90234 9 3.38672C9 2.87109 9.17578 2.42578 9.52734 2.05078C9.90234 1.67578 10.3477 1.48828 10.8633 1.48828C11.3789 1.48828 11.8125 1.67578 12.1641 2.05078C12.5391 2.42578 12.7266 2.87109 12.7266 3.38672C12.7266 3.90234 12.5391 4.34766 12.1641 4.72266ZM6.82031 3.84375C6.39844 4.28906 5.87109 4.51172 5.23828 4.51172C4.62891 4.51172 4.10156 4.28906 3.65625 3.84375C3.21094 3.39844 2.98828 2.87109 2.98828 2.26172C2.98828 1.62891 3.21094 1.10156 3.65625 0.679688C4.10156 0.234375 4.62891 0.0117188 5.23828 0.0117188C5.87109 0.0117188 6.39844 0.234375 6.82031 0.679688C7.26562 1.10156 7.48828 1.62891 7.48828 2.26172C7.48828 2.87109 7.26562 3.39844 6.82031 3.84375ZM8.15625 7.32422C9.11719 6.94922 10.0195 6.76172 10.8633 6.76172C11.7305 6.76172 12.6328 6.94922 13.5703 7.32422C14.5312 7.69922 15.0117 8.19141 15.0117 8.80078V10.4883H6.75V8.80078C6.75 8.19141 7.21875 7.69922 8.15625 7.32422ZM5.23828 5.98828C5.75391 5.98828 6.35156 6.05859 7.03125 6.19922C5.83594 6.85547 5.23828 7.72266 5.23828 8.80078V10.4883H0V8.625C0 8.08594 0.316406 7.60547 0.949219 7.18359C1.60547 6.76172 2.32031 6.45703 3.09375 6.26953C3.89062 6.08203 4.60547 5.98828 5.23828 5.98828Z" />
                    </svg>
                ';
            case 'visits':
                return '
                        <svg class="sld-ap-nav_menu-icon" width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.9883 0.238281C12.4102 0.238281 12.7617 0.390625 13.043 0.695312C13.3477 0.976562 13.5 1.32812 13.5 1.75V10.75C13.5 11.1719 13.3477 11.5352 13.043 11.8398C12.7617 12.1211 12.4102 12.2617 11.9883 12.2617H9V10.75H11.9883V3.26172H1.51172V10.75H4.5V12.2617H1.51172C1.08984 12.2617 0.726562 12.1211 0.421875 11.8398C0.140625 11.5352 0 11.1719 0 10.75V1.75C0 1.32812 0.140625 0.976562 0.421875 0.695312C0.726562 0.390625 1.08984 0.238281 1.51172 0.238281H11.9883ZM6.75 4.73828L9.73828 7.76172H7.48828V12.2617H6.01172V7.76172H3.76172L6.75 4.73828Z" />
                        </svg>
                ';
            case 'payouts':
                return '
                        <svg class="sld-ap-nav_menu-icon" width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.75 10.0117V9.23828H5.23828V7.76172H8.26172V6.98828H6.01172C5.80078 6.98828 5.61328 6.91797 5.44922 6.77734C5.30859 6.63672 5.23828 6.46094 5.23828 6.25V4C5.23828 3.78906 5.30859 3.61328 5.44922 3.47266C5.61328 3.33203 5.80078 3.26172 6.01172 3.26172H6.75V2.48828H8.26172V3.26172H9.73828V4.73828H6.75V5.51172H9C9.21094 5.51172 9.38672 5.58203 9.52734 5.72266C9.66797 5.86328 9.73828 6.03906 9.73828 6.25V8.5C9.73828 8.71094 9.66797 8.88672 9.52734 9.02734C9.38672 9.16797 9.21094 9.23828 9 9.23828H8.26172V10.0117H6.75ZM13.5 0.238281C13.9219 0.238281 14.2734 0.390625 14.5547 0.695312C14.8594 0.976562 15.0117 1.32812 15.0117 1.75V10.75C15.0117 11.1719 14.8594 11.5352 14.5547 11.8398C14.2734 12.1211 13.9219 12.2617 13.5 12.2617H1.51172C1.08984 12.2617 0.726562 12.1211 0.421875 11.8398C0.140625 11.5352 0 11.1719 0 10.75V1.75C0 1.32812 0.140625 0.976562 0.421875 0.695312C0.726562 0.390625 1.08984 0.238281 1.51172 0.238281H13.5ZM13.5 10.75V1.75H1.51172V10.75H13.5Z" />
                        </svg>
                ';
            case 'coupons':
                return '
                        <svg class="sld-ap-nav_menu-icon" width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 12.0117V10.5H2.25V12.0117H11.25ZM11.25 8.98828V7.51172H2.25V8.98828H11.25ZM11.25 6V4.48828H2.25V6H11.25ZM0 15.7383V0.761719L1.125 1.88672L2.25 0.761719L3.375 1.88672L4.5 0.761719L5.625 1.88672L6.75 0.761719L7.875 1.88672L9 0.761719L10.125 1.88672L11.25 0.761719L12.375 1.88672L13.5 0.761719V15.7383L12.375 14.6133L11.25 15.7383L10.125 14.6133L9 15.7383L7.875 14.6133L6.75 15.7383L5.625 14.6133L4.5 15.7383L3.375 14.6133L2.25 15.7383L1.125 14.6133L0 15.7383Z" />
                        </svg>
                ';
            case 'creatives':
                return '
                        <svg class="sld-ap-nav_menu-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.0117 11.2383C15.0117 11.6602 14.8594 12.0234 14.5547 12.3281C14.2734 12.6094 13.9219 12.75 13.5 12.75H4.5C4.07812 12.75 3.71484 12.6094 3.41016 12.3281C3.12891 12.0234 2.98828 11.6602 2.98828 11.2383V2.23828C2.98828 1.83984 3.12891 1.5 3.41016 1.21875C3.71484 0.914062 4.07812 0.761719 4.5 0.761719H13.5C13.9219 0.761719 14.2734 0.914062 14.5547 1.21875C14.8594 1.5 15.0117 1.83984 15.0117 2.23828V11.2383ZM6.75 8.25L4.5 11.2383H13.5L10.5117 7.51172L8.26172 10.2891L6.75 8.25ZM0 3.75H1.51172V14.2617H11.9883V15.7383H1.51172C1.08984 15.7383 0.726562 15.5859 0.421875 15.2812C0.140625 15 0 14.6602 0 14.2617V3.75Z" />
                        </svg>
                ';
            case 'urls':
                return '
                <svg class="sld-ap-nav_menu-icon"  width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.9 5C1.9 3.29 3.29 1.9 5 1.9H9V0H5C2.24 0 0 2.24 0 5C0 7.76 2.24 10 5 10H9V8.1H5C3.29 8.1 1.9 6.71 1.9 5ZM6 6H14V4H6V6ZM15 0H11V1.9H15C16.71 1.9 18.1 3.29 18.1 5C18.1 6.71 16.71 8.1 15 8.1H11V10H15C17.76 10 20 7.76 20 5C20 2.24 17.76 0 15 0Z" />
                </svg>
                ';
            case 'settings':
                return '
                    <svg class="sld-ap-nav_menu-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path  d="M12.3353 7.69014L13.8501 8.84038C14.0071 8.97183 14.0408 9.1252 13.951 9.30047L12.5036 11.7324C12.4138 11.9077 12.268 11.9515 12.066 11.8638L10.282 11.1737C9.87803 11.4585 9.4741 11.6886 9.07016 11.8638L8.80087 13.7042C8.77843 13.9014 8.65501 14 8.4306 14H5.5694C5.36743 14 5.24401 13.9014 5.19913 13.7042L4.92984 11.8638C4.50346 11.6886 4.09953 11.4585 3.71803 11.1737L1.93399 11.8638C1.73202 11.9296 1.58616 11.8858 1.49639 11.7324L0.0489618 9.30047C-0.0408015 9.1252 -0.00714026 8.97183 0.149945 8.84038L1.6647 7.69014C1.64226 7.42723 1.63104 7.19718 1.63104 7C1.63104 6.80282 1.64226 6.57277 1.6647 6.30986L0.149945 5.15962C-0.00714026 5.02817 -0.0408015 4.8748 0.0489618 4.69953L1.49639 2.26761C1.6086 2.09233 1.75446 2.04851 1.93399 2.13615L3.71803 2.82629C4.12197 2.54147 4.5259 2.31142 4.92984 2.13615L5.19913 0.295775C5.24401 0.0985915 5.36743 0 5.5694 0H8.4306C8.65501 0 8.77843 0.0985915 8.80087 0.295775L9.07016 2.13615C9.49654 2.31142 9.90047 2.54147 10.282 2.82629L12.066 2.13615C12.268 2.07042 12.4138 2.11424 12.5036 2.26761L13.951 4.69953C14.0408 4.8748 14.0071 5.02817 13.8501 5.15962L12.3353 6.30986C12.3802 6.57277 12.4026 6.80282 12.4026 7C12.4026 7.19718 12.3802 7.42723 12.3353 7.69014ZM5.23279 8.74178C5.72648 9.22379 6.30995 9.46479 6.98317 9.46479C7.67883 9.46479 8.27352 9.22379 8.76721 8.74178C9.26091 8.25978 9.50776 7.67919 9.50776 7C9.50776 6.32081 9.26091 5.74022 8.76721 5.25822C8.27352 4.77621 7.67883 4.53521 6.98317 4.53521C6.30995 4.53521 5.72648 4.77621 5.23279 5.25822C4.73909 5.74022 4.49224 6.32081 4.49224 7C4.49224 7.67919 4.73909 8.25978 5.23279 8.74178Z"/>
                    </svg>
                ';

            default:
                # code...
                break;
        }
        return Validators::str(apply_filters(self::AFFILIATE_PORTAL_TAB_ICON_FILTER, '[]', $tab_key));
    }

    /**
     * @param EnumOptionsReturnType $tab_tuples
     * 
     * @return EnumOptionsReturnType
     */
    public static function get_filtered_tab_tuples($tab_tuples)
    {
        return Validators::enum_options_array(apply_filters(self::AFFILIATE_PORTAL_TABS_FILTER, $tab_tuples));
    }
}
