<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleCalendarEvent;
use MasterStudy\Lms\Repositories\AbstractRepository;

final class GoogleMeetRepository extends AbstractRepository {
	protected static array $fields_post_map = array(
		'title' => 'post_title',
	);

	protected static array $fields_meta_map = array(
		'summary'    => 'stm_gma_summary',
		'start_date' => 'stm_gma_start_date',
		'start_time' => 'stm_gma_start_time',
		'end_date'   => 'stm_gma_end_date',
		'end_time'   => 'stm_gma_end_time',
		'timezone'   => 'stm_gma_timezone',
		'visibility' => 'stm_gma_visibility',
	);

	protected static array $casts = array(
		'start_date' => 'int',
		'end_date'   => 'int',
	);

	protected static string $post_type = PostType::GOOGLE_MEET;

	public function save_google_event( int $post_id, array $data ): void {
		$start_date = masterstudy_lms_validate_google_meet_start_date( $data['start_date'] );
		$start_date = gmdate( 'Y-m-d', $start_date / 1000 );
		$end_date   = gmdate( 'Y-m-d', (int) $data['end_date'] / 1000 );

		$meeting_data = array(
			'meeting_name'    => $data['title'],
			'meeting_summary' => $data['summary'] ?? '',
			'start_date_time' => "{$start_date}T{$data['start_time']}:00",
			'end_date_time'   => "{$end_date}T{$data['end_time']}:00",
			'timezone'        => $data['timezone'],
			'visibility'      => $data['visibility'] ?? 'public',
		);

		GoogleCalendarEvent::save_google_event( $post_id, $meeting_data );
	}
}
