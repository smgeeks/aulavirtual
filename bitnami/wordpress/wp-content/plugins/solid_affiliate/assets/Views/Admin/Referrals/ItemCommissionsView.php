<?php

namespace SolidAffiliate\Views\Admin\Referrals;

use SolidAffiliate\Lib\Formatters;
use SolidAffiliate\Lib\Integrations\WooCommerceIntegration;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Lib\Translation;
use SolidAffiliate\Lib\URLs;
use SolidAffiliate\Lib\Utils;
use SolidAffiliate\Lib\VO\ItemCommission;
use SolidAffiliate\Views\Shared\SimpleTableView;
use SolidAffiliate\Views\Shared\SolidTooltipView;

class ItemCommissionsView
{

    /**
     * @param ItemCommission[] $item_commissions 
     * @param int $referral_id
     * @param float $total_commission
     * @return string
     */
    public static function render($item_commissions, $referral_id, $total_commission)
    {
        $item_count = count($item_commissions);
        $total_commission_formatted = Formatters::money($total_commission);
        ob_start();
?>
        <style>
            .sld-item-commissions-container {
                min-width: 600px;
            }

            .sld-item-commissions-header {
                font-size: 20px;
                color: #5C5C5C;
                font-weight : 400;
            }

            .sld-item-commissions-primary {
                font-size: 14px;
            }
        </style>
        <div class='sld-item-commissions-container'>
            <div class="sld-item-commissions-header"><strong><?php _e('Referral', 'solid-affiliate') ?> #<?php echo ($referral_id) ?> - <?php _e('How was the commission calculated?', 'solid-affiliate') ?></strong></div>
            <br>
            <div class="sld-item-commissions-primary"><?php echo sprintf(__('This order included <strong>%1$d item(s)</strong> for a total commission of <strong>%2$s</strong>', 'solid-affiliate'), $item_count, $total_commission_formatted) ?></div>
            <br>
            <?php echo (self::render_line_items_container($item_commissions)) ?>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * @param ItemCommission[] $item_commissions
     * @return string
     */
    public static function render_line_items_container($item_commissions)
    {
        if (empty($item_commissions)) {
            return '';
        }
        $tooltip = self::render_commissionable_amount_tooltip();

        $header_rows = Translation::translate_array(['Item', 'Purchase Price', "Commissionable Amount", 'Referral Rate Used', 'Commission']);
        $header_rows[2] .= $tooltip;

        $item_indexes = range(0, count($item_commissions) - 1);
        $body_rows = array_map([self::class, 'item_commission_to_table_row'], $item_indexes, $item_commissions);

        ob_start();
    ?>

        <div class="sld-item-commissions-list-container">
            <?php echo (SimpleTableView::render($header_rows, $body_rows)); ?>
        </div>

<?php
        return ob_get_clean();
    }


    /**
     * @param int $item_index
     * @param ItemCommission $item_commission 
     * 
     * @return array<mixed>
     */
    public static function item_commission_to_table_row($item_index, $item_commission)
    {
        $purchase_price_formatted = Formatters::money($item_commission->purchase_amount);
        $commissionable_amount_formatted = Formatters::money($item_commission->commissionable_amount);

        $commission_amount_formatted = Formatters::money($item_commission->commission_amount);
        $commission_strategy = Formatters::humanize($item_commission->commission_strategy);

        $commission_rate_formatted = Formatters::commission_rate($item_commission->commission_strategy_rate, $item_commission->commission_strategy_rate_type);
        if ($item_commission->commission_strategy_rate_type == 'flat') {
            $commission_rate_formatted .= ' flat';
        }

        $referral_rate_used_string = $commission_strategy . ' (' . $commission_rate_formatted . ')';

        // $item_string = "Item " . ($item_index + 1);

        $item_string = WooCommerceIntegration::product_name_by_id($item_commission->product_id);

        return [$item_string, $purchase_price_formatted, $commissionable_amount_formatted, $referral_rate_used_string, $commission_amount_formatted];
    }


    /**
     * Undocumented function
     *
     * @return string
     */
    public static function render_commissionable_amount_tooltip()
    {
        $general_settings_link = URLs::settings(Settings::TAB_GENERAL);

        $tooltip_body = "
            <div class='sld-tooltip-box'>
                <p class='sld-tooltip_heading'>" . __("Commissionable Amount", 'solid-affiliate') . "</p>
                <p class='sld-tooltip_sub-heading'>" . __("Used to calculate referral commision.", 'solid-affiliate') . " </p>
                <p class='sld-tooltip_body'>" . __("Commissionable Amount takes all deducations and additions such as shipping costs and taxes into account.", 'solid-affiliate') . "</p>
                <p class='sld-tooltip_hint'>" . __("Update commissionable amount in", 'solid-affiliate') . " <a href='{$general_settings_link}'>Settings > General</a></p>
            </div>
        ";

        return SolidTooltipView::render($tooltip_body);
    }
}
