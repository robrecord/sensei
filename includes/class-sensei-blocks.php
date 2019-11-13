<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handles Sensei's blocks.
 */
final class Sensei_Blocks {
	/**
	 * Stores singleton of self.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Fetch singleton of class.
	 *
	 * @return Sensei_Blocks
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initializes blocks.
	 */
	public function init() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		add_action( 'init', [ $this, 'register_blocks' ], 11 );
	}

	/**
	 * Registers all the blocks.
	 */
	public function register_blocks() {
		$this->register_course_shortcode();
		$this->register_messages();
	}

	/**
	 * Registers the course shortcode block.
	 */
	private function register_course_shortcode() {
		wp_register_style( 'sensei-global', Sensei()->plugin_url . 'assets/css/global.css', '', Sensei()->version, 'screen' );
		wp_register_style( Sensei()->token . '-frontend', Sensei()->plugin_url . 'assets/css/frontend/sensei.css', [ 'sensei-global' ], Sensei()->version, 'screen' );

		$asset_info = include Sensei()->plugin_path . 'assets/block-editor/build/course-shortcode-block.asset.php';
		wp_register_script(
			'sensei-course-shortcode-block',
			Sensei()->plugin_url . 'assets/block-editor/build/course-shortcode-block.js',
			$asset_info['dependencies'],
			$asset_info['version'],
			true
		);

		register_block_type(
			'sensei-lms/course-shortcode-block',
			[
				'editor_script'   => 'sensei-course-shortcode-block',
				'editor_style'    => Sensei()->token . '-frontend',
				'render_callback' => function( $attributes, $content ) {
					return $this->do_shortcode( 'sensei_courses', $attributes );
				},
			]
		);
	}

	/**
	 * Registers the messages block.
	 */
	private function register_messages() {
		$asset_info_editor = include Sensei()->plugin_path . 'assets/block-editor/build/messages-block.asset.php';
		wp_register_script(
			'sensei-messages-block',
			Sensei()->plugin_url . 'assets/block-editor/build/messages-block.js',
			$asset_info_editor['dependencies'],
			$asset_info_editor['version'],
			true
		);

		$asset_info_frontend = include Sensei()->plugin_path . 'assets/block-editor/build/messages-block-frontend.asset.php';
		wp_register_script(
			'sensei-messages-block-frontend',
			Sensei()->plugin_url . 'assets/block-editor/build/messages-block-frontend.js',
			$asset_info_frontend['dependencies'],
			$asset_info_frontend['version'],
			true
		);

		register_block_type(
			'sensei-lms/messages-block',
			[
				'editor_script' => 'sensei-messages-block',
				'script'        => 'sensei-messages-block-frontend',
			]
		);
	}

	/**
	 * Render the shortcode.
	 *
	 * @param string $shortcode  Name of shortcode.
	 * @param array  $attributes Attributes passed to the block
	 * @return string
	 */
	public function do_shortcode( $shortcode, $attributes ) {
		$shortcode_str = '[' . $shortcode;
		foreach ( $attributes as $key => $value ) {
			$shortcode_str .= ' ' . esc_attr( $key ) .'="' . esc_attr( $value ). '"';
		}
		$shortcode_str .= ']';
		return do_shortcode( $shortcode_str );
	}
}
