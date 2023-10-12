<?php
define( 'STM_LMS_PLUS_ENABLED', true );

require_once STM_LMS_PRO_PLUS_ADDONS . '/email_branding/main.php';

add_filter(
	'masterstudy_lms_plugin_addons',
	function ( $addons ) {
		return array_merge(
			$addons,
			array(
				new MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\GoogleMeet(),
			)
		);
	}
);
