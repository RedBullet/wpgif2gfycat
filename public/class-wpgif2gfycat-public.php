<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://redbullet.co.uk
 * @since      1.0.0
 *
 * @package    Wpgif2gfycat
 * @subpackage Wpgif2gfycat/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpgif2gfycat
 * @subpackage Wpgif2gfycat/public
 * @author     Gavyn McKenzie <gavyn@redbullet.co.uk>
 */
class Wpgif2gfycat_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function generate_gfycat($id) {
		// Upload to gfycat
		$url = wp_get_attachment_url( $id );
		$hash = md5($url.time());
		$code = substr($hash,0,10);
		$gfycat_url = 'https://upload.gfycat.com/transcodeRelease/'.$code.'?fetchUrl='.$url;

		$request = new WP_Http();
		$response = $request->get( $gfycat_url );

		// Stash the code as meta on the image for later
		update_post_meta( $id, 'gfycat_code', $code );

		return $code;
	}

	/**
	 * Check if we have a gfycat finished
	 *
	 * @since    1.0.0
	 */
	public function check_gfycat($metadata, $id) {
		/**
		 * Check for gfycat status or complete
		 */

		if (is_array($metadata)) {
			if (get_post_meta( $id, 'html5video_replace', true )) {
				$code = get_post_meta( $id, 'gfycat_code', true );

				if (isset($code) && $code != '') {
					$metadata['gfycat_code'] = $code;

					$name = get_post_meta( $id, 'gfycat_name', true );

					if (isset($name) && $name != '') {
						$metadata['gfycat_name'] = $name;
					} else {
						$request = new WP_Http();
						$gfycat_url = 'http://upload.gfycat.com/status/'.$code;
						$response = $request->get( $gfycat_url );

						if (isset($response) && is_array($response) && $response['body']) {
							$response = json_decode($response['body']);

							if (isset($response->gfyname)) {
								update_post_meta( $id, 'gfycat_name', $response->gfyname );
								$metadata['gfycat_name'] = $response->gfyname;
							} else if (isset($response->error) || $response->task == "NotFoundo") {
								$metadata['gfycat_code'] = $this->generate_gfycat($id);
							}
						}
					}
				// Patch for previously uploaded images
				} else if (isset($metadata) && $metadata['sizes']['thumbnail']['mime-type'] == 'image/gif') {
					$metadata['gfycat_code'] = $this->generate_gfycat($id);
				}
			}
		}
		return $metadata;
	}

}
