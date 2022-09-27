<?php
/**
 * Class Test_Cycle_Blocks
 *
 * @package Cycle_Blocks
 */

/**
 * Basic test case.
 */
class Test_Cycle_Blocks extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->cycle_blocks = new \Cycle_Blocks\Cycle_Blocks();
	}

	/**
	 * @test
	 * @group basic
	 */
	function basic() {
		$this->assertRegExp( '#/cycle-blocks/cycle-blocks.php$#', CYCLE_BLOCKS );

		$this->assertTrue( class_exists( '\Cycle_Blocks\Cycle_Blocks' ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function public_variable() {
		$this->assertIsArray( $this->cycle_blocks->plugin_data );
		$this->assertEmpty( $this->cycle_blocks->plugin_data );

		$this->assertIsArray( $this->cycle_blocks->asset_file );
		$this->assertEmpty( $this->cycle_blocks->asset_file );
	}

	/**
	 * @test
	 * @group basic
	 */
	function constructor() {
		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->cycle_blocks, 'load_plugin_data' ] ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->cycle_blocks, 'load_asset_file' ] ) );

		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->cycle_blocks, 'init' ] ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->cycle_blocks, 'load_dynamic_blocks' ] ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function init() {
		$this->cycle_blocks->init();

		$this->assertSame( 10, has_action( 'init', [ $this->cycle_blocks, 'load_textdomain' ] ) );

		$this->assertSame( 10, has_filter( 'init', [ $this->cycle_blocks, 'register_styles' ] ) );
		$this->assertSame( 10, has_filter( 'init', [ $this->cycle_blocks, 'register_block_editor_scripts' ] ) );
		$this->assertSame( 10, has_filter( 'init', [ $this->cycle_blocks, 'register_block_editor_styles' ] ) );

		$this->assertSame( 10, has_filter( 'enqueue_block_editor_assets', [ $this->cycle_blocks, 'set_block_editor_translations' ] ) );
		$this->assertSame( 10, has_filter( 'block_categories_all', [ $this->cycle_blocks, 'add_block_categories' ] ) );

		$this->assertSame( 10, has_filter( 'plugin_row_meta', array( $this->cycle_blocks, 'plugin_metadata_links' ) ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_plugin_data() {
		$this->cycle_blocks->load_plugin_data();
		$result = $this->cycle_blocks->plugin_data;

		$this->assertTrue( is_array( $result ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_asset_file() {
		$this->cycle_blocks->load_asset_file();
		$result = $this->cycle_blocks->asset_file;

		$this->assertTrue( is_array( $result ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_textdomain() {
		$loaded = $this->cycle_blocks->load_textdomain();
		$this->assertFalse( $loaded );

		unload_textdomain( 'cycle-blocks' );

		add_filter( 'locale', [ $this, '_change_locale' ] );
		add_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ], 10, 2 );

		$loaded = $this->cycle_blocks->load_textdomain();
		$this->assertTrue( $loaded );

		remove_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ] );
		remove_filter( 'locale', [ $this, '_change_locale' ] );

		unload_textdomain( 'cycle-blocks' );
	}

	/**
	 * hook for load_textdomain
	 */
	function _change_locale( $locale ) {
		return 'ja';
	}

	function _change_textdomain_mofile( $mofile, $domain ) {
		if ( $domain === 'cycle-blocks' ) {
			$locale = determine_locale();
			$mofile = plugin_dir_path( CYCLE_BLOCKS ) . 'languages/cycle-blocks-' . $locale . '.mo';

			$this->assertSame( $locale, get_locale() );
			$this->assertFileExists( $mofile );
		}

		return $mofile;
	}

	/**
	 * @test
	 * @group basic
	 */
	public function set_block_editor_translations() {
		$result = $this->cycle_blocks->set_block_editor_translations();
		$this->assertTrue( $result );

		$this->assertArrayHasKey( 'cycle-blocks-editor-script', wp_scripts()->registered );
		$this->assertSame( wp_scripts()->registered[ 'cycle-blocks-editor-script' ]->textdomain, 'cycle-blocks' );
		$this->assertSame( wp_scripts()->registered[ 'cycle-blocks-editor-script' ]->translations_path, plugin_dir_path( CYCLE_BLOCKS ) . 'languages' );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function add_block_categories() {
		$expect[] = [
			'slug'  => 'cycle-blocks',
			'title' => __( 'Cycle Blocks', 'cycle-blocks' ),
		];
		$actual = $this->cycle_blocks->add_block_categories( [] );

		$this->assertIsArray( $actual );
		$this->assertSame( $expect, $actual );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function register_block_editor_scripts() {
		$this->cycle_blocks->load_asset_file();
		$this->cycle_blocks->register_block_editor_scripts();
		$this->assertArrayHasKey( 'cycle-blocks-editor-script', wp_scripts()->registered );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function register_block_editor_styles() {
		$this->cycle_blocks->load_plugin_data();
		$this->cycle_blocks->register_block_editor_styles();
		$this->assertArrayHasKey( 'cycle-blocks-editor-style', wp_styles()->registered );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function register_styles() {
		$this->cycle_blocks->load_plugin_data();
		$this->cycle_blocks->register_styles();
		$this->assertArrayHasKey( 'cycle-blocks-style', wp_styles()->registered );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_dynamic_blocks() {
		$this->cycle_blocks->load_dynamic_blocks();

		$this->assertTrue( class_exists( '\Cycle_Blocks\Blocks\Page_List' ) );
		$this->assertTrue( class_exists( '\Cycle_Blocks\Blocks\Sitemap' ) );
		$this->assertTrue( class_exists( '\Cycle_Blocks\Blocks\Profile' ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function plugin_metadata_links() {
		$links = $this->cycle_blocks->plugin_metadata_links( array(), plugin_basename( CYCLE_BLOCKS ) );
		$this->assertContains( '<a href="https://github.com/sponsors/thingsym">Become a sponsor</a>', $links );
	}

	/**
	 * @test
	 * @group basic
	 */
	function uninstall() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
