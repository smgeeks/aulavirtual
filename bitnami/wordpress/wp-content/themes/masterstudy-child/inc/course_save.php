<?php

STM_LMS_Manage_Course_Child::init();

class STM_LMS_Manage_Course_Child extends STM_LMS_Manage_Course {
	public static function init() {
		remove_action( 'wp_ajax_stm_lms_pro_save_front_course', 'STM_LMS_Manage_Course::save_course' );

		add_action( 'wp_ajax_stm_lms_pro_save_front_course', 'STM_LMS_Manage_Course_Child::save_course' );

		add_filter( 'stm_wpcfto_fields', 'STM_LMS_Manage_Course_Child::course_stm_lms_fields', 10, 1 );
	}

	public static function save_course() {

		check_ajax_referer( 'stm_lms_pro_save_front_course', 'nonce' );

		$validation = new Validation();

		$required_fields = apply_filters(
			'stm_lms_manage_course_required_fields',
			array(
				'title'      => 'required',
				'category'   => 'required',
				'image'      => 'required|integer',
				'content'    => 'required',
				'price'      => 'float',
				'curriculum' => 'required',
			)
		);

		$validation->validation_rules( $required_fields );

		$validation->filter_rules(
			array(
				'title'                      => 'trim|sanitize_string',
				'category'                   => 'trim|sanitize_string',
				'image'                      => 'sanitize_numbers',
				'content'                    => 'trim',
				'price'                      => 'sanitize_floats',
				'sale_price'                 => 'sanitize_floats',
				'curriculum'                 => 'sanitize_string',
				'duration'                   => 'sanitize_string',
				'video'                      => 'sanitize_string',
				'prerequisites'              => 'sanitize_string',
				'prerequisite_passing_level' => 'sanitize_floats',
				'enterprise_price'           => 'sanitize_floats',
				'co_instructor'              => 'sanitize_floats',
			)
		);

		$validated_data = $validation->run( $_POST );

		if ( false === $validated_data ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => $validation->get_readable_errors( true ),
				)
			);
		}

		$user = STM_LMS_User::get_current_user();

		do_action( 'stm_lms_pro_course_data_validated', $validated_data, $user );

		$is_updated = ( ! empty( $validated_data['post_id'] ) );

		$course_id = parent::create_course( $validated_data, $user, $is_updated );

		self::update_course_meta( $course_id, $validated_data );

		parent::update_course_category( $course_id, $validated_data );

		parent::update_course_image( $course_id, $validated_data );

		do_action( 'stm_lms_pro_course_added', $validated_data, $course_id, $is_updated );

		$course_url = get_the_permalink( $course_id );

		wp_send_json(
			array(
				'status'  => 'success',
				'message' => esc_html__( 'Course Saved, redirecting...', 'masterstudy-lms-learning-management-system-pro' ),
				'url'     => $course_url,
			)
		);

	}

	public static function update_course_meta( $course_id, $data ) {
		/*Update Course Post Meta*/
		$post_metas = array(
			'price',
			'sale_price',
			'curriculum',
			'faq',
			'announcement',
			'duration_info',
			'level',
			'prerequisites',
			'prerequisite_passing_level',
			'enterprise_price',
			'co_instructor',
			'course_files_pack',
			'video_duration',
			'subject_area',
			'name_agent',
			'duration_course'
		);

		foreach ( $post_metas as $post_meta_key ) {
			if ( isset( $data[ $post_meta_key ] ) ) {
				update_post_meta( $course_id, $post_meta_key, $data[ $post_meta_key ] );
			}
		}

	}

	public static function course_stm_lms_fields( $fields ) {
		$fields['stm_courses_settings']['section_settings']['fields']['subject_area'] = array(
			'type'  => 'text',
			'label' => esc_html__( 'Area tematica del curso', 'masterstudy-lms-learning-management-system-pro' ),
		);
		$fields['stm_courses_settings']['section_settings']['fields']['name_agent'] = array(
			'type'  => 'text',
			'label' => esc_html__( 'Nombre de agente capacitador', 'masterstudy-lms-learning-management-system-pro' ),
		);
		$fields['stm_courses_settings']['section_settings']['fields']['duration_course'] = array(
			'type'  => 'number',
			'label' => esc_html__( 'Tiempo de vigencia', 'masterstudy-lms-learning-management-system-pro' ),
		);

		return $fields;
	}
}