<?php


/* Quit */
defined('ABSPATH') OR exit;


/* Register autoload */
spl_autoload_register('checksum_verifier_autoload');


/* Perform autoload */
function checksum_verifier_autoload($class) {
    if ( in_array( $class, array('Checksum_Verifier') ) ) {
        require_once(
            sprintf(
                '%s%s%s.class.php',
                dirname(__FILE__),
                DIRECTORY_SEPARATOR,
                strtolower($class)
            )
        );
    }
}