<?php
/**
 * Checksum Verifier
 *
 * @package     ChecksumVerifier
 * @author      pluginkollektiv
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Checksum Verifier
 * Plugin URI:  https://wordpress.org/plugins/checksum-verifier/
 * Description: Verifies MD5 checksums of WordPress core files, sends e-mail to the mail address of your admin user warning in case of threat. Just activate it and you are done.
 * Version:     0.0.4
 * Author:      pluginkollektiv
 * Author URI:  https://pluginkollektiv.org/
 * Text Domain: checksum-verifier
 * License:     GPLv2 or later
 *
 * Copyright (C)  2014-2015 Sergej Müller
 * Copyright (C)  2016-2017 pluginkollektiv
 *
 * Checksum Verifier is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Checksum Verifier is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Checksum Verifier. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
 */

// Quit.
defined( 'ABSPATH' ) || exit;


// Constants.
define(
	'CHECKSUM_VERIFIER_BASE',
	plugin_basename( __FILE__ )
);
define(
	'CHECKSUM_VERIFIER_CRON',
	'checksum_verifier_verify_files'
);


// Register.
register_activation_hook(
	__FILE__,
	array(
		'Checksum_Verifier',
		'activation_hook',
	)
);
register_deactivation_hook(
	__FILE__,
	array(
		'Checksum_Verifier',
		'deactivation_hook',
	)
);
register_uninstall_hook(
	__FILE__,
	array(
		'Checksum_Verifier',
		'uninstall_hook',
	)
);

// Hooks.
add_action(
	CHECKSUM_VERIFIER_CRON,
	array(
		'Checksum_Verifier',
		'verify_files',
	)
);
add_filter(
	'plugin_row_meta',
	array(
		'Checksum_Verifier',
		'plugin_meta',
	),
	10,
	2
);

add_action(
	is_network_admin() ? 'network_admin_notices' : 'admin_notices',
	array(
		'Checksum_Verifier',
		'add_deprecation_notice',
	)
);

// Autoload.
require_once sprintf(
	'%s/inc/autoload.php',
	dirname( __FILE__ )
);
