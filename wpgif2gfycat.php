<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://redbullet.co.uk
 * @since             1.0.0
 * @package           Wpgif2gfycat
 *
 * @wordpress-plugin
 * Plugin Name:       WP Gif 2 Gfycat
 * Plugin URI:        http://github.com/redbullet/wpgif2gfycat
 * Description:       Convert gifs to gfycat video on upload
 * Version:           1.0.0
 * Author:            Gavyn McKenzie
 * Author URI:        http://redbullet.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpgif2gfycat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpgif2gfycat-activator.php
 */
function activate_wpgif2gfycat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpgif2gfycat-activator.php';
	Wpgif2gfycat_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpgif2gfycat-deactivator.php
 */
function deactivate_wpgif2gfycat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpgif2gfycat-deactivator.php';
	Wpgif2gfycat_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpgif2gfycat' );
register_deactivation_hook( __FILE__, 'deactivate_wpgif2gfycat' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpgif2gfycat.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpgif2gfycat() {

	$plugin = new Wpgif2gfycat();
	$plugin->run();

}
run_wpgif2gfycat();
