<?php

namespace SolidAffiliate\Lib;

use SolidAffiliate\Models\Affiliate;

/**
 * Class ShortCodes
 * 
 * The responsibility of this class is to define and register 
 * most of the shortcodes implemented by Solid Affiliate.
 * 
 * NOTES
 * — Affiliate full name
 * — Affiliate portal URL
 * — Affiliate groups lists with commission rates
 * — Affiliate coupons (good addition for the future)
 *  —Cookie duration shortcode
 *  (Just so you can have a dynamic page with all data in sync
 *   if you update settings.. page is automatically updated
 *   global rate etc..
 *   )
 *
 */
class ShortCodes
{
    const CURRENT_AFFILIATE = 'solid_affiliate_current_affiliate';
    const IF_REFERRED_BY_AFFILIATE = 'solid_affiliate_if_referred_by_affiliate';
    const CURRENT_AFFILIATE_LINK = 'solid_affiliate_current_affiliate_link';

    /**
     * @return void
     */
    public static function register_shortcodes()
    {
        add_shortcode(self::CURRENT_AFFILIATE, [self::class, 'current_affiliate']);
        add_shortcode(self::IF_REFERRED_BY_AFFILIATE, [self::class, 'if_referred_by_affiliate']);
        add_shortcode(self::CURRENT_AFFILIATE_LINK, [self::class, 'current_affiliate_link']);
    }

    /**
     * Returns the current affiliate (?)
     * 
     * @param array<string, string>|null $attributes
     * @param string|null $content
     * 
     * @return string
     */
    public static function current_affiliate($attributes = null, $content = null)
    {
        // get 'field' from attributes with a default value of 'full_name'
        $field = $attributes['field'] ?? 'full_name';

        $maybe_user_id = Misc::get_user_id_of_affiliate_who_referred_the_current_visitor(0);
        $maybe_wp_user = get_user_by('id', $maybe_user_id);

        if ($maybe_wp_user instanceof \WP_User) {
            $affiliate = Affiliate::find_where(['user_id' => $maybe_user_id]);
            if ($affiliate instanceof Affiliate) {
                switch ($field) {
                    case 'full_name':
                        return $affiliate->first_name . ' ' . $affiliate->last_name;
                    case 'username':
                        return $maybe_wp_user->user_login;
                    default:
                        return $affiliate->first_name . ' ' . $affiliate->last_name;
                }
            }

            return $maybe_wp_user->user_login;
        } else {
            return 'NO AFFILIATE';
        }
    }

    /**
     * This shortcode will only display the content if the current visitor was referred by an affiliate.
     * 
     * @param array<string, string>|null $attributes
     * @param string|null $content
     * 
     * @return string
     */
    public static function if_referred_by_affiliate($attributes = null, $content = null)
    {
        $maybe_user_id = Misc::get_user_id_of_affiliate_who_referred_the_current_visitor(0);
        if ($maybe_user_id > 0) {
            return do_shortcode($content ?? '');
        } else {
            return '';
        }
    }

    /**
     * Checks to see if the current user is an affiliate. If they are, it returns the default affiliate link for them. Otherwise, it returns an empty string.
     * 
     * @param array<string, string>|null $attributes
     * @param string|null $content
     *
     * @return string
     */
    public static function current_affiliate_link($attributes = null, $content = null)
    {
        $maybe_current_affiliate = Affiliate::for_user_id(get_current_user_id());

        if ($maybe_current_affiliate instanceof Affiliate) {
            return Affiliate::default_affiliate_link_for($maybe_current_affiliate);
        } else {
            return '';
        }

    }


}
