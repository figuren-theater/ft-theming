<?php
/**
 * Figuren_Theater Theming Post_Type_Templates.
 *
 * @package figuren-theater/ft-theming
 */

declare(strict_types=1);
namespace Figuren_Theater\Theming\Post_Type_Templates;

use WP_Post;
use WP_Theme;
use function add_filter;
use function apply_filters;
use function get_post_meta;
use function get_stylesheet;
use function get_theme_root;
use function is_search;
use function locate_template;
use function wp_cache_add;
use function wp_cache_delete;
use function wp_get_theme;

/**
 * Heavily based on the "'Good To Be Bad' Page Template Plugin" and some q&a threads
 * 
 * @see  https://github.com/wpexplorer/page-templater
 * @see  https://wordpress.stackexchange.com/questions/3396/create-custom-page-templates-with-plugins
 * @see  https://wordpress.stackexchange.com/questions/17385/custom-post-type-templates-from-plugin-folder
 */
class Loader {

	/**
	 * The human readable name of a template, keyed by its file name.
	 * 
	 * @var array<string, string>
	 */
	protected $templates = [];

	/**
	 * The base folder to look at for templates.
	 * 
	 * @var string
	 */
	protected $folder_abspath = '';

	/**
	 * Post Type to load template for.
	 * 
	 * @var string
	 */
	protected $post_type = '';


	/**
	 * Initializes the plugin by setting filters and administration functions.
	 *
	 * @param  array<string, string> $templates       The array of templates that this plugin tracks.
	 * @param  string                $folder_abspath  The base folder to look at for templates.
	 * @param  string                $post_type       Post Type to load template for.
	 */
	public function __construct( array $templates, string $folder_abspath, string $post_type ) {

		// Add your templates to this array.
		$this->templates = $templates;
		
		// Define the base folder to look at.
		$this->folder_abspath = $folder_abspath;
		
		// Set the post type to work with.
		$this->post_type = $post_type;

		// Load filters.
		$this->setup();
	}


	/**
	 * Load filters.
	 */
	public function setup(): void {

		// FRONTEND & ADMIN !

		// Add a filter to the template metabox.
		add_filter( 'theme_templates', [ $this, 'theme_templates' ], 10, 4 );

		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path.
		add_filter( 'template_include', [ $this, 'template_include' ] );

		// ADMIN ONLY !

		// Add a filter to the save post to inject out template into the page cache.
		add_filter( 'wp_insert_post_data', [ $this, 'wp_insert_post_data' ], 10, 2 );
	}


	/**
	 * Adds our template to the template dropdown for v4.7+
	 * 
	 * Filters list of templates for a theme.
	 * The dynamic portion of the hook name, `$post_type`, refers to the post type.
	 *
	 * Possible hook names include:
	 *
	 *  - `theme_post_templates`
	 *  - `theme_page_templates`
	 *  - `theme_attachment_templates`
	 *
	 * @see     https://developer.wordpress.org/reference/hooks/theme_templates/
	 *
	 * @param   array<string, string> $templates      Array of template header names keyed by the template file name.
	 * @param   WP_Theme              $theme          The theme object.
	 * @param   WP_Post|null          $post           The post being edited, provided for context, or null.
	 * @param   string                $post_type      Post type to get the templates for.
	 *
	 * @return  array<string, string>                    Array of template header names keyed by the template file name.
	 */
	public function theme_templates( array $templates, WP_Theme $theme, WP_Post|null $post, string $post_type ): array {

		if ( $this->post_type !== $post_type ) {
			return $templates;
		}

		// Glue together defaults with ours.
		return array_merge( $templates, $this->templates );
	}



	/**
	 * Checks if the template is assigned to the page
	 *
	 * This filter hook is executed immediately 
	 * before WordPress includes the predetermined template file. 
	 * 
	 * This can be used to override WordPress’s default template behavior.
	 *
	 * @see     https://developer.wordpress.org/reference/hooks/template_include/
	 *
	 * @param   string $template The path of the template to include.
	 * 
	 * @return  string                 The path of the template to include.
	 */
	public function template_include( string $template ): string {

		global $post;

		// Return the search template if we're searching 
		// (instead of the template for the first result).
		if ( is_search() ) {
			return $template;
		}

		// Return template if post is empty.
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return $template;
		}

		// Return template if wrong post_type.
		if ( $this->post_type !== $post->post_type ) {
			return $template;
		}

		// If a template is found in theme or child theme directories
		// return this instead.
		$parts = explode( '/', $template );
		$file  = array_pop( $parts );
		if ( $template === locate_template( [ $file ] ) ) {
			return $template;
		}


		// Get saved template from DB.
		$_current_template = get_post_meta( $post->ID, '_wp_page_template', true );

		// Return default template if we don't have a custom one defined.
		if ( ! isset( $this->templates[ $_current_template ] ) ) {
			return $template;
		}

		/**
		 * Allows filtering of file path
		 * 
		 * @var string The base folder to look at for templates.
		 */
		$folder_abspath = apply_filters( 
			__NAMESPACE__ . '\\template_include\folder_abspath', 
			$this->folder_abspath, 
			$template, 
			$this, 
			$_current_template
		);

		// Glue together.
		$file = $folder_abspath . $_current_template;

		// Just to be safe, we check if the file exist first.
		if ( file_exists( $file ) ) {
			return $file;
		} 

		// Return template.
		return $template;
	}


	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 * 
	 * Filters slashed post data just before it is inserted into the database.
	 *
	 * @see     https://developer.wordpress.org/reference/hooks/wp_insert_post_data/
	 *
	 * @param   array<string, string|int|bool> $data                An array of slashed, sanitized, and processed post data.
	 * @param   array<string, string|int|bool> $postarr             An array of sanitized (and slashed) but otherwise unmodified post data.
	 *
	 * @return  array<string, string|int|bool>                      An array of slashed, sanitized, and processed post data.
	 */
	public function wp_insert_post_data( array $data, array $postarr ): array {

		// Return data if wrong post_type.
		if ( $this->post_type !== $postarr['post_type'] ) {
			return $data;
		}

		// Create the key used for the themes cache.
		$hash      = md5(
			join(
				'',
				[
					get_theme_root() ,
					'/',
					get_stylesheet(),
				]
			)
		);
		$cache_key = join(
			'',
			[
				$postarr['post_type'],
				'_templates-',
				$hash,
			]
		);

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array.
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one.
		wp_cache_delete( $cache_key, 'themes' );

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress 
		// to pick it up for listing available templates.
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $data;
	}
}
