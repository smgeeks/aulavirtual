<?php
function incluir_estilos() {
    // Estilos del tema padre
    wp_enqueue_style( 'masterstudy', get_template_directory_uri() . '/style.css' );

    // Estilos del tema hijo
    wp_enqueue_style( 'masterstudy-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'masterstudy' ),
    '2.0'
    );
}
add_action( 'wp_enqueue_scripts', 'incluir_estilos' );

function estilos_personalizados() {
	wp_enqueue_style('style-hub', get_stylesheet_directory_uri().'/css/style-hub.css');

}
add_action('wp_enqueue_scripts', 'estilos_personalizados');

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script( 'stm-date', get_stylesheet_directory_uri().'/assets/js/form_builder/components/fields/date.js', array('jquery'), stm_lms_custom_styles_v(), true );

});

$inc_path = get_stylesheet_directory() . '/inc';
require_once $inc_path .'/quiz_metaboxes.php';
require_once $inc_path .'/quiz.php';
require_once $inc_path .'/course_save.php';
require_once $inc_path .'/add_to_cart.php';
require_once $inc_path .'/member_content.php';
require_once $inc_path .'/certificate_builder.php';
require_once $inc_path .'/generate_certificate.php';
require_once $inc_path .'/email_manager.php';
require_once $inc_path .'/attributes.php';
require_once $inc_path .'/WC_Order_Child.php';


add_filter( 'stm_lms_form_builder_available_fields', function ($fields) {
    $fields[] = array(
        'type'       => 'date',
        'field_name' => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
    );
    return $fields;
});



if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}
