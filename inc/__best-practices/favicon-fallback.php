<?php
/**
 * Figuren_Theater Theming Favicon_Fallback.
 *
 * @package figuren-theater/theming/favicon_fallback
 */

namespace Figuren_Theater\Theming\Favicon_Fallback;

use FT_CORESITES;

use function add_action;
use function get_site_url;
use function has_site_icon;
use function wp_redirect;


/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {
	add_action('do_faviconico', __NAMESPACE__ . '\\load' );
}


function load() : void {

	// I should open
	// a new trac ticket for this
	// if ( has_site_icon() ) {
	// if ( '' === \get_site_icon_url( 512, '', 0 ) ) {
	// if ( get_option('site_icon') ) {
	if ( empty(\get_blog_option( 0, 'site_icon')) ) {
		return;
	}

	// VARIANT 1
	// \wp_redirect(\get_site_icon_url(32)); // set as empty, do not fallback to the W of WordPress

	// VARIANT 2
	// if the favicon aka site-icon not exists , go on
	// and get the ID of the 'main' favicon from the network_blog
	// this is the one to show
	$ft_coresites_ids = array_flip( FT_CORESITES );
	$root_site_id = (int) $ft_coresites_ids['root'];

	$url = \WP_CONTENT_URL . '/favicon.ico/';

	// $url = \get_site_icon_url( 32, $url, $root_site_id );

	// this favicon request
	// doesn't work with 
	// wp_safe_redirect( $url )
	// 
	// just go the old way
	if ( wp_redirect( $url ) ) {
		exit;
	}
}
