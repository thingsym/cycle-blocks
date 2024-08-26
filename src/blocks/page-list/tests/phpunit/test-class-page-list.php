<?php
/**
 * Class Test_Class_Page_List
 *
 * @package Page_List
 */

class Test_Class_Page_List extends WP_UnitTestCase {

	public $page_list;

	public function setUp(): void {
		parent::setUp();
		$this->page_list = new \Cycle_Blocks\Blocks\Page_List();
	}

	/**
	 * @test
	 * @group Page_List
	 */
	function constructor() {
		$this->assertSame( 10, has_action( 'init', [ $this->page_list, 'register_block_type' ] ) );
	}

	/**
	 * @test
	 * @group Page_List
	 */
	function register_block_type() {
		$block_name = 'cycle-blocks/page-list';

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
	 * @group Page_List
	 */
	function render_callback() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
