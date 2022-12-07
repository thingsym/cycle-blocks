<?php
/**
 * Page List Block
 *
 * @package Page_List
 *
 * @since 1.0.0
 */

namespace Cycle_Blocks\Blocks;

/**
 * Core class Page_List
 *
 * @since 1.0.0
 */
class Page_List {
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
			plugin_dir_path( CYCLE_BLOCKS ) . '/dist/blocks/page-list',
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	public function render_callback( $attributes ) {
		$parsed_args = $this->parse_args( $attributes );
		$pages       = get_pages( $parsed_args );

		if ( ! $pages ) {
			return;
		}

		$post_content = '';

		if ( $attributes['layout'] === 'list' ) {
			$post_content = $this->get_list_layout( $pages, $parsed_args, $attributes );
		}
		elseif ( $attributes['layout'] === 'card' ) {
			$post_content = $this->get_card_layout( $pages, $parsed_args, $attributes );
		}
		elseif ( $attributes['layout'] === 'topics' ) {
			$post_content = $this->get_topics_layout( $pages, $parsed_args, $attributes );
		}
		elseif ( $attributes['layout'] === 'article' ) {
			$post_content = $this->get_article_layout( $pages, $parsed_args, $attributes );
		}

		$wrapper_classes[] = 'cycle-blocks-page-list';
		$wrapper_classes[] = $parsed_args['parent'] ? 'parent-post-' . $parsed_args['parent'] : null;
		$wrapper_classes[] = $attributes['layout'] === 'card' && $attributes['columns'] ? 'cycle-blocks-page-list__layout-card--columns-' . $attributes['columns'] : null;
		$wrapper_classes[] = $attributes['layout'] ? 'cycle-blocks-page-list__layout-' . $attributes['layout'] : null;

		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $wrapper_classes ) ) );

		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			$post_content
		);
	}

	public function parse_args( $attributes ) {
		$defaults = array(
			'depth'        => 1,
			'child_of'     => 0,
			'sort_order'   => 'ASC',
			'sort_column'  => 'post_title',
			'hierarchical' => 1,
			'exclude'      => [],
			'include'      => [],
			'meta_key'     => '', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'   => '', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'authors'      => '',
			'parent'       => -1,
			'exclude_tree' => [],
			'number'       => '',
			'offset'       => 0,
			'post_type'    => 'page',
			'post_status'  => 'publish',
		);

		$post      = null;
		$parent    = -1;
		$post_type = 'page';

		if ( ! empty( $attributes['postId'] ) ) {
			$post      = get_post( $attributes['postId'] );
			$parent    = $attributes['postId'];
			$post_type = get_post_type( $attributes['postId'] );
		}
		else {
			$parent    = get_the_ID();
			$post_type = get_post_type( get_the_ID() );
		}

		$args['parent']      = $parent;
		$args['child_of']    = $parent;
		$args['sort_order']  = $attributes['sortOrder'] ? $attributes['sortOrder'] : 'ASC';
		$args['sort_column'] = $attributes['sortColumn'] ? $attributes['sortColumn'] : 'menu_order';
		$args['post_type']   = $post_type;

		if ( $attributes['layout'] === 'list' ) {
			$args['depth']  = $attributes['depth'] ? $attributes['depth'] : 1;
			$args['parent'] = -1;

			if ( $attributes['displayAncestor'] ) {
				$ancestor = $this->get_post_id_ancestor( $post );
				if ( $ancestor ) {
					$parent = $ancestor;
				}
			}

			$args['child_of'] = $parent;
		}

		$parsed_args = wp_parse_args( $args, $defaults );

		return $parsed_args;
	}

	public function get_list_layout( $pages, $parsed_args, $attributes ) {
		if ( $attributes['displayAncestor'] ) {
			if ( ! empty( $attributes['postId'] ) ) {
				$parent_post = get_post( $attributes['postId'] );
				// the post_parent of 0 is root
				if ( ! $parent_post->post_parent ) {
					$pages[] = $parent_post;
				}
			}
			else {
				if ( empty( $GLOBALS['post']->post_parent ) ) {
					$pages[] = $GLOBALS['post'];
				}
			}
		}

		global $wp_query;
		$current_page = 0;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page ) {
			$current_page = get_queried_object_id();
		}
		elseif ( is_singular() ) {
			$queried_object = get_queried_object();
			if ( is_post_type_hierarchical( $queried_object->post_type ) ) {
				$current_page = $queried_object->ID;
			}
		}

		$post_content = walk_page_tree( $pages, $parsed_args['depth'], $current_page, $parsed_args );

		$post_items_markup = '<ul>' . $post_content . '</ul>';

		return $post_items_markup;
	}

	public function get_card_layout( $pages, $parsed_args, $attributes ) {
		$post_items_markup = '';

		foreach ( $pages as $post ) {
			$post_id        = $post->ID;
			$post_title     = get_the_title( $post );
			$post_permalink = get_permalink( $post );
			$post_excerpt   = get_the_excerpt( $post );

			$featured_image_markup = '';

			if ( $attributes['displayFeaturedImage'] ) {
				$featured_image = '';
				$image_style    = '';
				$image_size     = $attributes[ 'columns' ] > 2 ? 'medium' : 'medium_large';

				if ( has_post_thumbnail( $post ) ) {
					$featured_image = get_the_post_thumbnail(
						$post,
						$image_size,
						[
							'style' => esc_attr( $image_style ),
						]
					);
				}
				elseif ( isset( $attributes['featuredImageId'] ) ) {
					$image_classnames[] = 'attachment-' . $image_size;
					$image_classnames[] = 'wp-post-image';

					$featured_image = wp_get_attachment_image(
						$attributes['featuredImageId'],
						$image_size,
						false,
						[
							'style' => esc_attr( $image_style ),
							'class' => esc_attr( implode( ' ', $image_classnames ) ),
						]
					);
				}

				if ( $featured_image ) {
					$featured_image = sprintf(
						'<a href="%1$s" aria-label="%2$s">%3$s</a>',
						esc_url( $post_permalink ),
						esc_attr( $post_title ),
						$featured_image
					);

					$figure_classnames[] = 'cycle-blocks-page-list__featured-image';

					$featured_image_markup = sprintf(
						'<figure class="%1$s">%2$s</figure>',
						esc_attr( implode( ' ', $figure_classnames ) ),
						$featured_image
					);
				}
			}

			$post_items_markup .= '<article id="post-' . $post_id . '" class="' . esc_attr( implode( ' ', get_post_class( '', $post_id ) ) ) . '">';
			$post_items_markup .= $featured_image_markup;
			$post_items_markup .= '<div class="cycle-blocks-page-list__article-inner">';
			$post_items_markup .= '<h2 class="cycle-blocks-page-list__entry-title"><a href="' . $post_permalink . '">' . $post_title . '</a></h2>';
			$post_items_markup .= '<div class="cycle-blocks-page-list__entry-content">';
			$post_items_markup .= $post_excerpt;
			$post_items_markup .= '</div>';
			$post_items_markup .= '</div>';
			$post_items_markup .= '</article>';
		}

		return $post_items_markup;
	}

	public function get_topics_layout( $pages, $parsed_args, $attributes ) {
		$post_items_markup = '';

		foreach ( $pages as $post ) {
			$post_id        = $post->ID;
			$post_title     = get_the_title( $post );
			$post_permalink = get_permalink( $post );
			$post_excerpt   = get_the_excerpt( $post );

			$featured_image_markup = '';

			if ( $attributes['displayFeaturedImage'] ) {
				$featured_image = '';
				$image_style    = '';
				$image_size     = 'medium_large';

				if ( has_post_thumbnail( $post ) ) {
					$featured_image = get_the_post_thumbnail(
						$post,
						$image_size,
						[
							'style' => esc_attr( $image_style ),
						]
					);
				}
				elseif ( isset( $attributes['featuredImageId'] ) ) {
					$image_classnames[] = 'attachment-' . $image_size;
					$image_classnames[] = 'wp-post-image';

					$featured_image = wp_get_attachment_image(
						$attributes['featuredImageId'],
						$image_size,
						false,
						[
							'style' => esc_attr( $image_style ),
							'class' => esc_attr( implode( ' ', $image_classnames ) ),
						]
					);
				}

				if ( $featured_image ) {
					$featured_image = sprintf(
						'<a href="%1$s" aria-label="%2$s">%3$s</a>',
						esc_url( $post_permalink ),
						esc_attr( $post_title ),
						$featured_image
					);

					$figure_classnames[] = 'cycle-blocks-page-list__featured-image';

					$featured_image_markup = sprintf(
						'<figure class="%1$s">%2$s</figure>',
						esc_attr( implode( ' ', $figure_classnames ) ),
						$featured_image
					);
				}
			}

			$post_items_markup .= '<article id="post-' . $post_id . '" class="' . esc_attr( implode( ' ', get_post_class( '', $post_id ) ) ) . '">';
			$post_items_markup .= $featured_image_markup;
			$post_items_markup .= '<div class="cycle-blocks-page-list__article-inner">';
			$post_items_markup .= '<h2 class="cycle-blocks-page-list__entry-title"><a href="' . $post_permalink . '">' . $post_title . '</a></h2>';
			$post_items_markup .= '<div class="cycle-blocks-page-list__entry-content">';
			$post_items_markup .= $post_excerpt;
			$post_items_markup .= '</div>';
			$post_items_markup .= '</div>';
			$post_items_markup .= '</article>';
		}

		return $post_items_markup;
	}

	public function get_article_layout( $pages, $parsed_args, $attributes ) {
		$post_items_markup = '';

		foreach ( $pages as $post ) {
			$post_id        = $post->ID;
			$post_title     = get_the_title( $post );
			$post_permalink = get_permalink( $post );
			$post_excerpt   = get_the_excerpt( $post );

			$featured_image_markup = '';

			if ( $attributes['displayFeaturedImage'] ) {
				$featured_image = '';
				$image_style    = '';
				$image_size     = 'large';

				if ( has_post_thumbnail( $post ) ) {
					$featured_image = get_the_post_thumbnail(
						$post,
						$image_size,
						[
							'style' => esc_attr( $image_style ),
						]
					);
				}
				elseif ( isset( $attributes['featuredImageId'] ) ) {
					$image_classnames[] = 'attachment' . $image_size;
					$image_classnames[] = 'wp-post-image';

					$featured_image = wp_get_attachment_image(
						$attributes['featuredImageId'],
						$image_size,
						false,
						[
							'style' => esc_attr( $image_style ),
							'class' => esc_attr( implode( ' ', $image_classnames ) ),
						]
					);
				}

				if ( $featured_image ) {
					$featured_image = sprintf(
						'<a href="%1$s" aria-label="%2$s">%3$s</a>',
						esc_url( $post_permalink ),
						esc_attr( $post_title ),
						$featured_image
					);

					$figure_classnames[] = 'cycle-blocks-page-list__featured-image';

					$featured_image_markup = sprintf(
						'<figure class="%1$s">%2$s</figure>',
						esc_attr( implode( ' ', $figure_classnames ) ),
						$featured_image
					);
				}
			}

			$post_items_markup .= '<article id="post-' . $post_id . '" class="' . esc_attr( implode( ' ', get_post_class( '', $post_id ) ) ) . '">';
			$post_items_markup .= '<h2 class="cycle-blocks-page-list__entry-title"><a href="' . $post_permalink . '">' . $post_title . '</a></h2>';
			$post_items_markup .= $featured_image_markup;
			$post_items_markup .= '<div class="cycle-blocks-page-list__entry-content">';
			$post_items_markup .= $post_excerpt;
			$post_items_markup .= '</div>';
			$post_items_markup .= '</article>';
		}

		return $post_items_markup;
	}

	public function get_post_id_list_ancestor( $post, $root_ancestor = false ) {
		$ancestor_list = [];
		if ( is_object( $post ) && $post->post_parent ) {
			$ancestor_list[] = $post->post_parent;

			$ancestor = $post->post_parent;
			while ( $ancestor ) {
				$post_ancestor = get_post( $ancestor );
				if ( $post_ancestor->post_parent ) {
					$ancestor_list[] = $post_ancestor->post_parent;
				}

				$ancestor = $post_ancestor->post_parent;
			}

			if ( $root_ancestor ) {
				$ancestor_list[] = 0;
			}
		}

		return $ancestor_list;
	}

	public function get_post_id_ancestor( $post, $root_ancestor = false ) {
		$ancestor_list    = $this->get_post_id_list_ancestor( $post, $root_ancestor );
		$ancestor_post_id = array_pop( $ancestor_list );

		return $ancestor_post_id;
	}

}
