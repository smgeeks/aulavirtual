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

$inc_path = get_stylesheet_directory() . '/inc';
require_once $inc_path .'/quiz_metaboxes.php';
require_once $inc_path .'/quiz.php';
require_once $inc_path .'/course_save.php';
require_once $inc_path .'/add_to_cart.php';
require_once $inc_path .'/member_content.php';
require_once $inc_path .'/certificate_builder.php';
require_once $inc_path .'/generate_certificate.php';
require_once $inc_path .'/email_manager.php';