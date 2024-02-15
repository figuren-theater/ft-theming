<?php
/**
 * Figuren_Theater Theming No_Jquery_Migrate.
 *
 * @package figuren-theater/ft-theming
 */

namespace Figuren_Theater\Theming\No_Jquery_Migrate;

use WP_Scripts;
use function add_action;

/**
 * Bootstrap module, when enabled.
 *
 * Disable the message:
 * 'JQMIGRATE: Migrate is installed, version 1.4.1'
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'wp_default_scripts', __NAMESPACE__ . '\\load', 0 );
}

/**
 * Remove jQuery migrate from the list of dependecies for jQuery itself.
 *
 * @param WP_Scripts $scripts WP_Scripts instance (passed by reference).
 *
 * @return void
 */
function load( WP_Scripts $scripts ): void {
	if ( ! empty( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff(
			$scripts->registered['jquery']->deps,
			[
				'jquery-migrate',
			]
		);
	}
}
