<?php
add_filter('stm_certificates_fields', 'custom_certificate_fields', 99, 1);
function custom_certificate_fields($fields) {
	$fields['curp_user'] = array(
		'name'  => esc_html__( 'CURP', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-CURP-', 'masterstudy-lms-learning-management-system-pro' ),
	);
	$fields['ocupacion_especifica_user'] = array(
		'name'  => esc_html__( 'Ocupación Específica', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Ocupación Específica-', 'masterstudy-lms-learning-management-system-pro' ),
	);
	$fields['puesto_user'] = array(
		'name'  => esc_html__( 'Puesto', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Puesto-', 'masterstudy-lms-learning-management-system-pro' ),
	);
	$fields['rfc_user'] = array(
		'name'  => esc_html__( 'Rfc', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Rfc-', 'masterstudy-lms-learning-management-system-pro' ),
	);
	$fields['nombre_razon_social_user'] = array(
		'name'  => esc_html__( 'Nombre o Razon Social', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Nombre o Razon Social-', 'masterstudy-lms-learning-management-system-pro' ),
	);
	$fields['subject_area'] = array(
		'name'  => esc_html__( 'Area temática del curso', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Area temática del curso-', 'masterstudy-lms-learning-management-system-pro' ),
	);
	$fields['name_agent'] = array(
		'name'  => esc_html__( 'Agente capacitador', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Agente capacitador-', 'masterstudy-lms-learning-management-system-pro' ),
	);
	$fields['duration_course_calculated'] = array(
		'name'  => esc_html__( 'Vigencia', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Vigencia-', 'masterstudy-lms-learning-management-system-pro' ),
	);

	return $fields;
}