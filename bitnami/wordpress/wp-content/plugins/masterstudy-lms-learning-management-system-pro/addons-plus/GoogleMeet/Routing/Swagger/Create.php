<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class Create extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'title'      => array(
				'type'        => 'string',
				'description' => 'Meeting Name',
				'required'    => true,
			),
			'summary'    => array(
				'type'        => 'string',
				'description' => 'Meeting Summary',
				'required'    => false,
			),
			'start_date' => array(
				'type'        => 'integer',
				'description' => 'Meeting Start Date',
				'required'    => true,
			),
			'start_time' => array(
				'type'        => 'string',
				'description' => 'Meeting Start Time',
				'required'    => true,
			),
			'end_date'   => array(
				'type'        => 'integer',
				'description' => 'Meeting End Date',
				'required'    => true,
			),
			'end_time'   => array(
				'type'        => 'string',
				'description' => 'Meeting End Time',
				'required'    => true,
			),
			'timezone'   => array(
				'type'        => 'string',
				'description' => 'Meeting Timezone',
				'required'    => true,
			),
			'visibility' => array(
				'type'        => 'string',
				'description' => 'Quick Access',
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'id' => array(
				'type'        => 'integer',
				'description' => 'Google Meet ID',
			),
		);
	}

	public function get_summary(): string {
		return 'Create new Google Meet';
	}

	public function get_description(): string {
		return 'Create new Google Meet';
	}
}
