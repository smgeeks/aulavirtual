<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */
defined('ABSPATH') || exit;

$product_id = [];
// Проверяем, что объект заказа существует
if ($order && is_a($order, 'WC_Order')) {
    // Получаем массив элементов заказа
    $items = $order->get_items();

    // Обходим элементы заказа и получаем ID продукта
    foreach ($items as $item) {
        $product_id[] = $item->get_product_id();
    }
} else {
    // Обработка случая, если объект заказа не был найден
    echo 'Order not found or is not a valid WC_Order object.';
}

$item_id = [];
foreach ($product_id as $course) {
    $item_id[] = get_post_meta($course, 'stm_lms_product_id', true);
}

$post_attribute = get_post_meta(intval($item_id), 'attribute', true);


?>

<div class="stm_order__woo" style="margin-top: -300px;">
    <h3 class="stm_order__">!Felicidades¡</h3>
    <h3 class="stm_order_title">Ingresa ahora a tus cursos</h3>
    <div class="stm__order">
        <div class="stm_order_item">
            <div class="stm_order_header">
                <img src="/wp-content/themes/masterstudy-child/assets/image/estudiante.png" alt="">
                <p>
                    !Tu compra ga sido realizada de manera exirosal Ya <br> puedes comenzar a aprender.
                </p>
            </div>
            <?php
            foreach ($item_id as $course_id) {

                get_template_part('parts/order_loop', 'loop', array('course_id' => $course_id));
            }
            ?>

        </div>
        <?php
        if ($order) {

            $date_created = $order->get_date_created();
            $formatted_date = $date_created->format('d-M-Y'); // Форматируем дату как "15-oct-2023"
            // Преобразуем первую букву месяца в верхний регистр
            $formatted_date = ucfirst($formatted_date);


            ?>
            <div class="stm_order_item stm_order_item__factura">
                <h3 class="stm_factura_title">Factura</h3>
                <div class="stm_factura__total">
                    <span><?php echo $order->get_formatted_order_total(); ?> mxn</span>
                    <!--                <span>$3756 mxn</span>-->
                </div>

                <div class="stm_facture_ticket">
                    <div class="stm_facture_tick_num">
                        <p>Ticket</p>
                        <strong><?php echo $order->get_order_number(); ?></strong>
                    </div>
                    <div class="stm_facture_tick_date">
                        <p>Fecha</p>
                        <strong><?php echo $formatted_date; ?></strong>
                    </div>
                </div>

                <div class="stm_product__title">
                    <p class="stm_product_title">Producto</p>
                    <p class="stm_product_title">Precio</p>
                </div>

                <div>
                    <?php
                    foreach ($product_id as $product_id_s) {
                        get_template_part('parts/s_product_loop', 'product_loop', array('product_id' => $product_id_s));
                    }
                    ?>
                </div>

                <div class="stm_subtotal_info">
                    <div class="stm_subtotal_item">
                        <p class="stm_subtotal_title">Subtotal</p>
                        <p class="stm_subtotal_title">IVA</p>
                        <p class="stm_subtotal_title">Total</p>
                    </div>
                    <div class="stm_subtotal_item">
                        <?php
                        $subTotal = 0;
                        foreach ($product_id as $product_id_s) {
                            $subTotal += get_post_meta($product_id_s, '_regular_price', true);
                        }

                        $iva = ($subTotal / 100) * 16;
                        $subTotal = $subTotal - $iva;
                        ?>
                        <p class="stm_subtotal_price">$<?php echo $subTotal ?></p>
                        <p class="stm_subtotal_price">$<?php echo $iva ?></p>
                        <p class="stm_subtotal_price"><?php echo $order->get_formatted_order_total(); ?></p>
                    </div>
                </div>

                <div class="stm_order_download">
                     <a href="/facturacion/" class="order_download_btn"> 
                         <i class="fa fa-file-download"></i> 
                        Descargar Factura 
                  </a> 

                    
                </div>

            </div>
        <?php } ?>
    </div>
</div>



