<?php
remove_action( 'wp_ajax_stm_lms_user_answers', 'STM_LMS_Quiz::user_answers' );
remove_action( 'wp_ajax_nopriv_stm_lms_user_answers', 'STM_LMS_Quiz::user_answers' );
add_action( 'wp_ajax_stm_lms_user_answers', 'retake_user_answers' );
add_action( 'wp_ajax_nopriv_stm_lms_user_answers', 'retake_user_answers' );
function retake_user_answers() {

	check_ajax_referer( 'user_answers', 'nonce' );
	global $wpdb;
	$source   = ( ! empty( $_POST['source'] ) ) ? intval( $_POST['source'] ) : '';
	$sequency = ! empty( $_POST['questions_sequency'] ) ? $_POST['questions_sequency'] : array();
	$sequency = json_encode( $sequency );
	$user     = apply_filters( 'user_answers__user_id', STM_LMS_User::get_current_user(), $source );
	/*Checking Current User*/
	if ( ! $user['id'] ) {
		die;
	}
	$user_id   = $user['id'];
	$course_id = ( ! empty( $_POST['course_id'] ) ) ? intval( $_POST['course_id'] ) : '';
	$course_id = apply_filters( 'user_answers__course_id', $course_id, $source );

	if ( empty( $course_id ) || empty( $_POST['quiz_id'] ) ) {
		die;
	}
	$quiz_id = intval( $_POST['quiz_id'] );
	$progress        = 0;
	$quiz_info       = STM_LMS_Helpers::parse_meta_field( $quiz_id );
	$total_questions = count( explode( ',', $quiz_info['questions'] ) );

	$questions = explode( ',', $quiz_info['questions'] );
	$retake_counter = intval($quiz_info['quiz_retake_counter']);

	foreach ( $questions as $question ) {
		$type = get_post_meta( $question, 'type', true );

		if ( 'question_bank' !== $type ) {
			continue;
		}

		$answers = get_post_meta( $question, 'answers', true );

		if ( ! empty( $answers[0] ) && ! empty( $answers[0]['categories'] ) && ! empty( $answers[0]['number'] ) ) {
			$number     = $answers[0]['number'];
			$categories = wp_list_pluck( $answers[0]['categories'], 'slug' );

			$questions = get_post_meta( $quiz_id, 'questions', true );
			$questions = ( ! empty( $questions ) ) ? explode( ',', $questions ) : array();

			$args = array(
				'post_type'      => 'stm-questions',
				'posts_per_page' => $number,
				'post__not_in'   => $questions,
				'tax_query'      => array(
					array(
						'taxonomy' => 'stm_lms_question_taxonomy',
						'field'    => 'slug',
						'terms'    => $categories,
					),
				),
			);

			$q = new WP_Query( $args );

			if ( $q->have_posts() ) {

				$total_in_bank = $q->found_posts - 1;
				if ( $total_in_bank > $number ) {
					$total_in_bank = $number - 1;
				}
				$total_questions += $total_in_bank;
				wp_reset_postdata();
			}
		}
	}
	$single_question_score_percent = 100 / $total_questions;
	$cutting_rate                  = ( ! empty( $quiz_info['re_take_cut'] ) ) ? ( 100 - $quiz_info['re_take_cut'] ) / 100 : 1;
	$passing_grade                 = ( ! empty( $quiz_info['passing_grade'] ) ) ? intval( $quiz_info['passing_grade'] ) : 0;

	$user_quizzes   = stm_lms_get_user_quizzes( $user_id, $quiz_id, array( 'user_quiz_id', 'progress' ) );
	$attempt_number = count( $user_quizzes ) + 1;
	$prev_answers   = ( 1 !== $attempt_number ) ? stm_lms_get_user_answers( $user_id, $quiz_id, $attempt_number - 1, true, array( 'question_id' ) ) : array();

	foreach ( $_POST as $question_id => $value ) {
		if ( is_numeric( $question_id ) ) {
			$question_id = intval( $question_id );
			$type        = get_post_meta( $question_id, 'type', true );

			if ( 'fill_the_gap' === $type ) {
				$answer = STM_LMS_Quiz::encode_answers( $value );
			} else {
				if ( is_array( $value ) ) {
					$answer = STM_LMS_Quiz::sanitize_answers( $value );
				} else {
					$answer = sanitize_text_field( $value );
				}
			}

			$user_answer = ( is_array( $answer ) ) ? implode( ',', $answer ) : $answer;

			$correct_answer = STM_LMS_Quiz::check_answer( $question_id, $answer );

			if ( $correct_answer ) {
				if ( 1 === $attempt_number || STM_LMS_Helpers::in_array_r( $question_id, $prev_answers ) ) {
					$single_question_score = $single_question_score_percent;
				} else {
					$single_question_score = $single_question_score_percent * $cutting_rate;
				}

				$progress += $single_question_score;
			}

			$add_answer = compact( 'user_id', 'course_id', 'quiz_id', 'question_id', 'attempt_number', 'user_answer', 'correct_answer' );
			stm_lms_add_user_answer( $add_answer );
		}
	}

	/*Add user quiz*/
	$tries = null;
	if ($retake_counter > 0 && $progress < $passing_grade) {
		$user_tries = get_user_meta( $user_id, 'tries' . $quiz_id . '_' . $course_id, true );
		if(!$user_tries) {
			$tries = --$retake_counter;
		} else {
			$tries = --$user_tries;
		}
		update_user_meta( $user_id, 'tries' . $quiz_id . '_' . $course_id,  $tries);
	}

	if($tries == 0 && $progress < $passing_grade) {
		STM_LMS_Mails::wp_mail_text_html();
		delete_user_course($user_id, $course_id);
		update_user_meta( $user_id, 'tries' . $quiz_id . '_' . $course_id,  '');
		$user_login   = $user['login'];
		$course_title = get_the_title( $course_id );
		$quiz_name    = get_the_title( $quiz_id );
		STM_LMS_Mails::send_email( 'Unsubscribe course', '', $user['email'], array(), 'stm_lms_user_unsubscribe_course', compact( 'user_login', 'course_title', 'quiz_name', 'passing_grade' ) );
		$tries = 'unsubscribe';
		STM_LMS_Mails::remove_wp_mail_text_html();
	}

	$progress  = round( $progress );
	$status    = ( $progress < $passing_grade ) ? 'failed' : 'passed';
	$user_quiz = compact( 'user_id', 'course_id', 'quiz_id', 'progress', 'status', 'sequency' );

	if($tries !== 'unsubscribe') {
		stm_lms_add_user_quiz( $user_quiz );
	}

	/*REMOVE TIMER*/
	stm_lms_get_delete_user_quiz_time( $user_id, $quiz_id );

	if ( 'passed' === $status ) {
		STM_LMS_Mails::wp_mail_text_html();
		STM_LMS_Course::update_course_progress( $user_id, $course_id );
		$user_login   = $user['login'];
		$course_title = get_the_title( $course_id );
		$quiz_name    = get_the_title( $quiz_id );
		$message      = sprintf(
		/* translators: %1$s Course Title, %2$s User Login */
			esc_html__( '%1$s completed the %2$s on the course %3$s with a Passing grade of %4$s%%', 'masterstudy-lms-learning-management-system' ),
			$user_login,
			$quiz_name,
			$course_title,
			$passing_grade,
		);

		STM_LMS_Mails::send_email( 'Quiz Completed', $message, $user['email'], array(), 'stm_lms_course_quiz_completed_for_user', compact( 'user_login', 'course_title', 'quiz_name', 'passing_grade' ) );
		STM_LMS_Mails::remove_wp_mail_text_html();
	}

	if($tries > 1) {
		$user_tries_msg = esc_html__("Necesitas al menos el " . $passing_grade . "% para aprobar. ¡Ánimo, aún te quedan " . $tries . " intentos!", 'masterstudy-lms-learning-management-system');
	} else if ($tries === 1) {
		$user_tries_msg = esc_html__("¡Es tu último intento! Repasa tus lecciones y aprueba la evaluación.", 'masterstudy-lms-learning-management-system');
	} else if ($tries === 'unsubscribe') {
		$user_tries_msg = esc_html__("¡Oh, no! Tu puntuación ha sido menor a " . $passing_grade . "%, lo sentimos, el curso se cancelará.", 'masterstudy-lms-learning-management-system');
	} else {
		$user_tries_msg = '';
	}

	$user_quiz['passed']   = $progress >= $passing_grade;
	$user_quiz['progress'] = round( $user_quiz['progress'] );
	$user_quiz['url']      = '<a class="btn btn-default btn-close-quiz-modal-results" href="' . apply_filters( 'stm_lms_item_url_quiz_ended', STM_LMS_Course::item_url( $course_id, $quiz_id ) ) . '">' . esc_html__( 'Close', 'masterstudy-lms-learning-management-system' ) . '</a>';
	$user_quiz['url']      = apply_filters( 'user_answers__course_url', $user_quiz['url'], $source );
	$user_quiz['user_tries'] = $tries;
	$user_quiz['user_tries_msg'] = $user_tries_msg;

	do_action( 'stm_lms_quiz_' . $status, $user_id, $quiz_id, $user_quiz['progress'] );

	wp_send_json( $user_quiz );
}

function delete_user_course($user_id, $course_id) {
	$curriculum = get_post_meta( $course_id, 'curriculum', true );
	if ( empty( $curriculum ) ) {
		die;
	}

	$curriculum = explode( ',', $curriculum );

	foreach ( $curriculum as $item_id ) {

		$item_type = get_post_type( $item_id );

		if ( 'stm-lessons' === $item_type ) {
			STM_LMS_User_Manager_Course_User::reset_lesson( $user_id, $course_id, $item_id );
		} elseif ( 'stm-assignments' === $item_type ) {
			STM_LMS_User_Manager_Course_User::reset_assignment( $user_id, $course_id, $item_id );
		} elseif ( 'stm-quizzes' === $item_type ) {
			STM_LMS_User_Manager_Course_User::reset_quiz( $user_id, $course_id, $item_id );
		}
	}

	stm_lms_reset_user_answers( $course_id, $user_id );

	STM_LMS_Course::update_course_progress( $user_id, $course_id );

	stm_lms_get_delete_user_course( intval($user_id), intval($course_id) );
	$meta = STM_LMS_Helpers::parse_meta_field( $course_id );

	if ( ! empty( $meta['current_students'] ) && $meta['current_students'] > 0 ) {
		update_post_meta( $course_id, 'current_students', -- $meta['current_students'] );
	}
}
