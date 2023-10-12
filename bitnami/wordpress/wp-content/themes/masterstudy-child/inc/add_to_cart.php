<?php
wp_dequeue_script('stm-lms-lms');
wp_enqueue_script( 'stm-lms-lms', get_stylesheet_directory_uri().'/assets/js/lms.js', array('jquery'), stm_lms_custom_styles_v(), true );

STM_LMS_Cart_Child::init();

class STM_LMS_Cart_Child extends STM_LMS_Cart {
	public static function init()
	{
		remove_action( 'wp_ajax_stm_lms_add_to_cart', 'STM_LMS_Cart::add_to_cart' );
		remove_action( 'wp_ajax_nopriv_stm_lms_add_to_cart', 'STM_LMS_Cart::add_to_cart' );

		add_action( 'wp_ajax_stm_lms_add_to_cart', 'STM_LMS_Cart_Child::add_to_cart' );
		add_action( 'wp_ajax_nopriv_stm_lms_add_to_cart', 'STM_LMS_Cart_Child::add_to_cart' );
	}

	public static function add_to_cart()
	{
		check_ajax_referer( 'stm_lms_add_to_cart', 'nonce' );

		if ( ! is_user_logged_in() || empty( $_GET['item_id'] ) ) {
			die;
		}

		$item_id = intval( $_GET['item_id'] );
		$user    = STM_LMS_User::get_current_user();
		$user_id = $user['id'];

		$r = parent::_add_to_cart( $item_id, $user_id );

		$the_post = get_post( $item_id );
		$title = get_the_title( $the_post );
		$link = get_permalink( $the_post );
		$post_thumbnail_url = get_the_post_thumbnail_url( $the_post );

		if($title) $r['name'] = $title;
		if($link) $r['link'] = $link;
		if($post_thumbnail_url) $r['img_url'] = $post_thumbnail_url;

		wp_send_json( $r );
	}
}