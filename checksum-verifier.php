<?php
/*
Plugin Name: Checksum Verifier
Text Domain: checksum_verifier
Domain Path: /lang
Description: Verifies MD5 checksums of WordPress core files, sends e-mail warning in case of threat.
Author: Sergej M&uuml;ller
Author URI: http://wpcoder.de
Plugin URI: https://wordpress.org/plugins/checksum-verifier/
License: GPLv2 or later
Version: 0.0.1
*/

/*
Copyright (C)  2014-2015 Sergej Müller

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/* Quit */
defined('ABSPATH') OR exit;


/* Constants */
define(
    'CHECKSUM_VERIFIER_BASE',
    plugin_basename(__FILE__)
);
define(
    'CHECKSUM_VERIFIER_CRON',
    'checksum_verifier_verify_files'
);


/* Register */
register_activation_hook(
    __FILE__,
    array(
        'Checksum_Verifier',
        'activation_hook'
    )
);
register_deactivation_hook(
    __FILE__,
    array(
        'Checksum_Verifier',
        'deactivation_hook'
    )
);
register_uninstall_hook(
    __FILE__,
    array(
        'Checksum_Verifier',
        'uninstall_hook'
    )
);


/* Hooks */
add_action(
    CHECKSUM_VERIFIER_CRON,
    array(
        'Checksum_Verifier',
        'verify_files'
    )
);
add_filter(
    'plugin_row_meta',
    array(
        'Checksum_Verifier',
        'plugin_meta'
    ),
    10,
    2
);


/* Autoload */
require_once(
    sprintf(
        '%s/inc/autoload.php',
        dirname(__FILE__)
    )
);