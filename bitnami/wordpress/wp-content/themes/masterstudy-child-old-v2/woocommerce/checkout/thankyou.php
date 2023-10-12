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

$product_id = '';
// Проверяем, что объект заказа существует
if ($order && is_a($order, 'WC_Order')) {
    // Получаем массив элементов заказа
    $items = $order->get_items();

    // Обходим элементы заказа и получаем ID продукта
    foreach ($items as $item) {
        $product_id = $item->get_product_id();
    }
} else {
    // Обработка случая, если объект заказа не был найден
    echo 'Order not found or is not a valid WC_Order object.';
}

$item_id = get_post_meta($product_id, 'stm_lms_product_id', true);
$post_attribute = get_post_meta(intval($item_id), 'attribute', true);


?>
<h2 class="hs_cabezalThanks">¡GRACIAS POR TU COMPRA!</h2>
    <div class="woocommerce-order">
        <?php
        if ($order) :
            do_action('woocommerce_before_thankyou', $order->get_id());
            ?>
            <?php if ($order->has_status('failed')) : ?>
            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>
            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
                   class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                       class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
                <?php endif; ?>
            </p>
        <?php else : ?>
            <p class="woocommerce-notice woocommerce-notice–success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Revisa los detalles de tu compra y accede al curso', 'woocommerce'), $order); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
            <!-- <p class="thankyou-note"> Please note that delivery times are currently estimated at 7-10 business days for U.S. delivery. Orders outside the United States may take two weeks to 30 days to be delivered and may experience delays due to customs or other current policies that are out of our control. If you have any questions about your order, please contact us at customerservice@yourstore.com.</p> -->

            <h3>Detalles de la compra</h3>

            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
                <li class="woocommerce-order-overview__order order">
                    <?php esc_html_e('Order number:', 'woocommerce'); ?>
                    <strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </li>
                <li class="woocommerce-order-overview__date date">
                    <?php esc_html_e('Date:', 'woocommerce'); ?>
                    <strong><?php echo wc_format_datetime($order->get_date_created()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </li>
                <?php if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()) : ?>
                    <li class="woocommerce-order-overview__email email">
                        <?php esc_html_e('Email:', 'woocommerce'); ?>
                        <strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                    </li>
                <?php endif; ?>
                <?php if (!empty($post_attribute)) { ?>
                    <li>
                        <?php esc_html_e('Course attribute:', 'woocommerce'); ?>
                        <strong><?php echo wp_kses_post($post_attribute); ?></strong>
                    </li>

                <?php } ?>
                <li class="woocommerce-order-overview__total total">
                    <?php esc_html_e('Total:', 'woocommerce'); ?>
                    <strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </li>

                <?php if ($order->get_payment_method_title()) : ?>
                    <li class="woocommerce-order-overview__payment-method method">
                        <?php esc_html_e('Payment method:', 'woocommerce'); ?>
                        <strong><?php echo wp_kses_post($order->get_payment_method_title()); ?></strong>
                    </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

            <a href="<?php echo home_url(); ?>/user-account/enrolled-courses" class="hs_btnCursos"> Ir a tus Cursos</a>
            <?php //do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
            ?>
            <?php //do_action( 'woocommerce_thankyou', $order->get_id() );
            ?>
        <?php else : ?>
            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Thank you. Your order has been received.', 'woocommerce'), null); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
        <?php endif; ?>
    </div>
