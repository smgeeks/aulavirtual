<?php
function masterstudy_lms_google_meet_settings_page( $setups ) {
	$fields = masterstudy_lms_google_meet_config_passed()
		? array(
			'credentials' => array(
				'name'   => esc_html__( 'Credentials', 'masterstudy-lms-learning-management-system-pro' ),
				'label'  => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system-pro' ),
				'fields' => array(
					'stm_gm_hidden_links'    => array(
						'type' => 'meet_intro_links',
					),
					'stm_gm_hidden'          => array(
						'type' => 'stm_credentials',
					),
					'stm_sync_hidden'        => array(
						'type' => 'stm_sync_meets',
					),
					'stm_gm_timezones'       => array(
						'type'        => 'select',
						'label'       => esc_html__( 'Default timezone', 'masterstudy-lms-learning-management-system-pro' ),
						'description' => esc_html__( 'Set the default timezone for Google Meet', 'masterstudy-lms-learning-management-system-pro' ),
						'options'     => stm_lms_get_timezone_options(),
						'value'       => masterstudy_lms_get_current_timezone(),
					),
					'stm_gm_minute_reminder' => array(
						'type'        => 'number',
						'label'       => esc_html__( 'Default reminder time (minutes)', 'masterstudy-lms-learning-management-system-pro' ),
						'description' => esc_html__( 'Set a default reminder time to get an email notification', 'masterstudy-lms-learning-management-system-pro' ),
						'value'       => 30,
					),
					'stm_gm_send_updates'    => array(
						'type'        => 'select',
						'label'       => esc_html__( 'Send updates', 'masterstudy-lms-learning-management-system-pro' ),
						'value'       => 'all',
						'options'     => array(
							'all'          => 'All',
							'externalOnly' => 'ExternalOnly',
							'none'         => 'None',
						),
						'description' => esc_html__( 'Select how to send notifications about the creation of the new event. Note that some emails might still be sent.', 'masterstudy-lms-learning-management-system-pro' ),
					),
				),
			),
		)
		: array();

	$setups[] = array(
		'page'        => array(
			'parent_slug' => 'stm-lms-settings',
			'page_title'  => esc_html__( 'Google Meet', 'masterstudy-lms-learning-management-system-pro' ),
			'menu_title'  => esc_html__( 'Google Meet', 'masterstudy-lms-learning-management-system-pro' ),
			'menu_slug'   => 'google_meet_settings',
		),
		'fields'      => apply_filters( 'masterstudy_lms_google_meet_settings', $fields ),
		'option_name' => 'stm_lms_google_meet_settings',
	);

	return $setups;
}
add_filter( 'wpcfto_options_page_setup', 'masterstudy_lms_google_meet_settings_page' );

function masterstudy_lms_google_meet_boxes( $boxes ) {
	$boxes['stm_google_meet_single'] = array(
		'post_type' => array( 'stm-google-meets' ),
		'label'     => esc_html__( 'Google Meeting Fields', 'masterstudy-lms-learning-management-system-pro' ),
	);

	return $boxes;
}
add_filter( 'stm_wpcfto_boxes', 'masterstudy_lms_google_meet_boxes' );

function masterstudy_lms_google_meet_fields( $fields ) {
	$fields['stm_lesson_settings']['section_lesson_settings']['fields']['type']['options']['stream'] = esc_html__( 'Stream', 'masterstudy-lms-learning-management-system-pro' );
	$default_gm_settings              = get_option( 'stm_lms_google_meet_settings', true );
	$fields['stm_google_meet_single'] = array(
		'section_google_meet_single' => array(
			'name'   => esc_html__( 'Curriculum', 'masterstudy-lms-learning-management-system-pro' ),
			'fields' => array(
				'stm_gma_summary'    => array(
					'type'  => 'textarea',
					'label' => esc_html__( 'Meeting Summary', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'stm_gma_start_date' => array(
					'type'  => 'date',
					'label' => esc_html__( 'Meeting start date', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'stm_gma_start_time' => array(
					'type'  => 'time',
					'label' => esc_html__( 'Meeting start time', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'stm_gma_end_date'   => array(
					'type'  => 'date',
					'label' => esc_html__( 'Meeting end date', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'stm_gma_end_time'   => array(
					'type'  => 'time',
					'label' => esc_html__( 'Meeting end time', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'stm_gma_timezone'   => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Meeting timezone', 'masterstudy-lms-learning-management-system-pro' ),
					'options' => stm_lms_get_timezone_options(),
					'value'   => $default_gm_settings['stm_gm_timezones'] ?? 'UTC',
				),
				'stm_gma_visibility' => array(
					'type'        => 'select',
					'label'       => esc_html__( 'Event Visibility', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( 'Visibility of the Google Calendar event', 'masterstudy-lms-learning-management-system-pro' ),
					'options'     => masterstudy_lms_google_meet_visibility_types(),
					'value'       => 'public',
				),
			),
		),
	);

	return $fields;
}
add_filter( 'stm_wpcfto_fields', 'masterstudy_lms_google_meet_fields' );

function masterstudy_lms_google_meet_admin_enqueue_scripts() {
	if ( masterstudy_lms_is_google_meet_page() && ! masterstudy_lms_google_meet_config_passed() ) {
		wp_enqueue_style( 'stm-lms-google-meet', STM_LMS_URL . 'assets/css/parts/google_meet/google-meet.css', array(), STM_LMS_PRO_VERSION );
		wp_enqueue_script( 'stm-lms-google-meet', STM_LMS_PRO_URL . 'assets/js/google-meet/google_meet.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
		wp_localize_script(
			'stm-lms-google-meet',
			'stm_google_meet_ajax_variable',
			array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'stm-lms-gm-nonce' ),
			)
		);
	}
}
add_filter( 'admin_enqueue_scripts', 'masterstudy_lms_google_meet_admin_enqueue_scripts' );

function masterstudy_lms_load_google_meet_form_template() {
	if ( masterstudy_lms_is_google_meet_page() && ! masterstudy_lms_google_meet_config_passed() ) {
		load_template( __DIR__ . '/components/config-form.php' );
	}
}
add_action( 'admin_footer', 'masterstudy_lms_load_google_meet_form_template' );

add_filter(
	'wpcfto_field_stm_credentials',
	function () {
		return __DIR__ . '/components/credentials-status/fields/credentials.php';
	}
);

add_filter(
	'wpcfto_field_stm_sync_meets',
	function () {
		return __DIR__ . '/components/sync-meetings/fields/sync-meetings.php';
	}
);

add_filter(
	'wpcfto_field_meet_intro_links',
	function () {
		return __DIR__ . '/components/meet-intro-links/fields/meet-intro-links.php';
	}
);
