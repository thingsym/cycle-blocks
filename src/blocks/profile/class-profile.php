<?php
/**
 * Profile Block
 *
 * @package Profile
 *
 * @since 1.0.0
 */

namespace Cycle_Blocks\Blocks;

/**
 * Core class Profile
 *
 * @since 1.0.0
 */
class profile {
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
			plugin_dir_path( CYCLE_BLOCKS ) . '/dist/blocks/profile',
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	public function render_callback( $attributes, $content, $block ) {

		if ( isset( $attributes['userId'] ) ) {
			$author_id = $attributes['userId'];
		}
		else {
			if ( ! isset( $block->context['postId'] ) ) {
				return;
			}

			$author_id = get_post_field( 'post_author', $block->context['postId'] );

			if ( empty( $author_id ) ) {
				return;
			}
		}

		$avatar = ! empty( $attributes['avatarSize'] ) ? get_avatar(
			$author_id,
			$attributes['avatarSize']
		) : null;

		$byline  = ! empty( $attributes['byline'] ) ? $attributes['byline'] : false;

		$post_content = '';

		if ( isset( $attributes['showRecentPosts'] ) ) {
			global $post;

			$args = [
				'post_type'        => 'post',
				'posts_per_page'   => 5,
				'post_status'      => 'publish',
				'order'            => 'desc',
				'orderby'          => 'date',
				'suppress_filters' => false,
			];

			if ( isset( $author_id ) ) {
				$args['author'] = $author_id;
			}

			/**
			 * Filters the arguments for the Recent Posts widget.
			 *
			 * Filter hook: cycle_blocks/profile/widget_posts_args
			 *
			 * @since 3.4.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see WP_Query::get_posts()
			 *
			 * @param array  $args     An array of arguments used to retrieve the recent posts.
			 */
			$recent_posts = get_posts( apply_filters( 'cycle_blocks/profile/widget_posts_args', $args ) );

			$list_items_markup = '';

			foreach ( $recent_posts as $post ) {
				$post_link = esc_url( get_permalink( $post ) );
				$title     = get_the_title( $post );
				$featured_image = '';

				if ( ! $title ) {
					$title = __( '(no title)', 'cycle-blocks' );
				}

				if ( $attributes['displayFeaturedImage'] && ( has_post_thumbnail( $post ) || isset( $attributes['featuredImageId'] ) ) ) {
					$image_style = '';
					$image_classnames[] = 'wp-block-cycle-blocks-profile__featured-image';

					if ( has_post_thumbnail( $post ) ) {
						$featured_image = get_the_post_thumbnail(
							$post,
							'thumbnail',
							[
								'style' => esc_attr( $image_style ),
							]
						);
					}
					else if ( isset( $attributes['featuredImageId'] ) ) {
						$featured_image = wp_get_attachment_image(
							$attributes['featuredImageId'],
							'thumbnail',
							false,
							[
								'style' => esc_attr( $image_style ),
								'class' => 'attachment-thumbnail size-thumbnail wp-post-image',
							]
						);
					}

					$featured_image = sprintf(
						'<figure class="%1$s"><a href="%2$s" aria-label="%3$s">%4$s</a></figure>',
						esc_attr( implode( ' ', $image_classnames ) ),
						esc_url( $post_link ),
						esc_attr( $title ),
						$featured_image
					);
				}

				$post_time = sprintf(
					'<time datetime="%1$s" class="wp-block-cycle-blocks-profile__post-date">%2$s</time>',
					esc_attr( get_the_date( 'c', $post ) ),
					esc_html( get_the_date( '', $post ) )
				);

				$post_link = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $post_link ),
					esc_html( $title )
				);

				$list_items_markup .= sprintf(
					'<li>%1$s<div class="wp-block-cycle-blocks-profile__recent-post-content">%2$s%3$s</div></li>',
					$featured_image,
					$post_time,
					$post_link
				);
			}

			wp_reset_postdata();

			$post_classnames = [];

			if ( isset( $attributes['postLayout'] ) && 'grid' === $attributes['postLayout'] ) {
				$post_classnames[] = 'is-grid';
			}

			if ( isset( $attributes['postColumns'] ) && 'grid' === $attributes['postLayout'] ) {
				$post_classnames[] = 'columns-' . $attributes['postColumns'];
			}

			if ( isset( $attributes['displayFeaturedImage'] ) ) {
				$post_classnames[] = 'has-thumbnail';
			}

			if ( $attributes['authorTitle'] ) {
				$post_content .= '<h4>' . $attributes['authorTitle'] . '</h4>';
			}

			$post_content .= sprintf(
				'<ul class="%1$s">%2$s</ul>',
				esc_attr( implode( ' ', $post_classnames ) ),
				$list_items_markup
			);
		}

		$classes = array_merge(
			isset( $attributes['className'] ) ? array( $attributes['className'] ) : array(),
			isset( $attributes['itemsJustification'] ) ? array( 'items-justified-' . $attributes['itemsJustification'] ) : array(),
		);

		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

		return sprintf(
			'<div %1$s>', $wrapper_attributes ) .
			( ! empty( $attributes['showTitle'] ) ? '<div class="wp-block-cycle-blocks-profile__header"><h3 class="wp-block-cycle-blocks-profile__title">' . $attributes['title'] . '</h3></div>' : '' ) .
			( ! empty( $attributes['showAvatar'] ) ? '<div class="wp-block-cycle-blocks-profile__avatar">' . $avatar . '</div>' : '' ) .
			'<div class="wp-block-cycle-blocks-profile__content">' .
			( ! empty( $byline ) ? '<p class="wp-block-cycle-blocks-profile__byline">' . esc_html( $byline ) . '</p>' : '' ) .
			'<p class="wp-block-cycle-blocks-profile__name">' . get_the_author_meta( 'display_name', $author_id ) . '</p>' .
			( ! empty( $attributes['showBio'] ) ? '<p class="wp-block-cycle-blocks-profile__bio">' . get_the_author_meta( 'user_description', $author_id ) . '</p>' : '' ) .
			'</div>' .
			( ! empty( $attributes['showRecentPosts'] ) ? '<div class="wp-block-cycle-blocks-profile__recent-post">' . $post_content . '</div>' : '' ) .
			'</div>';
	}
}
