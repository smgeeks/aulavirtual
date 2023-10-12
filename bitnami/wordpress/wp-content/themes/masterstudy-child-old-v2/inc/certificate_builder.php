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
    $fields['course_title_for_certification'] = array(
		'name'  => esc_html__( 'Título del curso para certificación', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Título del curso para certificación-', 'masterstudy-lms-learning-management-system-pro' ),
	);
    $fields['start_date_day'] = array(
		'name'  => esc_html__( 'Start Day', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Start Day-', 'masterstudy-lms-learning-management-system-pro' ),
	);
    $fields['start_date_month'] = array(
		'name'  => esc_html__( 'Start Month', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Start Month-', 'masterstudy-lms-learning-management-system-pro' ),
	);
    $fields['start_date_years'] = array(
		'name'  => esc_html__( 'Start Year', 'masterstudy-lms-learning-management-system-pro' ),
		'value' => esc_html__( '-Start Year-', 'masterstudy-lms-learning-management-system-pro' ),
	);

    $fields['end_date_day'] = array(
        'name'  => esc_html__( 'End Day', 'masterstudy-lms-learning-management-system-pro' ),
        'value' => esc_html__( '-End Day-', 'masterstudy-lms-learning-management-system-pro' ),
    );
    $fields['end_date_month'] = array(
        'name'  => esc_html__( 'End Month', 'masterstudy-lms-learning-management-system-pro' ),
        'value' => esc_html__( '-End Month-', 'masterstudy-lms-learning-management-system-pro' ),
    );
    $fields['end_date_years'] = array(
        'name'  => esc_html__( 'End Year', 'masterstudy-lms-learning-management-system-pro' ),
        'value' => esc_html__( '-End Year-', 'masterstudy-lms-learning-management-system-pro' ),
    );

	return $fields;
}


