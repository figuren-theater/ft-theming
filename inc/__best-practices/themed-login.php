<?php # -*- coding: utf-8 -*-
/**
 * Figuren_Theater Theming Themed_Login.
 *
 * @package figuren-theater/theming/themed_login
 */

namespace Figuren_Theater\Theming\Themed_Login;

use Figuren_Theater\Network\Admin_UI;

use function add_action;
use function add_filter;
use function esc_attr;
use function esc_url;
use function get_site_icon_url;



/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {
	// the earliest 'do_action' of wp-login.php
	add_action('login_enqueue_scripts', __NAMESPACE__ . '\\load', 0 );
}


function load() : void {

	// Removes the language dropdown from the login screen.
	add_filter( 'login_display_language_dropdown', '__return_false' );

	// use theme colors for login-page
	add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\ft_login_logo_image_styles', 100 );

	// use site-icon as Login-Logo
	add_filter( 'login_headertext', __NAMESPACE__ . '\\ft_login_logo_image' );

	// Link the Login-Logo to its related Website, not WordPress
	/*function ft_login_url() {
		return get_site_url();
	}*/
	add_filter( 'login_headerurl', 'get_site_url' );
}


function ft_get_relevant_colors() : array {
	// 1. defaults
	$relevant_colors = [];
	
	// 1.1. defaults are swapped (Lets's see how (long) this will work.)
	// $relevant_colors['ft_background'] = '#f1f1f1';
	// $relevant_colors['ft_text']       = '#000';
	$relevant_colors['ft_background'] = '#000';
	$relevant_colors['ft_accent']     = '#808080';
	$relevant_colors['ft_text']       = '#f1f1f1';

	// 2. guard clause
	// $ft_global_styles =  ft_use_global_styles_to_login();
	$ft_global_styles = Admin_UI\Color_Scheme::get_all_colors();

	if ( empty( $ft_global_styles ) )
		return $relevant_colors;


	// 2.1. assignments are swapped, too (Lets's see how (long) this will work.)
	$relevant_colors['ft_background'] = ( isset( $ft_global_styles['foreground'] ) ) ? $ft_global_styles['foreground'] : $relevant_colors['ft_background'];
	
	// @TODO // THAT is why we need a own f.t ThemeProcessor API
	$relevant_colors['ft_accent']     = ( isset( $ft_global_styles['primary'] ) ) ? $ft_global_styles['primary'] : $relevant_colors['ft_accent']; 

	// @TODO // THAT is why we need a own f.t ThemeProcessor API
	// $ft_secondary  = $ft_global_styles['secondary']; 

	// @TODO // THAT is why we need a own f.t ThemeProcessor API
	$relevant_colors['ft_text']       = ( isset( $ft_global_styles['background'] ) ) ? $ft_global_styles['background'] : $relevant_colors['ft_text']; 

	return $relevant_colors;
}
/**
 * DISABLED: 4 beeing unused and (propably) merged insied the pwa-plugin-handler
 * 
 * @since 2022.10.20
 * 
 * 
// add_action( 'after_setup_theme', 'ft_prepare_manifest_data' );
function ft_prepare_manifest_data($value='')
{
// die(var_export($_SERVER['REQUEST_URI'],true));


	if( '/wp-json/wp/v2/web-app-manifest' !== $_SERVER['REQUEST_URI'] )
		return;

	add_theme_support( 'theme-color' );

	// this enables our colors
	// to be received by the default fn of the pwa plugin
	// @see plugins\pwa\wp-includes\class-wp-web-app-manifest.php#L168
	add_theme_support( 'custom-background' );
	// this replaces the default (because unset) background-color,
	// by the one extracted from our FSE "theme-engine" ()
	add_filter( 'theme_mod_background_color', 'ft_filter_manifest_background_color' );


}
function ft_filter_manifest_background_color($background_color = '') : string
{
	$relevant_colors = ft_get_relevant_colors();

	// because the theme_mod 'background_color' is delivered as hex without #
	// we have top clean up our color before returning it
	return substr($relevant_colors['ft_background'], 1);
}
*/

function ft_login_logo_image_styles() { 

	extract( ft_get_relevant_colors() );
?>

	<style type="text/css">
		body.login {
			background-color: <?php echo $ft_background; ?>;
			padding-bottom: 150px; /* helper to make the native lang-switch visible behind the fixed notice from llar */
		}
		body.login p#backtoblog,
		body.login p#nav {
			width: calc(49% - 48px);
			display: inline-block;
		}
		body.login a,
		body.login a:visited,
		body.login a:focus,
		body.login a:active,
		body.login #backtoblog a,
		body.login #nav a {
			color: <?php echo $ft_accent; ?>;
		}
		body.login a:hover,
		body.login #backtoblog a:hover,
		body.login #nav a:hover,
		body.login h1 a:hover,
		body.login a:focus,
		body.login #backtoblog a:focus,
		body.login #nav a:focus,
		body.login h1 a:focus {
			color: <?php echo $ft_text; ?>;
		}

		body.login.wp-core-ui .button {
			background-color: <?php echo $ft_accent; ?>;
			border-color: <?php echo $ft_accent; ?>;
			color: <?php echo $ft_text; ?>;
		}

		body.login.wp-core-ui .button-primary {
			box-shadow: none;
			text-shadow: none;
			width: 100%;
			margin-top: 16px;
			padding: 6px 12px 6px 12px;
			height: auto;
			font-size: 16px;
		}
		body.login.wp-core-ui .button-primary:hover {
			background-color: <?php echo $ft_background; ?>;
			border-color: <?php echo $ft_background; ?>;
			box-shadow: none;
			text-shadow: none;
		}

		body.login form#loginform {
			background-color: <?php echo $ft_text; ?>;
		}

		#login h1 a,
		.login h1 a {
			background-size: auto;
			background-image: none;
			background-position: center center;
			text-indent: 0;
			width: auto;
			height: auto;
			/*max-height: 170px;*/
		}

		#login h1 a img,
		.login h1 a img {
			max-width: 120px;
			height: auto !important;
		}

		#login .language-switcher label .dashicons,
		.login .language-switcher label .dashicons {
			color: <?php echo $ft_text; ?>;
		}

	</style>
	<?php
	}


/**
 * Set branded login form logo
 *
 * With a fall back for the f.t Plattform Logo, if none is set.
 * Requires WordPress v5.2+ in order to use the 'login_headertext' filter
 *
 * @param      string $login_header_text [description]
 * @return     string                    [description]
 */
function ft_login_logo_image( string $login_header_text ) : string {

	$_logo_width = 100;
	$_logo_src   = get_site_icon_url( 
		$_logo_width,
		// fallback to the logo of figuren.theater
		get_site_icon_url( $_logo_width, '', 1 )
	);

	// Change the 'login_header_text' to display our new logo.
	if ( esc_url( $_logo_src ) )
		$login_header_text = sprintf( 
			'<img src="%1$s" width="%2$s" \>',
			esc_attr( $_logo_src ),
			$_logo_width
		);

	return $login_header_text;
}
