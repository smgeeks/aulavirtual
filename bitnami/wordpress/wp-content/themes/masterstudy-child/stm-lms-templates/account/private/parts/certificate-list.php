<?php
/**
 * @var $current_user
 */



stm_lms_register_style( 'user-quizzes' );
stm_lms_register_style( 'user-certificates' );
$completed = stm_lms_get_user_completed_courses( $current_user['id'], array( 'user_course_id', 'course_id' ), -1 );

stm_lms_register_script( 'affiliate_points' );

stm_lms_register_style( 'affiliate_points' );
$forms = get_option('stm_lms_form_builder_forms', array());
$choices = $forms[2]['fields'][10]['choices'];
$current_user_id = get_current_user_id();
$current_user = wp_get_current_user();
$user_fullname =  $current_user->user_firstname.' '.$current_user->user_lastname;
$user_firstname = $current_user->user_firstname;
$user_lastname = $current_user->user_lastname;

$user_curp = get_user_meta($current_user_id, '8g2bnvxu3mt', true);
$user_puesta = get_user_meta($current_user_id, 'tnk9e6yw90a', true);
$user_ocupacion = get_user_meta($current_user_id, '7f8lxabexc9', true);
$user_nombre_razon = get_user_meta($current_user_id, '79p8r64z51r', true); 
$user_rfc = get_user_meta($current_user_id, '2h9dxub26gw', true);
$subject_area = get_user_meta($current_user_id, 'subject_area', true);


if ( ! empty( $completed ) ) { ?>
	<?php
	if ( class_exists( 'STM_LMS_Certificate_Builder' ) ) {
		wp_register_script( 'jspdf', STM_LMS_URL . '/assets/vendors/jspdf.umd.js', array(), stm_lms_custom_styles_v() );
		wp_enqueue_script( 'stm_generate_certificate', get_stylesheet_directory_uri() . '/assets/js/certificate_builder/generate_certificate.js', array( 'jspdf', 'stm_certificate_fonts' ), stm_lms_custom_styles_v() );
	}
	?>
	<div class="stm-lms-user-quizzes stm-lms-user-certificates">

		<h2 class="stm-lms-account-title">
			<?php esc_html_e( 'My Certificates', 'masterstudy-lms-learning-management-system' ); ?>
		</h2>

		<div class="multiseparator"></div>

		<div class="stm-lms-user-quiz__head heading_font">
			<div class="stm-lms-user-quiz__head_title">
				<?php esc_html_e( 'Course', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
			<div class="stm-lms-user-quiz__head_status">
				<?php esc_html_e( 'Certificate', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
		</div>

		<?php
		foreach ( $completed as $course ) :
			$code = STM_LMS_Certificates::stm_lms_certificate_code( $course['user_course_id'], $course['course_id'] );
			?>
			<div class="stm-lms-user-quiz">
				<div class="stm-lms-user-quiz__title">
					<a href="<?php echo esc_url( get_the_permalink( $course['course_id'] ) ); ?>">
						<?php echo wp_kses_post( get_the_title( $course['course_id'] ) ); ?>
					</a>
				</div>
				<?php if ( class_exists( 'STM_LMS_Certificate_Builder' ) ) : ?>
					<a href="#"
					   data-course-id="<?php echo esc_attr( $course['course_id'] ); ?>"
					   class="stm-lms-user-quiz__name stm_preview_certificate btn btn-default">
						<?php esc_html_e( 'Download', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
                    <a href="#"
                       data-course-id="<?php echo esc_attr( $course['course_id'] ); ?>"
                       data-certificate-dc="1"
					   data-coursetitle="<?php echo wp_kses_post( get_the_title( $course['course_id'] ) ); ?>"
                       class="stm-lms-user-quiz__name btn btn-default stm_preview_certificate certificado_button">
											<?php esc_html_e( 'Constancia DC-3', 'masterstudy-lms-learning-management-system' ); ?>
                    </a>
				<?php else : ?>
					<a href="<?php echo esc_url( STM_LMS_Course::certificates_page_url( $course['course_id'] ) ); ?>"
					   target="_blank"
					   class="stm-lms-user-quiz__name btn btn-default">
						<?php esc_html_e( 'Download', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
                    <a href="#"
                       data-course-id="<?php echo esc_attr( $course['course_id'] ); ?>"
                       data-certificate-dc="1"
					   data-coursetitle="<?php echo wp_kses_post( get_the_title( $course['course_id'] ) ); ?>"
                       class="stm-lms-user-quiz__name btn btn-default stm_preview_certificate certificado_button">
											<?php esc_html_e( 'Constancia DC-3', 'masterstudy-lms-learning-management-system' ); ?>
                    </a>
				<?php endif; ?>
				<div class="affiliate_points heading_font" data-copy="<?php echo esc_attr( $code ); ?>">
					<span class="hidden" id="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $code ); ?></span>
					<span class="affiliate_points__btn">
						<i class="fa fa-link"></i>
						<span class="text"><?php esc_html_e( 'Copy code', 'masterstudy-lms-learning-management-system' ); ?></span>
					</span>
				</div>

			</div>
		<?php endforeach; ?>

	</div>

<?php } else { ?>

	<h2 class="stm-lms-account-title">
		<?php esc_html_e( 'My Certificates', 'masterstudy-lms-learning-management-system' ); ?>
	</h2>

	<div class="multiseparator"></div>

	<h4 class="no-certificates-notice"><?php esc_html_e( 'You do not have a certificate yet.', 'masterstudy-lms-learning-management-system' ); ?></h4>
	<h4 class="no-certificates-notice"><?php esc_html_e( 'Get started easy, select a course here, pass it and get your first certificate', 'masterstudy-lms-learning-management-system' ); ?></h4>

<?php } ?>

<div class="stm_lms_confirm_certificate" style="opacity: 0;">
    <div class="stm_lms_finish_score_popup__overlay"></div>

    <div class="stm_lms_confirm_certificate__inner">
		
        <div id="confirm_certificate_form" class="popup_confirm_certificate_form">
		<h3>Datos del Trabajador</h3>
			<div style="display: flex;">
				<div>
					<label> Nombre / Apellidos(s)*</label>
					<input type="text" id="nombre" value="<?php echo $user_firstname; ?>" placeholder="Nombre">
				</div>
				<div>
					<label>&nbsp;</label>
					<input type="text" id="apellidos" value="<?php echo $user_lastname; ?>" placeholder="Apellidos">
				</div>
			</div>
            <label>CURP (Clave Unica de Registro de Población)*</label>
			<input type="text" id="curp" value="<?php echo $user_curp; ?>" placeholder="CURP (Clave Unica de Registro de Población)">
			<label>Ocupación específica (Catálogo Nacional de Ocupaciones)*</label>
			<select class="form-control disable-select" id="ocupacion">
			<?php 
				foreach($choices as $choice) {	
					?>
						<option value="<?php echo $choice ?>"><?php echo $choice ?></option>
					<?php				
				}
			?>
			</select>
			<label>Puesto de Trabajo*</label>
           	<input type="text" id="puesta" value="<?php echo $user_puesta; ?>" placeholder="Puesto de Trabajo"> 
           
			<h3>Datos de la Empresa</h3>
			<label>Nombre o Razón Social*</label>
            <input type="text" id="nombre_razon" value="<?php echo $user_nombre_razon; ?>" placeholder="Nombre o Razón Social">
			<label>Registro Federal de Contribuyentes con homoclave (SHCP)*</label>
			<input type="text" id="rfc" value="<?php echo $user_rfc; ?>" placeholder="RFC">
			
			<h3>Datos del Programa</h3>
			<label>Area temática del Curso*</label>
			<input type="text" id="area_del_curso" value="" placeholder="Area temática del Curso" readonly>
			
			<div class="poup-autorizo-input">
				<input type="checkbox" id="autorizo" name="autorizo" value="1">
				<label for="autorizo"> Autorizo y solicito que mi Constancia DC-3 se expida de manera digital. </label><br>
			</div>
        </div>
         <a href="#" class="btn btn-default btn-green custom-popup-certificate" id="confirm_certificate_form_btn" style="display:none;"><?php esc_html_e('Solicitar DC-3', 'masterstudy-lms-learning-management-system'); ?></a>
		
		  <a href="#" class="btn btn-default btn-green custom-popup-certificate disable_certificate" id="disable_certificate" title="disable"><?php esc_html_e('Solicitar DC-3', 'masterstudy-lms-learning-management-system'); ?></a>
		
		 <a href="" class="btn btn-default btn-green custom-popup-certificate"><?php esc_html_e('Cancelar', 'masterstudy-lms-learning-management-system'); ?></a>
    </div>
</div>

<script>
	jQuery(function () {
		jQuery("#autorizo").click(function () {
			if (jQuery(this).is(":checked")) {
				jQuery("#confirm_certificate_form_btn").show();
				jQuery("#disable_certificate").hide();
			} else {
				jQuery("#confirm_certificate_form_btn").hide();
				jQuery("#disable_certificate").show();
			}
		});
	});
	
jQuery('.certificado_button').on('click', function () {
    var $el = jQuery(this);
	var course_id = $el.data('courseId');

	var url = stm_lms_ajaxurl + '?action=stm_get_area_del_curso_child&course_id=' + course_id;


	jQuery.ajax({
		url: url,
		method: 'GET',
		success: function(data) {
			jQuery('#area_del_curso').val(data);
		}		 
	});
    
    //console.log($el.data('coursetitle'));
});
</script>