<?php
/**
 * Figuren_Theater Theming WP_Better_Emails.
 *
 * @package figuren-theater/theming/wp_better_emails
 */

namespace Figuren_Theater\Theming\WP_Better_Emails;

use FT_VENDOR_DIR;

use Figuren_Theater;
use Figuren_Theater\Options;
use function Figuren_Theater\get_config;

use function add_action;
use function do_shortcode;
use function remove_submenu_page;
use function wp_specialchars_decode;

const BASENAME   = 'wp-better-emails/wpbe.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
	
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}


function load_plugin() : void {

	$config = Figuren_Theater\get_config()['modules']['theming'];
	if ( ! $config['wp-better-emails'] )
		return; // early

	require_once PLUGINPATH;

	// Remove plugins menu
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );

	add_filter( 'wpbe_tags', __NAMESPACE__ . '\\wpbe_tags' );
}


function filter_options() : void {
	
	// Plain-text default template
	$_plaintext = "
%content%

---
%impressum%

%home_url%
";
	// Email sent %date% @ %time%
	// For any requests, please contact %admin_email%";


	// HTML default template

	// TODO
	// $this->template = ''; // Add something nice here
	$_template = nl2br( $_plaintext );

	$_options = [
		'from_email'         => get_default_from_email(),
		'from_name'          => wp_specialchars_decode( get_default_from(), ENT_QUOTES ),
		'template'           => $_template,
		'plaintext_template' => $_plaintext
	];

	new Options\Option(
		'wpbe_options',
		$_options,
		BASENAME,
	);
}

function remove_menu() : void {
	remove_submenu_page( 'options-general.php', 'wpbe_options' );
}


/**
 * Get default from email
 * Copy pasted from wp_mail
 *
 * @see wp_mail
 * @return string Default from email
*/
function get_default_from(){
	$sitename = \strtolower( $_SERVER['SERVER_NAME'] );
	if ( \substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = \substr( $sitename, 4 );
	}
	return $sitename;
}


function get_default_from_email() : string {
	return 'email-roboter@' . get_default_from();
}


function wpbe_tags( array $tags ) : array
{
	$tags['impressum'] = do_shortcode( '[impressum titles="0"]' );

	return $tags;
}
