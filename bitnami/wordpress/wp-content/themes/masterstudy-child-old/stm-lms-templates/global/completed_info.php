<?php

/**
 * @var $course_id
 */

$total_progress = STM_LMS_Lesson::get_total_progress( get_current_user_id(), $course_id );

if ( ! empty( $total_progress ) && $total_progress['course_completed'] ) :
	stm_lms_register_style( 'lesson/total_progress' );
	if ( class_exists( 'STM_LMS_Certificate_Builder' ) ) {
		wp_register_script( 'jspdf', STM_LMS_URL . '/assets/vendors/jspdf.umd.js', array(), stm_lms_custom_styles_v() );
		wp_enqueue_script( 'stm_generate_certificate', get_stylesheet_directory_uri() . '/assets/js/certificate_builder/generate_certificate.js', array( 'jspdf', 'stm_certificate_fonts' ), stm_lms_custom_styles_v() );
	}
	$current_user_id = get_current_user_id();
	$forms = get_option('stm_lms_form_builder_forms', array());
    //print_r($forms[2]['fields'][9]['choices']);
    $choices = $forms[2]['fields'][9]['choices'];
	$current_user = wp_get_current_user();
	$user_fullname =  $current_user->user_firstname.' '.$current_user->user_lastname;
	$user_firstname = $current_user->user_firstname;
	$user_lastname = $current_user->user_lastname;
	$user_curp = get_user_meta($current_user_id, '8g2bnvxu3mt', true);
	$user_puesta = get_user_meta($current_user_id, 'tnk9e6yw90a', true);
	$user_ocupacion = get_user_meta($current_user_id, '7f8lxabexc9', true);
	$user_nombre_razon = get_user_meta($current_user_id, '79p8r64z51r', true); 
	$user_rfc = get_user_meta($current_user_id, '2h9dxub26gw', true);
	?>

	<div class="stm_lms_course_completed_summary">

		<div class="stm_lms_course_completed_summary__title">
			<span><?php esc_html_e( 'You have completed the course:', 'masterstudy-lms-learning-management-system' ); ?></span>
			<strong><?php printf( /* translators: %s will be replaced with a string. */ esc_html__( 'Score %s', 'masterstudy-lms-learning-management-system' ), esc_html( "{$total_progress['course']['progress_percent']}%" ) ); ?></strong>
		</div>

		<div class="stm_lms_finish_score">

			<div class="stm_lms_finish_score__stats">

				<?php foreach ( $total_progress['curriculum'] as $item_type => $item_data ) : ?>

					<?php if ( 'lesson' === $item_type ) : ?>
						<div class="stm_lms_finish_score__stat">
							<div class="stm_lms_finish_score__stat_<?php echo esc_attr( $item_type ); ?>">
								<i class="far fa-file-alt"></i>
								<span><?php esc_html_e( 'Pages:', 'masterstudy-lms-learning-management-system' ); ?>
									<strong><?php echo esc_html( "{$item_data['completed']}/{$item_data['total']}" ); ?></strong></span>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( 'multimedia' === $item_type ) : ?>
						<div class="stm_lms_finish_score__stat">
							<div class="stm_lms_finish_score__stat_<?php echo esc_attr( $item_type ); ?>">
								<i class="far fa-play-circle"></i>
								<span><?php esc_html_e( 'Multimedia:', 'masterstudy-lms-learning-management-system' ); ?>
									<strong><?php echo esc_html( "{$item_data['completed']}/{$item_data['total']}" ); ?></strong></span>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( 'quiz' === $item_type ) : ?>
						<div class="stm_lms_finish_score__stat">
							<div class="stm_lms_finish_score__stat_<?php echo esc_attr( $item_type ); ?>">
								<i class="far fa-question-circle"></i>
								<span><?php esc_html_e( 'Quizzes:', 'masterstudy-lms-learning-management-system' ); ?>
									<strong><?php echo esc_html( "{$item_data['completed']}/{$item_data['total']}" ); ?></strong></span>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( 'assignment' === $item_type ) : ?>
						<div class="stm_lms_finish_score__stat">
							<div class="stm_lms_finish_score__stat_<?php echo esc_attr( $item_type ); ?>">
								<i class="fa fa-spell-check"></i>
								<span><?php esc_html_e( 'Assignments:', 'masterstudy-lms-learning-management-system' ); ?>
									<strong><?php echo esc_html( "{$item_data['completed']}/{$item_data['total']}" ); ?></strong></span>
							</div>
						</div>
					<?php endif; ?>

				<?php endforeach; ?>

			</div>

		</div>

	</div>

	<?php if ( class_exists( 'STM_LMS_Certificate_Builder' ) ) : ?>

	<?php
	$quizdata = $total_progress['curriculum']['quiz'];
	$quizresult = $quizdata['completed'];
	$quiztotal = $quizdata['total'];
	?>

	<?php if($quizresult == 1) { ?>
		<a href="#"
		   class="stm_lms_course_completed_summary__certificate stm_preview_certificate"
		   data-course-id="<?php echo esc_attr( $course_id ); ?>">
			<i class="fa fa-cloud-download-alt"></i>
			<?php esc_html_e( 'Download your Certificate', 'masterstudy-lms-learning-management-system' ); ?>
		</a>
	<?php } else { ?>

		<a href="javascript:void(0)"
		   class="stm_custom_download_certificate_btn"
		   data-course-id="<?php echo esc_attr( $course_id ); ?>">
			<i class="fa fa-cloud-download-alt"></i>
			<?php esc_html_e( 'Download your Certificate', 'masterstudy-lms-learning-management-system' ); ?>
		</a>

	<?php } ?>

    <a href="#"
       data-course-id="<?php echo esc_attr( $course_id ); ?>"
       data-certificate-dc="1"
       class="stm_lms_course_completed_summary__certificate stm_preview_certificate certificado_button">
        <i class="fa fa-cloud-download-alt"></i>
        <?php esc_html_e( 'Constancia DC-3', 'masterstudy-lms-learning-management-system' ); ?>
    </a>

<?php endif; ?>

<?php
endif;
$subject_area = get_post_meta($course_id, 'subject_area', true);
$name_agent = get_post_meta($course_id, 'name_agent', true);
$duration_course = get_post_meta($course_id, 'duration_course', true);
?>

<?php if($subject_area || $name_agent || $duration_course): ?>
<div class="stm_lms_course_metas">
    <div class="stm_lms_course_metas__item">
        <span><?php esc_attr_e('Area tematica del curso', 'masterstudy-lms-learning-management-system'); ?>:</span><strong><?php echo $subject_area; ?></strong>
    </div>
    <div class="stm_lms_course_metas__item">
        <span><?php esc_attr_e('Nombre de agente capacitador', 'masterstudy-lms-learning-management-system');  ?>:</span><strong><?php echo $name_agent; ?></strong>
    </div>
    <div class="stm_lms_course_metas__item">
        <span><?php esc_attr_e('Tiempo de vigencia', 'masterstudy-lms-learning-management-system'); ?>:</span><strong><?php echo $duration_course; ?></strong>
    </div>
</div>
<?php endif; ?>

<div class="stm_lms_confirm_certificate" style="opacity: 0;">
    <div class="stm_lms_finish_score_popup__overlay"></div>

    <div class="stm_lms_confirm_certificate__inner">
		
        <div id="confirm_certificate_form" class="popup_confirm_certificate_form">
		<h3>Datos del Trabajador</h3>
			<div style="display: flex;">
				<div>
					<label>Nombre*</label>
					<input type="text" id="nombre" value="<?php echo $user_firstname; ?>" placeholder="Nombre">
				</div>
				<div>
					<label>Apellidos*</label>
					<input type="text" id="apellidos" value="<?php echo $user_lastname; ?>" placeholder="Apellidos">
				</div>
			</div>
            <label>CURP (Clave Unica de Registro de Población)*</label>
			<input type="text" id="curp" value="<?php echo $user_curp; ?>" placeholder="CURP (Clave Unica de Registro de Población)">
			<label>Ocupación Específica*</label>
			<select class="form-control disable-select" id="ocupacion">
			<?php 
				foreach($choices as $choice) {	
					?>
						<option value="<?php echo $choice ?>"><?php echo $choice ?></option>
					<?php				
				}
			?>
			</select>
			<!-- <input type="text" id="ocupacion" value="<?php echo $user_ocupacion; ?>" placeholder="Ocupación Específica"> -->
			<label>Puesto de Trabajo*</label>
           	<input type="text" id="puesta" value="<?php echo $user_puesta; ?>" placeholder="Puesto de Trabajo"> 
			
					
		
			<h3>Datos de la Empresa</h3>
			<label>Nombre o Razón Social*</label>
            <input type="text" id="nombre_razon" value="<?php echo $user_nombre_razon; ?>" placeholder="Nombre o Razón Social">
			<label>RFC*</label>
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
</script>
