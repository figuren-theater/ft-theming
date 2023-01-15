<?php
/**
 * Figuren_Theater Theming No_Jquery_Migrate.
 *
 * @package figuren-theater/theming/no_jquery_migrate
 */

namespace Figuren_Theater\Theming\No_Jquery_Migrate;

use function add_action;

/**
 * Bootstrap module, when enabled.
 *
 * Disable the message:
 * 'JQMIGRATE: Migrate is installed, version 1.4.1'
 */
function bootstrap() {
	add_action( 'wp_default_scripts', __NAMESPACE__ . '\\load', 0 );
}


function load( $scripts ) : void {
	if ( ! empty( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff( 
			$scripts->registered['jquery']->deps, 
			['jquery-migrate']
		);
	}
}
