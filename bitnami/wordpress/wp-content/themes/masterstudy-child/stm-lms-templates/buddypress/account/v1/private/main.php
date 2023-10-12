<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $current_user
 */

$is_instructor = STM_LMS_Instructor::is_instructor();
$tpl = ($is_instructor) ? 'instructor' : 'student';
$current_user_id = get_current_user_id();
$forms = get_option('stm_lms_form_builder_forms', array());
//print_r($forms[2]['fields'][9]['choices']);
$choices = $forms[2]['fields'][10]['choices'];
$current_user_obj = wp_get_current_user();
$user_fullname =  $current_user_obj->user_firstname.' '.$current_user_obj->user_lastname;
$user_firstname = $current_user->user_firstname;
$user_lastname = $current_user->user_lastname;
$user_curp = get_user_meta($current_user_id, '8g2bnvxu3mt', true);
$user_puesta = get_user_meta($current_user_id, 'tnk9e6yw90a', true);
$user_ocupacion = get_user_meta($current_user_id, '7f8lxabexc9', true);
$user_nombre_razon = get_user_meta($current_user_id, '79p8r64z51r', true); 
$user_rfc = get_user_meta($current_user_id, '2h9dxub26gw', true);

if($tpl === 'student') {
	wp_enqueue_script( 'user-page-stm', get_stylesheet_directory_uri().'/assets/js/user-page-stm.js', array('jquery'), '1.1', true );
	wp_enqueue_script( 'vue.js' );
	wp_enqueue_script( 'vue-resource.js' );
	stm_lms_register_script( 'account/v1/enrolled-courses' );
	stm_lms_register_style( 'user-courses' );
	stm_lms_register_style( 'instructor_courses' );

	stm_lms_register_style( 'user-quizzes' );
	stm_lms_register_style( 'user-certificates' );
	$completed = stm_lms_get_user_completed_courses( $current_user['id'], array( 'user_course_id', 'course_id' ), -1 );
	$certificates = count($completed);
	stm_lms_register_script( 'affiliate_points' );
	stm_lms_register_style( 'affiliate_points' );

	$total = 0;
	$all_courses = stm_lms_get_user_courses( $current_user['id'], '', '', array() );
	foreach ( $all_courses as $course_user ) {
		if ( get_post_type( $course_user['course_id'] ) !== 'stm-courses' ) {
			stm_lms_get_delete_courses( $course_user['course_id'] );
			continue;
		}
		$total++;
	}

	$args = array(
		'post_type' => 'stm-courses',
		'posts_per_page' => 3,
		'per_row' => 3,
		'orderby' => 'rand',
	);

	stm_lms_register_style( 'user' );
	$tpl_wishlist          = ( is_user_logged_in() ? 'account/private/parts/wishlist' : 'account/private/not_logged/wishlist' ); ?>
	<div class="stm-member">
		<div class="stm-member__info">
			<div class="stm-member__avatar">
				<?php echo wp_kses_post($current_user['avatar']); ?>
			</div>
            <div>
                <h3><?php echo esc_html($current_user['login']); ?></h3>
                <p><i class="far fa-envelope"></i> <?php echo esc_html($current_user['email']); ?></p>
            </div>
			<div class="stm-member__settings">
				<a href="<?php echo esc_url(STM_LMS_User::settings_url()); ?>">
					<?php esc_html_e('Ajustes', 'masterstudy'); ?> <i class="lnr lnr-cog"></i>
				</a>
			</div>
		</div>
		<div class="stm-member__courses">
			<h3><i class="fa fa-book float_menu_item__icon"></i><?php esc_html_e('Mis Cursos', 'masterstudy'); ?></h3>
			<p class="stm-member__counter">
				<?php echo $total; ?>
			</p>
			<a href="/user-account/enrolled-courses/" class="btn btn-default stm-member__btn"><?php esc_html_e('Mis Cursos', 'masterstudy'); ?></a>
		</div>
		<div class="stm-member__sertificate">
			<h3><i class="fa fa-medal float_menu_item__icon"></i><?php esc_html_e('Mis Certificados', 'masterstudy'); ?></h3>
			<p class="stm-member__counter">
				<?php echo $certificates; ?>
			</p>
			<a href="/user-account/certificates/" class="btn btn-default stm-member__btn"><?php esc_html_e('Mis Certificados', 'masterstudy'); ?></a>
		</div>
	</div>
	<div class="stm-member-tab">
		<div id="buddypress">
			<div class="item-list-tabs no-ajax heading_font">
				<ul>
					<li class="current selected">
						<a href="#recommend-courses" id="recommend-courses">
							<?php esc_html_e('Aula', 'masterstudy'); ?>
						</a>
					</li>
					<li>
						<a href="#my-courses" id="my-courses">
							<?php esc_html_e('Mis Cursos', 'masterstudy'); ?>
						</a>
					</li>
					<li>
						<a href="#certificates" id="certificates">
							<?php esc_html_e('Mis Certificados', 'masterstudy'); ?>
						</a>
					</li>
					<li>
						<a href="#wishlist" id="wishlist">
							<?php esc_html_e('Mi Lista de Deseos', 'masterstudy'); ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="stm-member-content">
		<div class="stm-member-content__item active" id="recommend-courses">
			<div class="multiseparator"></div>
            <?php
                $q = new WP_Query( $args );
                if( $q->have_posts() ):
                    ?>
                    <div class="stm_lms_related_courses">
                        <?php
                            STM_LMS_Templates::show_lms_template( 'courses/grid', array( 'args' => $args ) );
                        ?>
                    </div>
                <?php endif;
                wp_reset_postdata();
            ?>
		</div>
		<div class="stm-member-content__item" id="my-courses">
            <div id="enrolled-courses">
                <div class="multiseparator"></div>
                <div class="stm-lms-user-courses">
                    <div class="stm_lms_instructor_courses__grid">
                        <div class="stm_lms_instructor_courses__single" v-for="course in courses"
                             v-bind:class="{'expired' : course.expiration.length && course.is_expired || course.membership_expired || course.membership_inactive}">
                            <div class="stm_lms_instructor_courses__single__inner">
                                <div class="stm_lms_instructor_courses__single--image">

                                    <div class="stm_lms_post_status heading_font"
                                         v-if="course.post_status"
                                         v-bind:class="course.post_status.status">
                                        {{ course.post_status.label }}
                                    </div>

                                    <div v-html="course.image" class="image_wrapper"></div>

                                    <?php STM_LMS_Templates::show_lms_template( 'account/private/parts/expiration' ); ?>

                                </div>

                                <div class="stm_lms_instructor_courses__single--inner">

                                    <div class="stm_lms_instructor_courses__single--terms" v-if="course.terms">
                                        <div class="stm_lms_instructor_courses__single--term"
                                             v-for="(term, key) in course.terms"
                                             v-html="term" v-if="key === 0">
                                        </div>
                                    </div>

                                    <div class="stm_lms_instructor_courses__single--title">
                                        <a v-bind:href="course.link">
                                            <h5 v-html="course.title"></h5>
                                        </a>
                                    </div>

                                    <div class="stm_lms_instructor_courses__single--progress">
                                        <div class="stm_lms_instructor_courses__single--progress_top">
                                            <div class="stm_lms_instructor_courses__single--duration" v-if="course.duration">
                                                <i class="far fa-clock"></i>
                                                {{ course.duration }}
                                            </div>
                                            <div class="stm_lms_instructor_courses__single--completed">
                                                {{ course.progress_label }}
                                            </div>
                                        </div>

                                        <div class="stm_lms_instructor_courses__single--progress_bar">
                                            <div class="stm_lms_instructor_courses__single--progress_filled"
                                                 v-bind:style="{'width' : course.progress + '%'}"></div>
                                        </div>

                                    </div>

                                    <div class="stm_lms_instructor_courses__single--enroll">
                                        <a v-if="course.expiration.length && course.is_expired || course.membership_expired || course.membership_inactive" class="btn btn-default"
                                           :href="course.url" target="_blank">
                                            <span><?php esc_html_e( 'Preview Course', 'masterstudy-lms-learning-management-system' ); ?></span>
                                        </a>
                                        <a v-bind:href="course.current_lesson_id" class="btn btn-default"
                                           v-bind:class="{'continue' : course.progress !== '0'}"
                                           v-else>
                                            <span v-if="course.progress === '0'"><?php esc_html_e( 'Start Course', 'masterstudy-lms-learning-management-system' ); ?></span>
                                            <span v-else-if="course.progress === '100'"><?php esc_html_e( 'Completed', 'masterstudy-lms-learning-management-system' ); ?></span>
                                            <span v-else><?php esc_html_e( 'Continue', 'masterstudy-lms-learning-management-system' ); ?></span>
                                        </a>
                                    </div>

                                    <div class="stm_lms_instructor_courses__single--started">
                                        {{ course.start_time }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 v-if="!courses.length && !loading"><?php esc_html_e( 'No courses.', 'masterstudy-lms-learning-management-system' ); ?></h4>
                    <h4 v-if="loading"><?php esc_html_e( 'Loading courses.', 'masterstudy-lms-learning-management-system' ); ?></h4>
                </div>

                <div class="text-center load-my-courses">
                    <a @click="getCourses()" v-if="!total" class="btn btn-default" v-bind:class="{'loading' : loading}">
                        <span><?php esc_html_e( 'Show more', 'masterstudy-lms-learning-management-system' ); ?></span>
                    </a>
                </div>
            </div>
		</div>
		<div class="stm-member-content__item" id="certificates">
			<?php if ( ! empty( $completed ) ) {
				if ( class_exists( 'STM_LMS_Certificate_Builder' ) ) {
					wp_register_script( 'jspdf', STM_LMS_URL . '/assets/vendors/jspdf.umd.js', array(), stm_lms_custom_styles_v() );
//					wp_enqueue_script( 'stm_generate_certificate', STM_LMS_URL . '/assets/js/certificate_builder/generate_certificate.js', array( 'jspdf', 'stm_certificate_fonts' ), stm_lms_custom_styles_v() );
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
				?>
				<div class="stm-lms-user-quizzes stm-lms-user-certificates">
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
								   class="stm-lms-user-quiz__name btn btn-default stm_preview_certificate certificado_button">
									<?php esc_html_e( 'Constancia DC-3', 'masterstudy-lms-learning-management-system' ); ?>
								</a>
							<?php else : ?>
								<a href="<?php echo esc_url( STM_LMS_Course::certificates_page_url( $course['course_id'] ) ); ?>"
								   target="_blank"
								   class="stm-lms-user-quiz__name">
									<?php esc_html_e( 'Download', 'masterstudy-lms-learning-management-system' ); ?>
								</a>
                                <a href="#"
                                   data-course-id="<?php echo esc_attr( $course['course_id'] ); ?>"
                                   data-certificate-dc="1"
                                   class="stm-lms-user-quiz__name btn btn-default stm_preview_certificate certificado_button">
																	<?php esc_html_e( 'Certificados DC-3', 'masterstudy-lms-learning-management-system' ); ?>
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

				<div class="multiseparator"></div>

				<h4 class="no-certificates-notice"><?php esc_html_e( 'You do not have a certificate yet.', 'masterstudy-lms-learning-management-system' ); ?></h4>
				<h4 class="no-certificates-notice"><?php esc_html_e( 'Get started easy, select a course here, pass it and get your first certificate', 'masterstudy-lms-learning-management-system' ); ?></h4>

			<?php } ?>
		</div>
		<div class="stm-member-content__item" id="wishlist">
            <?php STM_LMS_Templates::show_lms_template( $tpl_wishlist, array( 'current_user' => $current_user ) ); ?>
		</div>
	</div>
<?php } else {
	
	STM_LMS_Templates::show_lms_template("buddypress/account/v1/private/{$tpl}", compact('current_user'));
} ?>

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