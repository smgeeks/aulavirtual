<?php

namespace SolidAffiliate\Views\Admin\AffiliateProductRates;

use SolidAffiliate\Controllers\AffiliateProductRatesController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Models\AffiliateProductRate;

class EditView
{
    /**
     * @param object $item
     * @return string
     */
    public static function render($item)
    {
        $singular = __('Affiliate Product Rate', 'solid-affiliate');
        $form_id = 'affiliate-product-rates-edit';
        $schema = AffiliateProductRate::schema();
        $nonce = AffiliateProductRatesController::NONCE_SUBMIT;
        $submit_action = AffiliateProductRatesController::POST_PARAM_SUBMIT;

        $form = FormBuilder::render_crud_form_edit($schema, $submit_action, $nonce, $form_id, $singular, $item);
        ob_start();
?>

        <div class="wrap">
            <h1><?php echo sprintf(__('Update %1$s', 'solid-affiliate'), $singular); ?></h1>
            <?php echo $form ?>
        </div>
<?php
        return ob_get_clean();
    }
}
