<?php
/**
 * Figuren_Theater Theming Post_Type_Templates.
 *
 * @package figuren-theater/ft-theming
 */

namespace Figuren_Theater\Theming\Post_Type_Templates;

use Figuren_Theater\Theming;
use function add_action;

const PT_SUPPORT          = 'post-type-templates';
const TEMPLATES_DIRECTORY = Theming\DIRECTORY . '/templates/post-type-templates/'; 

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'init', __NAMESPACE__ . '\\load', 0 );
}

/**
 * Conditionally load if needed.
 * 
 * Registers ...
 *
 * @return void
 */
function load(): void {
	
	\add_post_type_support(
		'page',
		PT_SUPPORT,
		[
			'templates' => [
				'blank.php' => \_x( 'Blank', 'Template Title', 'figurentheater' ),
			],
			'path'      => TEMPLATES_DIRECTORY,
		]
	);
	
	\array_map(
		__NAMESPACE__ . '\\register_post_type_template',
		\get_post_types_by_support( PT_SUPPORT )
	);
}


/**
 * Registers new templates per post_type independent from the theme.
 *
 * @param  string $post_type  The slug of the post_type that this template is getting registered for.
 *
 * @return void
 */
function register_post_type_template( string $post_type ): void {
	
	$post_type_supports = \get_all_post_type_supports( $post_type )[ PT_SUPPORT ];
	if ( empty( $post_type_supports ) ) {
		return;
	}
	
	if ( empty( $post_type_supports['templates'] ) || ! \is_array( $post_type_supports['templates'] ) ) {
		return;
	}
	
	if ( empty( $post_type_supports['path'] ) || ! \is_string( $post_type_supports['path'] ) || ! isset( $post_type_supports['path'] ) ) {
		// Try to provide a fallback, if no path was given.
		// This allows other ft-modules to call add_post_type_support 
		// with just the name of the template from the ft-theming module, that should be used.
		$post_type_supports['path'] = TEMPLATES_DIRECTORY;
	}

	// Checks for file existence are done inside the Loader class.
	new Loader( 
		$post_type_supports['templates'], 
		$post_type_supports['path'], 
		$post_type 
	);
}
