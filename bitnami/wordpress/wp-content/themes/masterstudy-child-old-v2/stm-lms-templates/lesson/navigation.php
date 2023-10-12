<?php
/**
 * @var $post_id
 * @var $item_id
 */

$course_meta = STM_LMS_Helpers::parse_meta_field( $post_id );
if ( ! empty( $course_meta['curriculum'] ) ) :
	$curriculum_full = explode( ',', $course_meta['curriculum'] );
	$curriculum      = STM_LMS_Helpers::only_array_numbers( $curriculum_full );

	$course_type = get_post_type( $item_id );

	if ( 'stm-quizzes' === $course_type ) {
		if ( STM_LMS_Quiz::quiz_passed( $item_id ) ) {
			$completed       = 'completed';
			$completed_label = esc_html__( 'Passed', 'masterstudy-lms-learning-management-system' );
		} else {
			$completed_label = '';
			$completed       = '';
		}
	} else {
		$completed = ( STM_LMS_Lesson::is_lesson_completed( '', $post_id, $item_id ) ) ? 'completed' : 'uncompleted';
		if ( 'completed' === $completed ) {
			$completed_label = esc_html__( 'Completed', 'masterstudy-lms-learning-management-system' );
		} else {
			$completed_label = esc_html__( 'Complete', 'masterstudy-lms-learning-management-system' );
		}
	}
	if ( in_array( $item_id, $curriculum ) ) {

		$current_lesson_id = array_search( $item_id, $curriculum );
		$section           = STM_LMS_Lesson::get_lesson_info( $curriculum_full, $item_id );
		$prev_lesson       = ( ! empty( $curriculum[ $current_lesson_id - 1 ] ) ) ? $curriculum[ $current_lesson_id - 1 ] : '';
		$next_lesson       = ( ! empty( $curriculum[ $current_lesson_id + 1 ] ) ) ? $curriculum[ $current_lesson_id + 1 ] : '';
	}


	$completed_label = apply_filters( 'stm_lms_completed_label', $completed_label, $item_id, $post_id );
	$lesson_style    = STM_LMS_Options::get_option( 'lesson_style', 'default' );
	?>

	<div class="stm-lms-lesson_navigation heading_font <?php esc_attr_e( 'Completed', 'masterstudy-lms-learning-management-system' ); ?>" data-completed="<?php esc_html_e( 'Completed', 'masterstudy-lms-learning-management-system' ); ?>">

		<div class="stm-lms-lesson_navigation_side stm-lms-lesson_navigation_prev">

			<?php
			if ( ! empty( $prev_lesson ) ) :
				$prev_section = STM_LMS_Lesson::get_lesson_info( $curriculum_full, $prev_lesson );
				if ( 'classic' === $lesson_style && 'stream' !== $lesson_type && 'zoom_conference' !== $lesson_type ) :
					?>
					<a href="<?php echo esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $prev_lesson ) ); ?>">
						<i class="lnr lnr-arrow-left"></i>
						<span>
						<?php esc_html_e( 'Prev lesson', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $prev_lesson ) ); ?>">
						<i class="lnr lnr-chevron-left"></i>
						<span>
							<?php if ( ! empty( $prev_section['text'] ) ) : ?>
								<span class="stm_lms_section_text">
									<?php echo esc_html( sanitize_text_field( urldecode( $prev_section['text'] ) ) ); ?>
								</span>
							<?php endif; ?>
							<span>
								<?php echo esc_html( sanitize_text_field( get_the_title( $prev_lesson ) ) ); ?>
							</span>
						</span>
					</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $completed_label ) && $completed != 'completed' ) :
            $real_next_url =  esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
        ?>
			<div <?php echo esc_attr( apply_filters( 'stm_lms_navigation_complete_atts', '', $item_id ) ); ?> class="<?php echo esc_attr( apply_filters( 'stm_lms_navigation_complete_class', 'stm-lms-lesson_navigation_complete', $item_id ) ); ?>">
				<a href="#" class="btn stm_lms_complete_lesson <?php echo esc_attr( $completed ); ?>" data-url="<?php echo $real_next_url; ?>" data-course="<?php echo intval( $post_id ); ?>" data-lesson="<?php echo intval( $item_id ); ?>">
                    <span>
                      <?php esc_html_e( 'Next lesson', 'masterstudy-lms-learning-management-system' ); ?>
                    </span>
                    <i class="lnr lnr-arrow-right"></i>
				</a>
			</div>
		<?php endif; ?>

        <?php if($completed === 'completed'): ?>
            <div class="stm-lms-lesson_navigation_side stm-lms-lesson_navigation_next">
                <?php if ( ! empty( $next_lesson ) ) :
                    $next_section = STM_LMS_Lesson::get_lesson_info( $curriculum_full, $next_lesson );
                    if ( 'classic' === $lesson_style && 'stream' !== $lesson_type && 'zoom_conference' !== $lesson_type ) :
                        ?>

                        <?php if($completed == 'completed') { ?>
                        <?php $lesson_url =  esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
                        $customPopupClass= "";
                        $real_next_url =  esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
                        ?>
                    <?php } else { ?>
                        <?php
                        $lesson_url = 'javascript:void(0)';
                        $customPopupClass = "loackedAlertMsg";
                        $real_next_url =  esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
                        ?>
                    <?php } ?>
                        <a href="<?php echo $lesson_url; ?>" class="<?php echo $customPopupClass; ?>" data-url="<?php echo $real_next_url; ?>" id="NextLessionUpdated">
                            <span>
                                <?php esc_html_e( 'Next lesson', 'masterstudy-lms-learning-management-system' ); ?>
                            </span>
                            <i class="lnr lnr-arrow-right"></i>
                        </a>
                    <?php else : ?>


                        <?php if($completed == 'completed') { ?>
                            <?php $lesson_url =  esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
                            $customPopupClass= "";
                            $real_next_url =  esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
                            ?>
                        <?php } else { ?>
                            <?php $lesson_url = 'javascript:void(0)';
                            $customPopupClass = "loackedAlertMsg";
                            $real_next_url =  esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
                            ?>
                        <?php } ?>

                        <a href="<?php echo $lesson_url; ?>" class="<?php echo $customPopupClass; ?>" data-url="<?php echo $real_next_url; ?>" id="NextLessionUpdated">
                            <span>
                                <?php if ( ! empty( $next_section['text'] ) ) : ?>
                                    <span class="stm_lms_section_text">
                                        <?php echo esc_html( sanitize_text_field( urldecode( $next_section['text'] ) ) ); ?>
                                    </span>
                                <?php endif; ?>
                                <span><?php echo esc_html( sanitize_text_field( get_the_title( $next_lesson ) ) ); ?></span>
                            </span>
                            <i class="lnr lnr-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

	</div>

<?php
endif;