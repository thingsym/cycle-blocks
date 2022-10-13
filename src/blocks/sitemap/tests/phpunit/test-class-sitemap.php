<?php
/**
 * Class Test_Class_Sitemap
 *
 * @package Sitemap
 */

class Test_Class_Sitemap extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->sitemap = new \Cycle_Blocks\Blocks\Sitemap();
	}

	/**
	 * @test
	 * @group Sitemap
	 */
	function constructor() {
		$this->assertSame( 10, has_action( 'init', [ $this->sitemap, 'register_block_type' ] ) );
	}

	/**
	 * @test
	 * @group Sitemap
	 */
	function register_block_type() {
		$block_name = 'cycle-blocks/sitemap';

		$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
		$this->assertArrayHasKey( $block_name, $block_types );

		$block_type = $block_types[ $block_name ];
		$this->assertTrue( $block_type->is_dynamic() );

		$this->assertContains( $block_name, get_dynamic_block_names() );

		$this->assertSame( 'cycle-blocks-editor-script', $block_type->editor_script );
		$this->assertSame( 'cycle-blocks-editor-style', $block_type->editor_style );
		$this->assertSame( 'cycle-blocks-style', $block_type->style );
	}

	/**
	 * @test
	 * @group Sitemap
	 */
	function render_callback() {
		$posts = $this->factory->post->create_many( 5, [ 'post_author' => 1 ] );
		$pages = $this->factory->post->create_many( 5, [
			'post_author' => 1,
			'post_type' => 'page'
		] );

		$attributes = [
			'displayPage'     => true,
			'displayPost'     => true,
			'displayCategory' => true,
			'displayAuthor'   => true,
		];

		$render = $this->sitemap->render_callback( $attributes );

		$this->assertRegExp( '#cycle-blocks-sitemap-blog#', $render );
		$this->assertRegExp( '#cycle-blocks-sitemap-page#', $render );
		$this->assertRegExp( '#cycle-blocks-sitemap-category#', $render );
		$this->assertRegExp( '#cycle-blocks-sitemap-author#', $render );
	}

	/**
	 * @test
	 * @group Sitemap
	 */
	function render_callback_case_01() {
		$posts = $this->factory->post->create_many( 5, [ 'post_author' => 1 ] );
		$pages = $this->factory->post->create_many( 5, [
			'post_author' => 1,
			'post_type' => 'page'
		] );

		$attributes = [
			'displayPage'     => false,
			'displayPost'     => false,
			'displayCategory' => true,
			'displayAuthor'   => true,
		];

		$render = $this->sitemap->render_callback( $attributes );

		$this->assertNotRegExp( '#cycle-blocks-sitemap-blog#', $render );
		$this->assertNotRegExp( '#cycle-blocks-sitemap-page#', $render );
		$this->assertRegExp( '#cycle-blocks-sitemap-category#', $render );
		$this->assertRegExp( '#cycle-blocks-sitemap-author#', $render );
	}

	/**
	 * @test
	 * @group Sitemap
	 */
	function render_callback_case_02() {
		$posts = $this->factory->post->create_many( 5, [ 'post_author' => 1 ] );
		$pages = $this->factory->post->create_many( 5, [
			'post_author' => 1,
			'post_type' => 'page'
		] );

		$attributes = [
			'displayPage'     => true,
			'displayPost'     => true,
			'displayCategory' => false,
			'displayAuthor'   => false,
		];

		$render = $this->sitemap->render_callback( $attributes );

		$this->assertRegExp( '#cycle-blocks-sitemap-blog#', $render );
		$this->assertRegExp( '#cycle-blocks-sitemap-page#', $render );
		$this->assertNotRegExp( '#cycle-blocks-sitemap-category#', $render );
		$this->assertNotRegExp( '#cycle-blocks-sitemap-author#', $render );
	}

}
