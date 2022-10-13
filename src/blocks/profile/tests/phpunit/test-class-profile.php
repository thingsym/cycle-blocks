<?php
/**
 * Class Test_Class_Profile
 *
 * @package Profile
 */

class Test_Class_Profile extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->profile = new \Cycle_Blocks\Blocks\Profile();
	}

	/**
	 * @test
	 * @group Profile
	 */
	function constructor() {
		$this->assertSame( 10, has_action( 'init', [ $this->profile, 'register_block_type' ] ) );
	}

	/**
	 * @test
	 * @group Profile
	 */
	function register_block_type() {
		$block_name = 'cycle-blocks/profile';

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
	 * @group Profile
	 */
	function render_callback_admin() {
		$post_id = $this->factory->post->create( [ 'post_author' => 1 ] );
		$display_name = get_userdata( 1 )->display_name;

		$attributes = [];
		$content = '';
		$block = (object) [
			'context' => [
				'postId' => $post_id
			]
		];

		$render = $this->profile->render_callback( $attributes, $content, $block );

		$this->assertRegExp( '#' . $display_name . '#', $render );
	}

	/**
	 * @test
	 * @group Profile
	 */
	function render_callback_post_author() {
		$user_id = $this->factory->user->create();
		$display_name = get_userdata( $user_id )->display_name;
		$post_id = $this->factory->post->create( [ 'post_author' => $user_id ] );

		$attributes = [];
		$content = '';
		$block = (object) [
			'context' => [
				'postId' => $post_id
			]
		];

		$render = $this->profile->render_callback( $attributes, $content, $block );

		$this->assertRegExp( '#' . $display_name . '#', $render );
	}

	/**
	 * @test
	 * @group Profile
	 */
	function render_callback_userid() {
		$user_id = $this->factory->user->create();
		$display_name = get_userdata( $user_id )->display_name;
		$post_id = $this->factory->post->create();

		$attributes = [
			'userId' => $user_id
		];
		$content = '';
		$block = (object) [
			'context' => [
				'postId' => $post_id
			]
		];

		$render = $this->profile->render_callback( $attributes, $content, $block );

		$this->assertRegExp( '#' . $display_name . '#', $render );
	}

}
