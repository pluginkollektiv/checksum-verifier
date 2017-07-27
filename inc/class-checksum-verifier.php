<?php
/**
 * Checksum-Verifier: Main Class
 *
 * @package Checksum-Verifier
 * @since   0.0.1
 */

// Quit.
defined( 'ABSPATH' ) || exit;


/**
 * Checksum_Verifier
 *
 * @since    0.0.1
 */
class Checksum_Verifier {

	/**
	 * Perform the check
	 *
	 * @since   0.0.1
	 */
	public static function verify_files() {
		// Get checksums via API.
		$checksums = self::_get_checksums();
		if ( ! $checksums ) {
			return;
		}

		// Loop files and match checksums.
		$matches = self::_match_checksums( $checksums );
		if ( ! $matches ) {
			return;
		}

		// Notification mail.
		self::_notify_admin( $matches );
	}


	/**
	 * Get file checksums.
	 *
	 * @since   0.0.1
	 *
	 * @return  array  Checksums getting from API.
	 */
	private static function _get_checksums() {
		// Blog information.
		$version  = get_bloginfo( 'version' );
		$language = get_locale();

		// Transient name.
		$transient = sprintf(
			'checksums_%s',
			base64_encode( $version . $language )
		);

		// Read from cache.
		$checksums = get_site_transient( $transient );
		if ( $checksums ) {
			return $checksums;
		}

		// Start API request.
		$response = wp_remote_get(
			add_query_arg(
				array(
					'version' => $version,
					'locale'  => $language,
				),
				'https://api.wordpress.org/core/checksums/1.0/'
			)
		);

		// Check response code.
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return;
		}

		// JSON magic.
		$json = json_decode(
			wp_remote_retrieve_body( $response )
		);

		// Exit on JSON error.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return;
		}

		// Checksums exists?
		if ( empty( $json->checksums ) ) {
			return;
		}

		// Eat it.
		$checksums = $json->checksums;

		// Save into the cache.
		set_site_transient(
			$transient,
			$checksums,
			DAY_IN_SECONDS
		);

		return $checksums;
	}


	/**
	 * Matching of MD5 hashes
	 *
	 * @since   0.0.1
	 *
	 * @hook    array  checksum_verifier_ignore_files
	 *
	 * @param   array $checksums File checksums.
	 * @return  array            File paths
	 */
	private static function _match_checksums( $checksums ) {
		// Ignore files filter.
		$ignore_files = (array) apply_filters(
			'checksum_verifier_ignore_files',
			array(
				'wp-config-sample.php',
				'wp-includes/version.php',
			)
		);

		// Init matches.
		$matches = array();

		// Loop files.
		foreach ( $checksums as $file => $checksum ) {
			// Skip ignored files.
			if ( in_array( $file, $ignore_files, true ) ) {
				continue;
			}

			// File path.
			$file_path = ABSPATH . $file;

			// File check.
			if ( 0 !== validate_file( $file_path ) || ! file_exists( $file_path ) ) {
				continue;
			}

			// Compare MD5 hashes.
			if ( md5_file( $file_path ) !== $checksum ) {
				$matches[] = $file;
			}
		}

		return $matches;
	}


	/**
	 * Admin notification mail.
	 *
	 * @since   0.0.1
	 *
	 * @param   array $matches File paths.
	 * @return  void
	 */
	private static function _notify_admin( $matches ) {
		// Text domain on demand.
		load_plugin_textdomain(
			'checksum-verifier',
			false,
			dirname( CHECKSUM_VERIFIER_BASE ) . '/lang'
		);

		// Mail recipient.
		$to = get_bloginfo( 'admin_email' );

		// Mail subject.
		$subject = wp_specialchars_decode(
			sprintf(
				'[%s] %s',
				get_bloginfo( 'name' ),
				esc_html__( 'Checksum Verifier Alert', 'checksum-verifier' )
			),
			ENT_QUOTES
		);

		// Mail body.
		$body = wp_specialchars_decode(
			sprintf(
				"%s:\r\n\r\n- %s",
				esc_html__( 'Official checksums do not match for the following files', 'checksum-verifier' ),
				implode( "\r\n- ", $matches )
			),
			ENT_QUOTES
		);

		// Send!
		wp_mail(
			$to,
			$subject,
			$body
		);

		// Write to log.
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			// @codingStandardsIgnoreLine Ignore this call for now...
			error_log(
				sprintf(
					'%s: %s',
					esc_html__( 'Checksums do not match for the following files', 'checksum-verifier' ),
					implode( ', ', $matches )
				)
			);
		}
	}


	/**
	 * Plugin meta rows.
	 *
	 * @since   0.0.1
	 *
	 * @param   array  $input Exists plugin rows.
	 * @param   string $file  Current plugin file.
	 *
	 * @return  array         Extended plugin rows.
	 */
	public static function plugin_meta( $input, $file ) {
		// Skip other plugins.
		if ( CHECKSUM_VERIFIER_BASE !== $file ) {
			return $input;
		}

		// Next update time.
		$timestamp = wp_next_scheduled( CHECKSUM_VERIFIER_CRON );
		if ( $timestamp ) {
			$scheduled = human_time_diff( time(), $timestamp );
		} else {
			$scheduled = esc_html( 'Never' );
		}

		// Plugin rows.
		return array_merge(
			$input,
			array(
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=TD4AMD2D8EMZW" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Donate', 'checksum-verifier' ) . '</a>',
				'<a href="https://wordpress.org/support/plugin/checksum-verifier" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Support', 'checksum-verifier' ) . '</a>',
				sprintf(
					'%s %s',
					esc_html__( 'Next check in', 'checksum-verifier' ),
					$scheduled
				),
			)
		);
	}


	/**
	 * Plugin activation hook.
	 *
	 * @since   0.0.1
	 */
	public static function activation_hook() {
		wp_schedule_event(
			time(),
			'daily',
			CHECKSUM_VERIFIER_CRON
		);
	}


	/**
	 * Plugin deactivation hook.
	 *
	 * @since   0.0.1
	 */
	public static function deactivation_hook() {
		wp_clear_scheduled_hook(
			CHECKSUM_VERIFIER_CRON
		);
	}


	/**
	 * Plugin uninstall hook.
	 *
	 * @since   0.0.1
	 */
	public static function uninstall_hook() {
		global $wpdb;

		// Execute.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE ( option_name LIKE (%s) OR option_name LIKE (%s) )",
				'%\\_transient\\_checksums%',
				'%\\_transient\\_timeout\\_checksums%'
			)
		);
	}
}
