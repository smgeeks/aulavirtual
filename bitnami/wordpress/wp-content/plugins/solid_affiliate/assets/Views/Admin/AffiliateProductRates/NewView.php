<?php

namespace SolidAffiliate\Views\Admin\AffiliateProductRates;

use SolidAffiliate\Controllers\AffiliateProductRatesController;
use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Models\AffiliateProductRate;

class NewView
{

    /**
     * @return string
     */
    public static function render()
    {
        $singular = __('Affiliate Product Rate', 'solid-affiliate');
        $form_id = 'affiliate-product-rates-new';
        $schema = AffiliateProductRate::schema();
        $nonce = AffiliateProductRatesController::NONCE_SUBMIT;
        $submit_action = AffiliateProductRatesController::POST_PARAM_SUBMIT;
        $form = FormBuilder::render_crud_form_new($schema, $submit_action, $nonce, $form_id, $singular);
        ob_start();
?>
        <div class="wrap">
            <h1><?php echo sprintf(__('Add New %1$s', 'solid-affiliate'), $singular); ?></h1>
            <?php echo $form ?>
        </div>

<?php
        return ob_get_clean();
    }
}
