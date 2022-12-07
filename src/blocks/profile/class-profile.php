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
class Profile {
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

		$byline = ! empty( $attributes['byline'] ) ? $attributes['byline'] : false;

		$post_content = '';

		if ( isset( $attributes['showRecentPosts'] ) ) {
			$args = [
				'post_type'           => 'post',
				'posts_per_page'      => $attributes['postsToShow'],
				'post_status'         => 'publish',
				'order'               => 'desc',
				'orderby'             => 'date',
				'suppress_filters'    => false,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			];

			if ( isset( $author_id ) ) {
				$args['author'] = $author_id;
			}

			/**
			 * Filters the arguments for the Recent Posts.
			 *
			 * Filter hook: cycle_blocks/profile/recent_posts_args
			 *
			 * @see WP_Query
			 *
			 * @param array  $args     An array of arguments used to retrieve the recent posts.
			 */
			$recent_posts = new \WP_Query( apply_filters( 'cycle_blocks/profile/recent_posts_args', $args ) );

			if ( ! $recent_posts->have_posts() ) {
				return;
			}

			$list_items_markup = '';

			while ( $recent_posts->have_posts() ) {
				$recent_posts->the_post();

				$post_permalink = get_permalink( $post );
				$post_title     = get_the_title( $post );

				$featured_image_markup = '';

				if ( ! $post_title ) {
					$post_title = __( '(no title)', 'cycle-blocks' );
				}

				if ( $attributes['displayFeaturedImage'] && ( has_post_thumbnail( $post ) || isset( $attributes['featuredImageId'] ) ) ) {
					$featured_image     = '';
					$image_style        = '';

					$image_size = 'thumbnail';
					if ( 'grid' === $attributes['postLayout'] ) {
						$image_size = 'medium';
					}

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

					$figure_classnames[] = 'cycle-blocks-profile_recent-post__featured-image';

					$featured_image_markup = sprintf(
						'<figure class="%1$s"><a href="%2$s" aria-label="%3$s">%4$s</a></figure>',
						esc_attr( implode( ' ', $figure_classnames ) ),
						esc_url( $post_permalink ),
						esc_attr( $post_title ),
						$featured_image
					);
				}

				$post_time = sprintf(
					'<time datetime="%1$s" class="cycle-blocks-profile__post-date">%2$s</time>',
					esc_attr( get_the_date( 'c', $post ) ),
					esc_html( get_the_date( '', $post ) )
				);

				$post_permalink = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $post_permalink ),
					esc_html( $post_title )
				);

				$list_items_markup .= sprintf(
					'<li>%1$s<div class="cycle-blocks-profile_recent-post__content">%2$s%3$s</div></li>',
					$featured_image_markup,
					$post_time,
					$post_permalink
				);
			}

			wp_reset_postdata();

			$post_classnames = [];

			if ( isset( $attributes['postLayout'] ) && 'grid' === $attributes['postLayout'] ) {
				$post_classnames[] = 'cycle-blocks-profile__recent-post--layout-grid';
			}

			if ( isset( $attributes['postColumns'] ) && 'grid' === $attributes['postLayout'] ) {
				$post_classnames[] = 'cycle-blocks-profile__recent-post--columns-' . $attributes['postColumns'];
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

		$wrapper_classes[] = 'cycle-blocks-profile';
		$wrapper_classes[] = isset( $attributes['itemsJustification'] ) ? 'items-justified-' . $attributes['itemsJustification'] : null;

		$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => implode( ' ', $wrapper_classes ) ] );

		return sprintf( '<div %1$s>', $wrapper_attributes ) .
			( ! empty( $attributes['showTitle'] ) && $attributes['title'] ? '<div class="cycle-blocks-profile__header"><h3 class="cycle-blocks-profile__title">' . $attributes['title'] . '</h3></div>' : '' ) .
			( ! empty( $attributes['showAvatar'] ) && $avatar ? '<div class="cycle-blocks-profile__avatar">' . $avatar . '</div>' : '' ) .
			'<div class="cycle-blocks-profile__content">' .
			( ! empty( $byline ) ? '<p class="cycle-blocks-profile__byline">' . esc_html( $byline ) . '</p>' : '' ) .
			'<p class="cycle-blocks-profile__name">' . get_the_author_meta( 'display_name', $author_id ) . '</p>' .
			( ! empty( $attributes['showBio'] ) && get_the_author_meta( 'user_description', $author_id ) ? '<p class="cycle-blocks-profile__bio">' . get_the_author_meta( 'user_description', $author_id ) . '</p>' : '' ) .
			'</div>' .
			( ! empty( $attributes['showRecentPosts'] ) && $post_content ? '<div class="cycle-blocks-profile__recent-post">' . $post_content . '</div>' : '' ) .
			'</div>';
	}
}
