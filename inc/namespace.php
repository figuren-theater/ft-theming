<?php
/**
 * Figuren_Theater Theming.
 *
 * @package figuren-theater/theming
 */

namespace Figuren_Theater\Theming;

use Altis;

use function wp_get_global_settings;
use function wp_list_pluck;

/**
 * Register module.
 *
 * @return void
 */
function register(): void {

	$default_settings = [
		'enabled'          => true, // Needs to be set.
		'wp-better-emails' => false,
	];
	$options          = [
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
 *
 * @return void
 */
function bootstrap(): void {

	// Plugins.
	WP_Better_Emails\bootstrap();

	// Best practices.
	Defer_Async_Loader\bootstrap();
	Favicon_Fallback\bootstrap();
	No_Jquery_Migrate\bootstrap();
	Themed_Login\bootstrap();
}

/**
 * Function to get all the color settings resulting of merging core, theme, and user data.
 *
 * @return array<string, string> The colors to retrieve indexed by their slug.
 */
function get_all_colors(): array {
	$_global_settings = wp_get_global_settings( [ 'color', 'palette' ] );
	if ( ! \is_array( $_global_settings ) || ! isset( $_global_settings['theme'] ) || ! \is_array( $_global_settings['theme'] ) ) {
		return [];
	}

	return wp_list_pluck(
		$_global_settings['theme'],
		'color',
		'slug'
	);
}
