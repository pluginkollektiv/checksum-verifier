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
		$checksums = self::get_checksums();
		if ( ! $checksums ) {
			return;
		}

		// Loop files and match checksums.
		$matches = self::match_checksums( $checksums );
		if ( ! $matches ) {
			return;
		}

		// Notification mail.
		self::notify_admin( $matches );
	}


	/**
	 * Get file checksums.
	 *
	 * @since   0.0.1
	 *
	 * @return  array  Checksums getting from API.
	 */
	private static function get_checksums() {
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
	private static function match_checksums( $checksums ) {
		// Ignore files filter.
		$ignore_files = (array) apply_filters(
			'checksum_verifier_ignore_files',
			array(
				'wp-config-sample.php',
				'wp-includes/version.php',
				'readme.html',      // Default readme file.
				'readme-ja.html',   // Japanese readme, shipped up to 3.9 (ja).
				'liesmich.html',    // German readme (de_DE).
				'olvasdel.html',    // Hungarian readme (hu_HU).
				'procitajme.html',  // Croatian readme (hr).
			)
		);

		// Init matches.
		$matches = array();

		// Loop files.
		foreach ( $checksums as $file => $checksum ) {
			// Skip ignored files and wp-content directory.
			if ( 0 === strpos( $file, 'wp-content/' ) || in_array( $file, $ignore_files, true ) ) {
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
	private static function notify_admin( $matches ) {
		// Text domain on demand.
		load_plugin_textdomain( 'checksum-verifier' );

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

	/**
	 * Add deprecation notice in WP Admin.
	 */
	public static function add_deprecation_notice() {
		$active = is_plugin_active( 'antivirus/antivirus.php' );
		?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'Checksum Verifier is deprecated. Its functionality is integrated into AntiVirus now.', 'checksum-verifier' ); ?></p>
			<p><?php esc_html_e( 'Switch to AntiVirus now:', 'checksum-verifier ' ); ?></p>
			<ol>
				<li><?php esc_html_e( 'Install and activate AntiVirus 1.4.0+.', 'checksum-verifier' ); ?>
					<?php
					if ( ! $active ) {
							$install_url = add_query_arg(
								array(
									's' => 'pluginkollektiv',
									'tab' => 'search',
									'type' => 'author',
								),
								admin_url( '/plugin-install.php' )
							);
						?>
						<a href="<?php echo esc_attr( $install_url ); ?>">
							<?php esc_html_e( 'Install now.', 'checksum-verifier' ); ?>
						</a>
					<?php } ?>
				</li>
				<li><?php esc_html_e( 'Enable option "Checksum verification of WP core files".', 'checksum-verifier' ); ?>
					<?php
					if ( $active ) {
							$options_url = add_query_arg(
								array(
									'page' => 'antivirus',
								),
								admin_url( '/options-general.php' )
							);
						?>
						<a href="<?php echo esc_attr( $options_url ); ?>">
							<?php esc_html_e( 'Go to AntiVirus settings.', 'checksum-verifier' ); ?>
						</a>
					<?php } ?>
				</li>
				<li><?php esc_html_e( 'Deactivate and uninstall Checksum Verifier.', 'checksum-verifier' ); ?></li>
			</ol>
		</div>
		<?php
	}
}
