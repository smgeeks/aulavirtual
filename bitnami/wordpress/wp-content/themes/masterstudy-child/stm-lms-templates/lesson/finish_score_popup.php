<?php

/**
 * @var $post_id
 * @var $item_id
 */

$completed = STM_LMS_Lesson::is_lesson_completed( null, $post_id, $item_id );

stm_lms_register_style( 'lesson/total_progress' );
stm_lms_register_script( 'lesson/total_progress', array( 'vue.js', 'vue-resource.js' ) );
wp_localize_script(
	'stm-lms-lesson/total_progress',
	'total_progress',
	array(
		'course_id' => $post_id,
		'completed' => (bool) $completed,
	)
);
if ( class_exists( 'STM_LMS_Certificate_Builder' ) ) {
	wp_register_script( 'jspdf', STM_LMS_URL . '/assets/vendors/jspdf.umd.js', array(), stm_lms_custom_styles_v(), false );
	wp_enqueue_script(
		'stm_generate_certificate',
			get_stylesheet_directory_uri() . '/assets/js/certificate_builder/generate_certificate.js',
		array(
			'jspdf',
			'stm_certificate_fonts',
		),
		stm_lms_custom_styles_v(),
		false
	);
}

$current_user_id = get_current_user_id();
$current_user = wp_get_current_user();
$forms = get_option('stm_lms_form_builder_forms', array());
//print_r($forms[2]['fields'][9]['choices']);
$choices = $forms[2]['fields'][10]['choices'];
$user_curp = get_user_meta($current_user_id, '8g2bnvxu3mt', true);
$user_puesta = get_user_meta($current_user_id, 'tnk9e6yw90a', true);
$user_ocupacion = get_user_meta($current_user_id, '7f8lxabexc9', true);
$user_nombre_razon = get_user_meta($current_user_id, '79p8r64z51r', true);
$user_rfc = get_user_meta($current_user_id, '2h9dxub26gw', true);
$user_firstname = $current_user->user_firstname;
$user_lastname = $current_user->user_lastname;
$disable_smile           = STM_LMS_Options::get_option( 'finish_popup_image_disable', false );
$failed_image            = STM_LMS_URL . '/assets/img/faces/crying.svg';
$success_image           = STM_LMS_URL . '/assets/img/faces/kissing.svg';
$custom_failed_image_id  = STM_LMS_Options::get_option( 'finish_popup_image_failed' );
$custom_success_image_id = STM_LMS_Options::get_option( 'finish_popup_image_success' );
?>
<div class="stm_lms_finish_score_popup" style="opacity: 0;">

	<div class="stm_lms_finish_score_popup__overlay"></div>

	<div class="stm_lms_finish_score_popup__inner">

		<i class="stm_lms_finish_score_popup__close fa fa-times"></i>

		<div id="stm_lms_finish_score">
			<h4 v-if="loading" class="loading">
				<?php esc_html_e( 'Loading your statistics', 'masterstudy-lms-learning-management-system' ); ?>
			</h4>

			<div class="stm_lms_finish_score" v-else>

				<div class="stm_lms_finish_score__head">
					<?php if ( ! $disable_smile ) : ?>
						<?php
						if ( ! empty( $custom_failed_image_id ) ) {
							$custom_failed_image_url = wp_get_attachment_image_url( $custom_failed_image_id, 'thumbnail' );
							if ( ! empty( $custom_failed_image_url ) ) {
								$failed_image = $custom_failed_image_url;
							}
						}
						if ( ! empty( $custom_success_image_id ) ) {
							$custom_success_image_url = wp_get_attachment_image_url( $custom_success_image_id, 'thumbnail' );
							if ( ! empty( $custom_success_image_url ) ) {
								$success_image = $custom_success_image_url;
							}
						}
						?>
						<div class="stm_lms_finish_score__face">
							<img src="<?php echo esc_url( $failed_image ); ?>"
							     v-if="!stats.course_completed"/>
							<img src="<?php echo esc_url( $success_image ); ?>" v-else/>
						</div>
					<?php endif; ?>
					<div class="stm_lms_finish_score__score <?php echo esc_attr( ( $disable_smile ) ? 'no_face' : '' ); ?>">
						<span><?php esc_html_e( 'Your score', 'masterstudy-lms-learning-management-system' ); ?></span>
						<h3 v-html="stats.course.progress_percent + '%'"></h3>
					</div>
				</div>

				<div class="stm_lms_finish_score__notice">
					<span v-if="!stats.course_completed"><?php esc_html_e( 'You have NOT completed the course', 'masterstudy-lms-learning-management-system' ); ?></span>
					<span v-else><?php esc_html_e( 'You have successfully completed the course', 'masterstudy-lms-learning-management-system' ); ?></span>
				</div>

				<h2 class="stm_lms_finish_score__title" v-html="stats.title"></h2>

				<div class="stm_lms_finish_score__stats">

					<div class="stm_lms_finish_score__stat" v-for="(stat, type) in stats.curriculum">

						<div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='lesson'">
							<i class="far fa-file-alt"></i>
							<span>
								<?php esc_html_e( 'Pages:', 'masterstudy-lms-learning-management-system' ); ?>
								<strong>{{stat.completed}}/{{stat.total}}</strong>
							</span>
						</div>

						<div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='multimedia'">
							<i class="far fa-play-circle"></i>
							<span>
								<?php esc_html_e( 'Media:', 'masterstudy-lms-learning-management-system' ); ?>
								<strong>{{stat.completed}}/{{stat.total}}</strong>
							</span>
						</div>

						<div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='quiz'">
							<i class="far fa-question-circle"></i>
							<span>
								<?php esc_html_e( 'Quizzes:', 'masterstudy-lms-learning-management-system' ); ?>
								<strong>{{stat.completed}}/{{stat.total}}</strong>
							</span>
						</div>

						<div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='assignment'">
							<i class="fas fa-spell-check"></i>
							<span>
								<?php esc_html_e( 'Assignments:', 'masterstudy-lms-learning-management-system' ); ?>
								<strong>{{stat.completed}}/{{stat.total}}</strong>
							</span>
						</div>

					</div>

				</div>

				<div class="stm_lms_finish_score__buttons">
					<!--Buttons for passed-->
					<div class="inner">
						<?php if ( class_exists( 'STM_LMS_Certificate_Builder' ) ) : ?>
							<a v-if="stats.course_completed" href="#" class="btn btn-default stm_preview_certificate"
							   data-course-id="<?php echo esc_attr( $post_id ); ?>">
								<?php esc_html_e( 'Certificate', 'masterstudy-lms-learning-management-system' ); ?>
							</a>
							<a v-if="stats.course_completed" href="#" class="btn btn-default stm_preview_certificate certificado_button"
							   data-certificate-dc="1" data-course-id="<?php echo esc_attr( $post_id ); ?>">
								<?php esc_html_e( 'Constancia DC-3', 'masterstudy-lms-learning-management-system' ); ?>
							</a>
						<?php endif; ?>

						<a :href="stats.url" class="btn btn-default btn-green">
							<?php esc_html_e( 'View course', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>

				</div>

			</div>

		</div>

	</div>

</div>

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