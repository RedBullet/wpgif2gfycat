<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://redbullet.co.uk
 * @since      1.0.0
 *
 * @package    Wpgif2gfycat
 * @subpackage Wpgif2gfycat/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wpgif2gfycat
 * @subpackage Wpgif2gfycat/includes
 * @author     Gavyn McKenzie <gavyn@redbullet.co.uk>
 */
class Wpgif2gfycat_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wpgif2gfycat',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
