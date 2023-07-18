<?php
/**
 * Figuren_Theater Theming Defer_Async_Loader.
 *
 * Add async & defer attribute to asset links.
 *
 * @todo #11 Re-factor this after 6.3 was released, which provides native async & defer handling
 *
 * Enqueue your scripts as normal,
 * and simply add the #asyncload, or #deferload string
 * to any script you want to async or defer.
 *
 * @package figuren-theater/ft-theming
 */

namespace Figuren_Theater\Theming\Defer_Async_Loader;

use function add_action;
use function add_filter;
use function apply_filters;
use function is_admin;

/**
 * Bootstrap module, when enabled.
 *
 * Async and Defer assets with PHP in WordPress.
 * 08/2019
 * https://uncoverwp.com/course/async-defer-assets-in-wordpress/
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load', 0 );
}

/**
 * Conditionally load the modifications.
 *
 * @return void
 */
function load() : void {

	if ( is_admin() ) {
		return;
	}

	add_filter( 'script_loader_tag', __NAMESPACE__ . '\\load_async', 5, 3 );
	add_filter( 'script_loader_tag', __NAMESPACE__ . '\\load_defered', 5, 3 );
}

/**
 * Filters the HTML script tag of an enqueued script.
 *
 * This function is simply looking for the #asyncload string,
 * and if found, appending async='async' to the URL.
 *
 * @param string $tag     The tag for the enqueued script.
 * @param string $handle  The script's registered handle.
 * @param string $src     The script's source URL.
 *
 * @return string
 */
function load_async( string $tag, string $handle, string $src ) : string {

	// If this is alrady done, do nothing and return original $tag.
	if ( strpos( $tag, 'defer' ) || strpos( $tag, 'async' ) ) {
		return $tag;
	}

	$scripts_to_async = array_flip(
		apply_filters(
			__NAMESPACE__ . '\\scripts_to_async',
			[]
		)
	);

	if (
		// Manage all third-party-assets, we havent control over.
		isset( $scripts_to_async[ $handle ] )
		||
				strpos( $src, '#asyncload' )
	) {
		return str_replace(
			[
				' src',
				'#asyncload',
			],
			[
				' async src',
				'',
			],
			$tag
		);
	}

	return $tag;
}

/**
 * Filters the HTML script tag of an enqueued script.
 *
 * This function is simply looking for the #deferload string,
 * and if found, appending defer='defer' to the URL.
 *
 * @param string $tag     The tag for the enqueued script.
 * @param string $handle  The script's registered handle.
 * @param string $src     The script's source URL.
 *
 * @return string
 */
function load_defered( string $tag, string $handle, string $src ) : string {

	// If this is alrady done, do nothing and return original $tag.
	if ( strpos( $tag, 'defer' ) || strpos( $tag, 'async' ) ) {
		return $tag;
	}

	$scripts_to_defer = array_flip(
		apply_filters(
			__NAMESPACE__ . '\\scripts_to_defer', [
				'mediaelement-core',
				'mediaelement-migrate',
				'mediaelement-vimeo',
				'wp-mediaelement',
			]
		)
	);

	if (
		// Manage all third-party-assets, we havent control over.
		isset( $scripts_to_defer[ $handle ] )
		||
		// Or use it directly as a temporary appendix on registered scripts (and styles).
		strpos( $src, '#deferload' )
	) {
		// Returns the html-tag for calling the asset
		// including an attr of 'defer'
		// in case the URL contained our temporary
		// appendix, it will get removed.
		return str_replace(
			[
				' src',
				'#deferload',
			],
			[
				' defer src',
				'',
			],
			$tag
		);
	}

	return $tag;
}
