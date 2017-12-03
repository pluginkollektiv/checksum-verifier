<?php
/**
 * Checksum-Verifier: Autoloader
 *
 * @package Checksum-Verifier
 * @since   0.0.1
 */

// Quit.
defined( 'ABSPATH' ) || exit;

// Register autoload.
spl_autoload_register( 'checksum_verifier_autoload' );

/**
 * Perform autoload.
 *
 * @param string $class The classname.
 */
function checksum_verifier_autoload( $class ) {
	if ( in_array( $class, array( 'Checksum_Verifier' ), true ) ) {
		require_once sprintf(
			'%s%sclass-%s.php',
			dirname( __FILE__ ),
			DIRECTORY_SEPARATOR,
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}
