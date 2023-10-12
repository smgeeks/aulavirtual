<?php
extract($args);

$_regular_price = get_post_meta($product_id, '_regular_price',true);
$_sale_price = get_post_meta($product_id, '_sale_price',true);

$price = $_sale_price ? $_sale_price :  $_regular_price;

?>

<div class="stm_product__info">
    <div class="stm_product__item stm_product__item--name">
        <?php echo get_the_title($product_id); ?>
    </div>
    <div class="stm_product__item stm_product__item--price">
        <span>$</span>
        <?php echo $price . ' mxn';?>
    </div>
</div>
