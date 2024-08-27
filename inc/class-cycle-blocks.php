<?php
/**
 * Cycle_Blocks class
 *
 * @package Cycle_Blocks
 *
 * @since 1.0.0
 */

namespace Cycle_Blocks;

/**
 * Core class Cycle_Blocks
 *
 * @since 1.0.0
 */
class Cycle_Blocks {

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var array|null $plugin_data
	 */
	public $plugin_data = array();

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var array|null $asset_file
	 */
	public $asset_file = array();

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'load_plugin_data' ] );
		add_action( 'plugins_loaded', [ $this, 'load_asset_file' ] );

		add_action( 'plugins_loaded', [ $this, 'init' ] );
		add_action( 'plugins_loaded', [ $this, 'load_dynamic_blocks' ] );
	}

	public function init() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'load_textdomain' ] );

		add_action( 'init', [ $this, 'register_styles' ] );
		add_action( 'init', [ $this, 'register_block_editor_scripts' ] );
		add_action( 'init', [ $this, 'register_block_editor_styles' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'set_block_editor_translations' ] );

		add_filter( 'block_categories_all', [ $this, 'add_block_categories' ], 10, 2 );

		add_filter( 'plugin_row_meta', array( $this, 'plugin_metadata_links' ), 10, 2 );
	}

	/**
	 * Load plugin data
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_data() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin_data = get_plugin_data( CYCLE_BLOCKS );
	}

	/**
	 * Load asset file
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function load_asset_file() {
		$this->asset_file = include plugin_dir_path( CYCLE_BLOCKS ) . 'dist/js/blocks.asset.php';
	}

	/**
	 * Load textdomain
	 *
	 * @access public
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		return load_plugin_textdomain(
			'cycle-blocks',
			false,
			plugin_dir_path( CYCLE_BLOCKS ) . 'languages'
		);
	}

	/**
	 * Load block editor translations
	 *
	 * @access public
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 */
	public function set_block_editor_translations() {
		if ( function_exists( 'wp_set_script_translations' ) ) {
			return wp_set_script_translations(
				'cycle-blocks-editor-script',
				'cycle-blocks',
				plugin_dir_path( CYCLE_BLOCKS ) . 'languages'
			);
		}

		return false;
	}

	public function add_block_categories( $categories ) {
		return array_merge(
			$categories,
			[
				[
					'slug'  => 'cycle-blocks',
					'title' => __( 'Cycle Blocks', 'cycle-blocks' ),
				],
			]
		);
	}

	public function register_block_editor_scripts() {
		wp_register_script(
			'cycle-blocks-editor-script',
			plugins_url( 'dist/js/blocks.js', CYCLE_BLOCKS ),
			$this->asset_file['dependencies'],
			$this->asset_file['version'],
			false
		);
	}

	public function register_block_editor_styles() {
		wp_register_style(
			'cycle-blocks-editor-style',
			plugins_url( 'dist/css/block-editor-style.min.css', CYCLE_BLOCKS ),
			[],
			$this->plugin_data['Version'],
			'all'
		);
	}

	public function register_styles() {
		wp_register_style(
			'cycle-blocks-style',
			plugins_url( 'dist/css/blocks.min.css', CYCLE_BLOCKS ),
			[],
			$this->plugin_data['Version'],
			'all'
		);
	}

	public function load_dynamic_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		new \Cycle_Blocks\Blocks\Page_List();
		new \Cycle_Blocks\Blocks\Sitemap();
		new \Cycle_Blocks\Blocks\Profile();
	}

	/**
	 * Set links below a plugin on the Plugins page.
	 *
	 * Hooks to plugin_row_meta
	 *
	 * @see https://developer.wordpress.org/reference/hooks/plugin_row_meta/
	 *
	 * @access public
	 *
	 * @param array  $links  An array of the plugin's metadata.
	 * @param string $file   Path to the plugin file relative to the plugins directory.
	 *
	 * @return array $links
	 *
	 * @since 1.0.0
	 */
	public function plugin_metadata_links( $links, $file ) {
		if ( $file === plugin_basename( CYCLE_BLOCKS ) ) {
			$links[] = '<a href="https://github.com/sponsors/thingsym">' . __( 'Become a sponsor', 'cycle-blocks' ) . '</a>';
		}

		return $links;
	}

}
