<?php

function stm_lms_settings_profiles_section() {
	$pages           = WPCFTO_Settings::stm_get_post_type_array( 'page' );
	$submenu_general = esc_html__( 'General', 'masterstudy-lms-learning-management-system' );

	$general_fields = array(
		'user_premoderation'               => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Enable email confirmation', 'masterstudy-lms-learning-management-system' ),
			'hint'    => esc_html__( 'All new registered users will get an e-mail for account verification', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
		),
		'register_as_instructor'           => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Disable Instructor Registration', 'masterstudy-lms-learning-management-system' ),
			'hint'    => esc_html__( 'Remove checkbox "Register as instructor" from registration', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
			'value'   => false,
		),
		'disable_instructor_premoderation' => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Disable Instructor Pre-moderation', 'masterstudy-lms-learning-management-system' ),
			'hint'    => esc_html__( 'Set user role "instructor" automatically', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
			'value'   => false,
		),
		'instructor_can_add_students'      => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Add students to own course', 'masterstudy-lms-learning-management-system' ),
			'hint'    => esc_html__( 'Instructor will have a tool in an account to add student by email to course', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
		),
		'pro_banner'                       => array(
			'type'    => ( STM_LMS_Helpers::is_pro() ) ? 'pro_empty_banner' : 'pro_banner',
			'label'   => esc_html__( 'Course Pre-moderation', 'masterstudy-lms-learning-management-system' ),
			'img'     => STM_LMS_URL . 'assets/img/pro-features/course-premoderation.png',
			'desc'    => esc_html__( 'This will help you maintain quality control and student confidence. Courses from instructors will need admin approval before their publication.', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
			'hint'    => esc_html__( 'Enable', 'masterstudy-lms-learning-management-system' ),
		),
		'instructors_page'                 => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Instructors Archive Page', 'masterstudy-lms-learning-management-system' ),
			'options' => $pages,
			'submenu' => $submenu_general,
		),
		'cancel_subscription'              => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Cancel subscription page', 'masterstudy-lms-learning-management-system' ),
			'options' => $pages,
			'hint'    => esc_html__( 'If you want to display link to Cancel Subscription page, choose page and add to page content shortcode [pmpro_cancel].', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
		),
		'float_menu'                       => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Side Profile Menu', 'masterstudy-lms-learning-management-system' ),
			'value'   => false,
			'submenu' => $submenu_general,
			'hint'    => esc_html__( 'Moves the Profile Menu to the left or right side.', 'masterstudy-lms-learning-management-system' ),
		),
		'float_menu_guest'                 => array(
			'type'       => 'checkbox',
			'label'      => esc_html__( 'Side Profile Menu for Guest Users', 'masterstudy-lms-learning-management-system' ),
			'value'      => true,
			'dependency' => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'    => $submenu_general,
		),
		'float_menu_position'              => array(
			'type'       => 'select',
			'label'      => esc_html__( 'Side Profile Menu Position', 'masterstudy-lms-learning-management-system' ),
			'options'    => array(
				'left'  => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
				'right' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
			),
			'value'      => 'left',
			'dependency' => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'    => $submenu_general,
		),
		/*GROUP STARTED*/
		'float_background_color'           => array(
			'group'       => 'started',
			'type'        => 'color',
			'label'       => esc_html__( 'Background color', 'masterstudy-lms-learning-management-system' ),
			'columns'     => '33',
			'group_title' => esc_html__( 'Side Profile Menu Colors', 'masterstudy-lms-learning-management-system' ),
			'dependency'  => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'     => $submenu_general,
		),
		'float_text_color'                 => array(
			'group'      => 'ended',
			'type'       => 'color',
			'label'      => esc_html__( 'Text color', 'masterstudy-lms-learning-management-system' ),
			'columns'    => '33',
			'dependency' => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'    => $submenu_general,
		),
	);

	if ( STM_LMS_Helpers::is_pro() ) {
		$course_moderation_field = array(
			'course_premoderation' => array(
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable Course Pre-moderation', 'masterstudy-lms-learning-management-system' ),
				'hint'    => esc_html__( 'Course will have Pending status, until you approve it', 'masterstudy-lms-learning-management-system' ),
				'pro'     => true,
				'pro_url' => admin_url( 'admin.php?page=stm-lms-go-pro' ),
				'submenu' => $submenu_general,
			),
		);

		array_splice( $general_fields, 4, 0, $course_moderation_field );
	}

	return array(
		'name'   => esc_html__( 'Profiles', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'Profiles Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'fa fa-user-circle',
		'fields' => $general_fields + stm_lms_settings_sorting_the_menu_section(),
	);
}
