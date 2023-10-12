<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

wp_enqueue_style( 'stm-sync-meetings-css', STM_LMS_PRO_URL . '/addons-plus/GoogleMeet/Settings/components/sync-meetings/css/sync-meetings.css', null, get_bloginfo( 'version' ), 'all' );
wp_enqueue_script( 'stm-sync-meetings-js', STM_LMS_PRO_URL . '/addons-plus/GoogleMeet/Settings/components/sync-meetings/js/sync-meetings.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
wp_localize_script(
	'stm-sync-meetings-js',
	'stm_google_meet_ajax_variable',
	array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'stm-lms-gm-nonce' ),
	)
);
?>

<div class="wpcfto_generic_field wpcfto_generic_field__select" field_data="[object Object]">
	<div class="wpcfto-field-aside">
		<label class="wpcfto-field-aside__label"><?php echo esc_html__( 'Sync Meet Attendees', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
		<div class="wpcfto-field-description wpcfto-field-description__before description">
			<?php echo esc_html__( 'Synchronize calendar attendees with students enrolled in all courses that have meetings.', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</div>
		<p id="sync-error-message"></p>
	</div>
	<div class="wpcfto-field-content">
		<div class="wpcfto-admin-btns">
			<div class="credential-buttons">
				<button class="btn-outlined nuxy_reset_sync_meetings">
					<?php echo esc_html__( 'Update Calendar', 'masterstudy-lms-learning-management-system-pro' ); ?>
					<i class="fa fa-refresh fa-spin installing"></i>
					<i class="fa fa-check downloaded" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>
	<div>&nbsp;</div>
</div>
