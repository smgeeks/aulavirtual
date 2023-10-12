<?php
add_filter('stm_lms_email_manager_emails', 'course_user_unsubscribe');
function course_user_unsubscribe($data) {
	$data['stm_lms_user_unsubscribe_course'] = array(
		'section' => 'course',
		'notice'  => esc_html__( 'Unsubscribe user course (for user)', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Unsubscribe User', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Course {{course_title}} unsubscribed.',
		'vars'    => array(
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_name' => esc_html__(
				'Quiz name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'passing_grade' => esc_html__(
				'Passing grade',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
	return $data;
}