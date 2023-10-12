<?php
/**
 * A register and information repository of user capabilities.
 *
 * The class will also handle the registration and mapping of user capabilities.
 *
 * @todo should this be part of the model schema? There are things that might require caps and are NOT models...
 *
 * @since   TBD
 *
 * @package SolidAffiliate\Lib
 */

namespace SolidAffiliate\Lib;

/**
 * Class Capabilities
 *
 * @since   TBD
 *
 * @package SolidAffiliate\Lib
 */
class Capabilities {
	/**
	 * The capability a user shoudl have to insert and update Affiliates.
	 * @var string
	 */
	const EDIT_AFFILIATES = 'edit_users';
}
