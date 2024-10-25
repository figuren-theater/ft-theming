<?php
/**
 * Plugin Name:     figuren.theater | Theming
 * Plugin URI:      https://github.com/figuren-theater/ft-theming
 * Description:     This package helps you & your brand with a consistent look across the figuren.theater Multisite Network and beyond.
 * Author:          figuren.theater
 * Author URI:      https://figuren.theater
 * Text Domain:     figurentheater
 * Domain Path:     /languages
 * Version:         1.2.1
 *
 * @package         figuren-theater/theming
 */

namespace Figuren_Theater\Theming;

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
