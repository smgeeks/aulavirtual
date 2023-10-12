<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

class UpdateAccessSettingsController {
	private CourseRepository $course_repository;

	public function __construct() {
		$this->course_repository = new CourseRepository();
	}

	public function __invoke( $course_id, WP_REST_Request $request ): WP_REST_Response {
		if ( ! $this->course_repository->exists( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		$rules = apply_filters(
			'masterstudy_lms_course_access_validation_rules',
			array(
				'expiration' => 'required|boolean',
				'end_time'   => 'required_if_accepted,expiration|nullable|integer|min,1',
			)
		);

		$validator = new Validator(
			$request->get_json_params(),
			$rules
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data = $validator->get_validated();

		$this->course_repository->update_access( $course_id, $data );

		return WpResponseFactory::ok();
	}
}
