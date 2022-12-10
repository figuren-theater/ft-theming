<?php
/**
 * Figuren_Theater Theming Defer_Async_Loader.
 *
 * Add async & defer attribute to asset links.
 *
 * Enqueue your scripts as normal,
 * and simply add the #asyncload, or #deferload string
 * to any script you want to async or defer.
 *
 * @package figuren-theater/theming/defer_async_loader
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
 */
function bootstrap() {
	
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load', 0 );
}


function load() : void {

	if (is_admin())
		return;

	add_filter('script_loader_tag', 'load_async', 5, 3);
	add_filter('script_loader_tag', 'load_defered', 5, 3);
}

/**
 * This function is simply looking for the #asyncload string, 
 * and if found, appending async='async' to the URL.
 *
 */
function load_async( string $tag, string $handle, string $src) : string {

	// if this is alrady done, do nothing and return original $tag
	if ( strpos( $tag, 'defer' ) || strpos( $tag, 'async' ) )
		return $tag;
	
	$scripts_to_async = array_flip( 
		apply_filters( 
			__NAMESPACE__ . '\\scripts_to_async', 
			[]
		)
	);

	if (
		// manage all third-party-assets,
		// we havent control over
		isset($scripts_to_async[ $handle ] )
		||
		// 
		strpos( $src, '#asyncload')
	)
	{
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
 * This function is simply looking for the #deferload string, 
 * and if found, appending defer='defer' to the URL.
 *
 */
function load_defered( string $tag, string $handle, string $src) : string {

	// if this is alrady done, do nothing and return original $tag
	if ( strpos( $tag, 'defer' ) || strpos( $tag, 'async' ) )
		return $tag;

	$scripts_to_defer = array_flip( 
		apply_filters( 
			__NAMESPACE__ . '\\scripts_to_defer', [
			// 
			// 'jquery',
			// 'jquery-core',
			// 
			// 'cookie-notice-front',
			// 
			// 'contact-form-7',
			// 
			'mediaelement-core',
			'mediaelement-migrate',
			'mediaelement-vimeo',
			'wp-mediaelement',
			// 
			// 'photoswipe-lib',
			// 'photoswipe-ui-default',
			// 'photoswipe',
			]
		)
	);

	if (
		// manage all third-party-assets,
		// we havent control over
		isset($scripts_to_defer[ $handle ] )
		||
		// or use it directly as a temporary appendix
		// on registered scripts (and styles)
		strpos( $src, '#deferload')
	)
	{
		// returns the html-tag for calling the asset
		// including an attr of 'defer'
		// in case the URL contained our temporary 
		// appendix, it will get removed
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
