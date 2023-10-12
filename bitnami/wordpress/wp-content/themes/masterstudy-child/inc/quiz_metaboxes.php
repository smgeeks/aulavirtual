<?php
add_filter( 'stm_wpcfto_fields', 'quiz_stm_lms_fields', 99, 1 );
function quiz_stm_lms_fields($fields) {
	$fields['stm_quiz_settings']['section_quiz_settings']['fields']['quiz_retake_counter'] = array(
		'type'  => 'number',
		'label' => esc_html__( 'Course evaluation', 'masterstudy-lms-learning-management-system-pro' ),
	);

	return $fields;
}