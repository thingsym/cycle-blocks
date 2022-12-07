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
		$display_page     = ! empty( $attributes['displayPage'] );
		$display_post     = ! empty( $attributes['displayPost'] );
		$display_category = ! empty( $attributes['displayCategory'] );
		$display_author   = ! empty( $attributes['displayAuthor'] );

		$sitemap_markup = '';
		$class          = 'cycle-blocks-sitemap cycle-blocks-sitemap__';
		$wrapper_markup = '<ul class="%1$s">%2$s</ul>';

		if ( $display_page ) {
			$pages = wp_list_pages( 'title_li=&echo=0' );

			if ( $pages ) {
				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap__title">' . __( 'Page', 'cycle-blocks' ) . '</h3>';
				$sitemap_markup .= sprintf(
					$wrapper_markup,
					esc_attr( $class . 'page' ),
					$pages
				);
			}
		}

		if ( $display_post ) {
			$args = [
				'post_type'           => 'post',
				'posts_per_page'      => -1,
				'post_status'         => 'publish',
				'order'               => 'DESC',
				'orderby'             => 'date',
				'suppress_filters'    => false,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			];

			$posts = new \WP_Query( $args );

			if ( $posts->have_posts() ) {
				$html = '';

				while ( $posts->have_posts() ) {
					$posts->the_post();

					$html .= '<li>';
					$html .= sprintf( __( '<time class="post-date" datetime="%1$s">%2$s</time> ', 'cycle-blocks' ), esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
					$html .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
					$html .= '</li>';
				}

				wp_reset_postdata();

				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap__title">' . __( 'Blog', 'cycle-blocks' ) . '</h3>';
				$sitemap_markup .= sprintf(
					$wrapper_markup,
					esc_attr( $class . 'blog' ),
					$html
				);
			}
		}

		if ( $display_category ) {
			$categories = wp_list_categories( 'title_li=&echo=0&show_count=1' );

			if ( $categories ) {
				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap__title">' . __( 'Category', 'cycle-blocks' ) . '</h3>';
				$sitemap_markup .= sprintf(
					$wrapper_markup,
					esc_attr( $class . 'category' ),
					$categories
				);
			}
		}

		if ( $display_author ) {
			$authors = wp_list_authors( 'title_li=&echo=0&exclude_admin=0&optioncount=1' );

			if ( $authors ) {
				$sitemap_markup .= '<h3 class="cycle-blocks-sitemap__title">' . __( 'Author', 'cycle-blocks' ) . '</h3>';
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
