<?php
/**
 * Sitemap Block
 *
 * @package Sitemap
 *
 * @since 1.0.0
 */

namespace Cycle_Blocks\Blocks;

/**
 * Core class Sitemap
 *
 * @since 1.0.0
 */
class Sitemap {
	/**
	 * Ensure that the ID attribute only appears in the markup once
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access public
	 * @var int
	 */
	public static $block_id = 0;

	public function __construct() {
		add_action( 'init', [ $this, 'register_block_type' ] );
	}

	public function register_block_type() {
		register_block_type(
			plugin_dir_path( CYCLE_BLOCKS ) . '/dist/blocks/sitemap',
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	public function render_callback( $attributes ) {
		$displayPage     = ! empty( $attributes['displayPage'] );
		$displayPost     = ! empty( $attributes['displayPost'] );
		$displayCategory = ! empty( $attributes['displayCategory'] );
		$displayAuthor   = ! empty( $attributes['displayAuthor'] );

		$sitemap_markup = '';
		$class          = "cycle-blocks-sitemap cycle-blocks-sitemap-";
		$wrapper_markup = '<ul class="%1$s">%2$s</ul>';

		if ( $displayPage ) {
			$pages = wp_list_pages( 'title_li=&echo=0' );

			if ( $pages ) {
				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap-title">' . __( 'Page', 'cycle-blocks' ) . '</h3>';
				$sitemap_markup .= sprintf(
					$wrapper_markup,
					esc_attr( $class . 'page' ),
					$pages
				);
			}
		}

		if ( $displayPost ) {
			$posts = get_posts( 'posts_per_page=-1' );
			global $post;

			if ( $posts ) {
				$html = '';
				foreach ( $posts as $post ) {
					setup_postdata( $post );

					$html .= '<li>';
					$html .= sprintf( __( '<time class="post-date" datetime="%1$s">%2$s</time> ' ), esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
					$html .= '<a href="' . get_permalink() . '">' . $post->post_title . '</a>';
					$html .= '</li>';
				}

				wp_reset_postdata();

				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap-title">' . __( 'Blog', 'cycle-blocks' ) . '</h3>';
				$sitemap_markup .= sprintf(
					$wrapper_markup,
					esc_attr( $class . 'blog' ),
					$html
				);
			}
		}

		if ( $displayCategory ) {
			$categories = wp_list_categories( 'title_li=&echo=0&show_count=1' );

			if ( $categories ) {
				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap-title">' . __( 'Category', 'cycle-blocks' ) . '</h3>';
				$sitemap_markup .= sprintf(
					$wrapper_markup,
					esc_attr( $class . 'category' ),
					$categories
				);
			}
		}

		if ( $displayAuthor ) {
			$authors = wp_list_authors( 'title_li=&echo=0&exclude_admin=0&optioncount=1' );

			if ( $authors ) {
				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap-title">' . __( 'Author', 'cycle-blocks' ) . '</h3>';
				$sitemap_markup .= sprintf(
					$wrapper_markup,
					esc_attr( $class . 'author' ),
					$authors
				);
			}
		}

		return $sitemap_markup;
	}
}
