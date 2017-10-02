<?php
/*
Plugin Name: Page template in plugin
Description: Adds page template from Page_Template_in_plugin->templates array. Requires WP 4.7 or newer
Plugin URI: http://www.shramee.me/
Version: 1.0.0
Author: Shramee
Author URI: http://www.shramee.me/
Requires WP 4.7 and newer
*/

class Page_Template_in_plugin {

	/** @var Page_Template_in_plugin Instance */
	private static $instance;

	/**
	 * Returns an instance of this class.
	 * @return Page_Template_in_plugin Instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new Page_Template_in_plugin();
		}

		return self::$instance;
	}

	/** @var array Templates */
	protected $templates;

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		// Add a filter to the attributes metabox to inject template into the cache.
		add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );


		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path
		add_filter( 'template_include', array( $this, 'view_project_template' ) );


		// Add your templates to this array.
		$this->templates = array(
			'page-template.php' => 'Test page template',
		);

	}

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {

		foreach ( $this->templates as $file => $name ) {
			$posts_templates[ plugin_dir_path( __FILE__ ) . $file ] = $name;
		}

		return $posts_templates;
	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		// Get global post
		global $post;

		// Return template if post is empty
		if ( $post ) {

			$file = get_post_meta( $post->ID, '_wp_page_template', true );

			// Just to be safe, we check if the file exist first
			if ( file_exists( $file ) ) {
				$template = $file;
			} else {
				echo "Template file $file doesn't exist";
			}
		}

		// Return template
		return $template;

	}

}

add_action( 'plugins_loaded', array( 'Page_Template_in_plugin', 'get_instance' ) );
