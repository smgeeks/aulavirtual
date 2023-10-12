<?php
function stm_eroom_general_admin_notice() {
	$settings = get_option( 'stm_zoom_settings', array() );
	if ( get_admin_page_parent() == 'stm_zoom' ) {
		if ( empty( $settings['sdk_key'] ) || empty( $settings['sdk_secret'] ) ) {
			$init_data = array(
				'notice_type'          => 'animate-triangle-notice only-title',
				'notice_logo'          => 'attent_triangle.svg',
				'notice_title'         => esc_html__( 'Please add Meeting SDK to integrate Zoom Client functionalities and make Join In Browser work', 'eroom-zoom-meetings-webinar' ),
			);

			stm_admin_notices_init( $init_data );
		}
		if ( empty( $settings['auth_account_id'] ) && empty( $settings['auth_client_id'] ) && empty( $settings['auth_client_secret'] ) ) {
			$init_data = array(
				'notice_type'          => 'animate-triangle-notice without-btn',
				'notice_logo'          => 'attent_triangle.svg',
				'notice_title'         => esc_html__( 'Zoom is deprecating their JWT app from June of 2023 and until the deadline all your current APIs will work.', 'eroom-zoom-meetings-webinar' ),
				'notice_desc'          => sprintf( 'Please see <a href="https://marketplace.zoom.us/docs/guides/build/jwt-app/jwt-faq/#jwt-app-type-deprecation-faq--omit-in-toc-" target="_blank">%1s</a> %2s <a href="#" class="eroom-migration-wizard" >%3s</a> %4s', esc_html__( 'JWT App Type Depreciation FAQ', 'eroom-zoom-meetings-webinar' ), esc_html__( 'for more details, It is recommended to run the', 'eroom-zoom-meetings-webinar' ), esc_html__( 'migration wizard', 'eroom-zoom-meetings-webinar' ), esc_html__( 'in easy steps to smooth the transition to the new Server to Server OAuth system.', 'eroom-zoom-meetings-webinar' ) ),
			);

			stm_admin_notices_init( $init_data );
		} else if ( empty( $settings['auth_account_id'] ) || empty( $settings['auth_client_id'] ) || empty( $settings['auth_client_secret'] ) ) {
			$init_data = array(
				'notice_type'          => 'animate-triangle-notice only-title',
				'notice_logo'          => 'attent_triangle.svg',
				'notice_title'         => esc_html__( 'Please complete all OAuth fields', 'eroom-zoom-meetings-webinar' ),
			);

			stm_admin_notices_init( $init_data );
		}
	}

	if ( get_admin_page_parent() == 'stm_zoom_pro' ) {
		if ( empty( $settings['auth_account_id'] ) && empty( $settings['auth_client_id'] ) && empty( $settings['auth_client_secret'] ) ) {
			$init_data = array(
				'notice_type'          => 'animate-triangle-notice without-btn',
				'notice_logo'          => 'attent_triangle.svg',
				'notice_title'         => esc_html__( 'Zoom is deprecating their JWT app from June of 2023 and until the deadline all your current APIs will work.', 'eroom-zoom-meetings-webinar' ),
				'notice_desc'          => sprintf( 'Please see <a href="https://marketplace.zoom.us/docs/guides/build/jwt-app/jwt-faq/#jwt-app-type-deprecation-faq--omit-in-toc-" target="_blank">%1s</a> %2s <a href="#" class="eroom-migration-wizard" >%3s</a> %4s', esc_html__( 'JWT App Type Depreciation FAQ', 'eroom-zoom-meetings-webinar' ), esc_html__( 'for more details, It is recommended to run the', 'eroom-zoom-meetings-webinar' ), esc_html__( 'migration wizard', 'eroom-zoom-meetings-webinar' ), esc_html__( 'in easy steps to smooth the transition to the new Server to Server OAuth system.', 'eroom-zoom-meetings-webinar' ) ),
			);

			stm_admin_notices_init( $init_data );
		}
	}
}

add_action( 'admin_init', 'stm_eroom_general_admin_notice' );
$settings = get_option( 'stm_zoom_settings', array() );

if ( ! empty( $settings ) && ( empty( $settings['auth_account_id'] ) && empty( $settings['auth_client_id'] ) && empty( $settings['auth_client_secret'] ) ) ) {
	Migration::get_instance();
}
