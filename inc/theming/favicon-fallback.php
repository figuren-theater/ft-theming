<?php
/**
 * Figuren_Theater Theming Favicon_Fallback.
 *
 * @package figuren-theater/ft-theming
 */

namespace Figuren_Theater\Theming\Favicon_Fallback;

use function add_action;
use function content_url;
use function get_blog_option;
use function wp_redirect; // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'do_faviconico', __NAMESPACE__ . '\\load' );
}

/**
 * Conditionally load a fallback favicon if needed.
 *
 * @return void
 */
function load(): void {
	/**
	 * Needs attention!
	 *
	 * @todo #10 Maybe a new trac ticket: has_site_icon() vs.  get_site_icon_url() vs. get_option('site_icon')
	 *
	 * Because there is some crazy things going on here:
	 *
	 * if ( has_site_icon() ) {
	 * if ( '' === \get_site_icon_url( 512, '', 0 ) ) {
	 * if ( get_option('site_icon') ) {
	 */
	if ( empty( get_blog_option( 0, 'site_icon' ) ) ) {
		return;
	}

	$url = content_url( '/favicon.ico' );

	/*
	 * The favicon request doesn't work with
	 * wp_safe_redirect( $url )
	 *
	 * So just go the old wa ignore phpcs yelling at us.
	 */
	if ( wp_redirect( $url ) ) { // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}
}
