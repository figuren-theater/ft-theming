<?php
/**
 * Figuren_Theater Theming.
 *
 * @package figuren-theater/theming
 */

namespace Figuren_Theater\Theming;

use Altis;
use function Altis\register_module;


/**
 * Register module.
 */
function register() {

	$default_settings = [
		'enabled'          => true, // needs to be set
		'wp-better-emails' => false,
	];
	$options = [
		'defaults' => $default_settings,
	];

	Altis\register_module(
		'theming',
		DIRECTORY,
		'Theming',
		$options,
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	// Plugins
	WP_Better_Emails\bootstrap();
	
	// Best practices
	Defer_Async_Loader\bootstrap();
	No_Jquery_Migrate\bootstrap();
}
