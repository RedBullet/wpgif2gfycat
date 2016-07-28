<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://redbullet.co.uk
 * @since      1.0.0
 *
 * @package    Wpgif2gfycat
 * @subpackage Wpgif2gfycat/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpgif2gfycat
 * @subpackage Wpgif2gfycat/admin
 * @author     Gavyn McKenzie <gavyn@redbullet.co.uk>
 */
class Wpgif2gfycat_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Get an attachment ID given a URL.
	 *
	 * @param string $url
	 *
	 * @return int Attachment ID on success, 0 on failure
	 */
	function get_image_id_by_url( $url ) {

		$attachment_id = 0;
		$dir = wp_upload_dir();

		if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?

			$file = basename( $url );
			$query_args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'meta_query'  => array(
					array(
						'value'   => $file,
						'compare' => 'LIKE',
						'key'     => '_wp_attachment_metadata',
					),
				)
			);

			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post_id ) {

					$meta = wp_get_attachment_metadata( $post_id );
					$original_file       = basename( $meta['file'] );
					$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );

					if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
						$attachment_id = $post_id;
						break;
					}

				}
			}
		}

		return $attachment_id;
	}

	/**
	 * Try and get a gfycat for the gif instead
	 * @since    1.0.0
	 */
	public function get_gfycat($metadata, $id) {

		// Check type is gif
		if (isset($metadata) && $metadata['sizes']['thumbnail']['mime-type'] == 'image/gif') {
			// Upload to gfycat
			$url = wp_get_attachment_url( $id );
			$hash = md5($url.time());
			$code = substr($hash,0,10);
			$gfycat_url = 'https://upload.gfycat.com/transcodeRelease/'.$code.'?fetchUrl='.$url;

			$request = new WP_Http();
			$response = $request->get( $gfycat_url );

			// Stash the code as meta on the image for later
			update_post_meta( $id, 'gfycat_code', $code );
			$metadata['gfycat_code'] = $code;
		}

		return $metadata;

	}

	function filter_attachment_fields_to_edit( $form_fields, $post ) {
		$code = get_post_meta( $post->ID, 'gfycat_code', true );
		if (isset($code) && $code != '') {
			$html5video_replace = get_post_meta($post->ID, 'html5video_replace', true);

			$form_fields['html5video_replace'] = array(
			'label' => 'Replace with HTML5 video',
			'input' => 'html',
			'html' => '<label for="attachments-'.$post->ID.'-foo"> '.
			'<input type="checkbox" id="attachments-'.$post->ID.'-foo" name="attachments['.$post->ID.'][html5video_replace]" value="1"'.($html5video_replace ? ' checked="checked"' : '').' /></label>  ',
			'value' => $html5video_replace
			);
		}
		return $form_fields;
	}

	function image_attachment_fields_to_save($post, $attachment) {
		if( isset($attachment['html5video_replace']) ){
			update_post_meta($post['ID'], 'html5video_replace', $attachment['html5video_replace']);
		}
		return $post;
	}

}
