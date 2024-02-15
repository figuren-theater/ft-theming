<?php
/**
 * Figuren_Theater Theming WP_Better_Emails.
 *
 * @package figuren-theater/theming/wp_better_emails
 */

namespace Figuren_Theater\Theming\WP_Better_Emails;

use Figuren_Theater;

use Figuren_Theater\Options;
use FT_VENDOR_DIR;
use function add_action;

use function do_shortcode;
use function remove_submenu_page;
use function wp_specialchars_decode;

const BASENAME   = 'wp-better-emails/wpbe.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['theming'];
	if ( ! $config['wp-better-emails'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );

	add_filter( 'wpbe_tags', __NAMESPACE__ . '\\wpbe_tags' );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options(): void {
	/*
	 * Plain-text default template
	 */
	$_plaintext = '
%content%

---
%impressum%

%home_url%
';

	/**
	 * HTML default template
	 *
	 * @todo    #9  Add a nice default for all emails
	 *
	 * @example Email sent %date% @ %time%
	 *          For any requests, please contact %admin_email%"
	 */
	$_template = nl2br( $_plaintext );

	$_options = [
		'from_email'         => get_default_from_email(),
		'from_name'          => wp_specialchars_decode( get_default_from(), ENT_QUOTES ),
		'template'           => $_template,
		'plaintext_template' => $_plaintext,
	];

	new Options\Option(
		'wpbe_options',
		$_options,
		BASENAME
	);
}

/**
 * Remove Submenu from 'Settings'
 *
 * @return void
 */
function remove_menu(): void {
	remove_submenu_page( 'options-general.php', 'wpbe_options' );
}

/**
 * Get default from email
 * Copy pasted from wp_mail
 *
 * @see wp_mail
 *
 * @return string Default from email
 */
function get_default_from() {
	$sitename = \strtolower( (string) getenv( 'SERVER_NAME' ) );
	if ( \substr( $sitename, 0, 4 ) === 'www.' ) {
		$sitename = \substr( $sitename, 4 );
	}
	return $sitename;
}

/**
 * Get Adress to send emails from
 *
 * @return string
 */
function get_default_from_email(): string {

	if ( getenv( 'FT_SMTP_USER' ) ) {
		return getenv( 'FT_SMTP_USER' );
	}

	return 'email-roboter@' . get_default_from();
}

/**
 * Setup custom tags to be used in email-templates with wpbe.
 *
 * @param array<string, string> $tags List of rendered html, keyed by custom name.
 *
 * @return array<string, string>
 */
function wpbe_tags( array $tags ): array {
	$tags['impressum'] = do_shortcode( '[impressum titles="0"]' );

	return $tags;
}
