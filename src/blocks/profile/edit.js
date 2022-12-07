'use strict';

/**
 * External dependencies
 */
import { get, forEach, invoke } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { RawHTML } from '@wordpress/element';
 import {
	BlockControls,
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	RangeControl,
	ToolbarGroup,
	Placeholder,
} from '@wordpress/components';
import { dateI18n, format, getSettings } from '@wordpress/date';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { store as coreStore } from '@wordpress/core-data';
import { pin, list, grid } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import UserControl from '../../helper/user-control/index.js';
import FeaturedImageUploadControl from '../../helper/featured-image-upload-control/index.js';

const { title: metadataTitle } = metadata;

const MAX_POSTS_COLUMNS = 6;

export default function profileEdit( {
	isSelected,
	context: { postType, postId, queryId },
	attributes,
	setAttributes,
} ) {
	const {
		userId,
		showTitle,
		title,
		showAvatar,
		showBio,
		showRecentPosts,
		authorTitle,
		postsToShow,
		displayFeaturedImage,
		featuredImageId,
		postLayout,
		postColumns,
		byline
	} = attributes;

	const { authorId, authorDetails } = useSelect(
		( select ) => {
			const { getEditedEntityRecord, getUser } = select( coreStore );

			const _authorId = ! userId ? getEditedEntityRecord(
				'postType',
				postType,
				postId
			)?.author : null;

			return {
				authorId: userId ? userId : _authorId,
				authorDetails: userId ? getUser( userId ) : getUser( _authorId ),
			};
		},
		[
			userId,
		]
	);

	const latestPosts = useSelect(
		( select ) => {
			if ( ! showRecentPosts ) {
				return {};
			}

			const { getEntityRecords, getMedia } = select( coreStore );

			const posts = getEntityRecords(
				'postType',
				'post',
				{
					author: authorId,
					order: 'desc',
					orderby: 'date',
					per_page: postsToShow,
				}
			);

			const latestPosts = ! Array.isArray( posts )
				? posts
				: posts.map( ( post ) => {
					if ( ! post.featured_media ) return post;

					const image = getMedia( post.featured_media );
					let url = get(
						image,
						[
							'media_details',
							'sizes',
							'thumbnail',
							'source_url',
						],
						null
					);
					if ( ! url ) {
						url = get( image, 'source_url', null );
					}
					const featuredImageInfo = {
						url,
						// eslint-disable-next-line camelcase
						alt: image?.alt_text,
					};
					return { ...post, featuredImageInfo };
				} );

			return latestPosts;
		},
		[
			userId,
			authorId,
			showRecentPosts,
			postsToShow,
			displayFeaturedImage
		]
	);

	const avatarSizes = [];
	if ( authorDetails ) {
		forEach( authorDetails.avatar_urls, ( url, size ) => {
			avatarSizes.push( {
				value: size,
				label: `${ size } x ${ size }`,
			} );
		} );
	}

	const layoutControls = [
		{
			icon: list,
			title: __( 'List view', 'cycle-blocks' ),
			onClick: () => setAttributes( { postLayout: 'list' } ),
			isActive: postLayout === 'list',
		},
		{
			icon: grid,
			title: __( 'Grid view', 'cycle-blocks' ),
			onClick: () => setAttributes( { postLayout: 'grid' } ),
			isActive: postLayout === 'grid',
		},
	];

	const dateFormat = getSettings().formats.date;

	const blockProps = useBlockProps( {
		className: 'cycle-blocks-profile'
	} );

	const post_class = classnames( {
		'cycle-blocks-profile__recent-post--layout-grid': postLayout === 'grid',
		[ `cycle-blocks-profile__recent-post--columns-${ postColumns }` ]: postLayout === 'grid',
	} );

	const { mediaSizes } = useSelect(
		( select ) => {
			if ( ! featuredImageId ) {
				return {};
			}

			const { getMedia } = select( coreStore );
			const mediaObj = getMedia( featuredImageId, { context: 'view' } );

			return {
				media: mediaObj,
				mediaSizes: mediaObj ? mediaObj.media_details.sizes : undefined,
			};
		},
		[ featuredImageId ]
	);

	const hasPosts = !! latestPosts?.length;

	const inspectorControls = (
		<InspectorControls>
			<PanelBody title={ __( 'Profile Settings', 'cycle-blocks' ) }>
				<UserControl
					value={ attributes?.userId }
					onChange={ ( value ) => {
						setAttributes( {
							userId: value,
						} );
					} }
				/>

				<ToggleControl
					label={ __( 'Show title', 'cycle-blocks' ) }
					checked={ showTitle }
					onChange={ () =>
						setAttributes( { showTitle: ! showTitle } )
					}
				/>
				<ToggleControl
					label={ __( 'Show avatar', 'cycle-blocks' ) }
					checked={ showAvatar }
					onChange={ () =>
						setAttributes( { showAvatar: ! showAvatar } )
					}
				/>
				{ showAvatar && (
					<SelectControl
						label={ __( 'Avatar size', 'cycle-blocks' ) }
						value={ attributes.avatarSize }
						options={ avatarSizes }
						onChange={ ( size ) => {
							setAttributes( {
								avatarSize: Number( size ),
							} );
						} }
					/>
				) }
				<ToggleControl
					label={ __( 'Show bio', 'cycle-blocks' ) }
					checked={ showBio }
					onChange={ () =>
						setAttributes( { showBio: ! showBio } )
					}
				/>
				<ToggleControl
					label={ __( 'Show recent posts', 'cycle-blocks' ) }
					checked={ showRecentPosts }
					onChange={ () =>
						setAttributes( { showRecentPosts: ! showRecentPosts } )
					}
				/>

				{ showRecentPosts && (
					<RangeControl
						label={ __( 'Number of Posts', 'cycle-blocks' ) }
						value={ postsToShow }
						onChange={ ( value ) =>
							setAttributes( { postsToShow: value } )
						}
						min={ 1 }
						max={ 24 }
						required
					/>
				) }
				{ showRecentPosts && postLayout === 'grid' && (
					<RangeControl
						label={ __( 'Columns', 'cycle-blocks' ) }
						value={ postColumns }
						onChange={ ( value ) =>
							setAttributes( { postColumns: value } )
						}
						min={ 3 }
						max={
							! hasPosts
								? MAX_POSTS_COLUMNS
								: Math.min(
										MAX_POSTS_COLUMNS,
										latestPosts.length
									)
						}
						required
					/>
				) }
				{ showRecentPosts && (
					<ToggleControl
						label={ __( 'Display featured image', 'cycle-blocks' ) }
						checked={ displayFeaturedImage }
						onChange={ () =>
							setAttributes( { displayFeaturedImage: ! displayFeaturedImage } )
						}
					/>
				) }
				{ showRecentPosts && displayFeaturedImage && (
					<FeaturedImageUploadControl
						value={ featuredImageId }
						setAttributes={ setAttributes }
					/>
				) }
			</PanelBody>
		</InspectorControls>
	);

	if ( ! authorId ) {
		return (
			<div { ...blockProps }>
				{ inspectorControls }
				{ showRecentPosts && (
					<BlockControls>
						<ToolbarGroup controls={ layoutControls } />
					</BlockControls>
				) }
				<Placeholder
					icon={ pin }
					label={ metadataTitle }
				>
					{ __( 'Not Found authorId. If the User is blank or not a post/page, it cannot be viewed in the editor.', 'cycle-blocks' ) }
				</Placeholder>
			</div>
		);
	}

	return (
		<>
			{ inspectorControls }
			{ showRecentPosts && (
				<BlockControls>
					<ToolbarGroup controls={ layoutControls } />
				</BlockControls>
			) }
			<div { ...blockProps }>
				{ showTitle && (
					<div className="cycle-blocks-profile__header">
						{ ( ! RichText.isEmpty( title ) || isSelected ) && (
							<RichText
								className="cycle-blocks-profile__title"
								tagName={ "h3" }
								multiline={ false }
								aria-label={ __( 'Title', 'cycle-blocks' ) }
								placeholder={ __( 'Write title…', 'cycle-blocks' ) }
								value={ title }
								onChange={ ( value ) =>
									setAttributes( { title: value } )
								}
							/>
						) }
					</div>
				) }
				{ showAvatar && authorDetails && (
					<div className="cycle-blocks-profile__avatar">
						<img
							width={ attributes.avatarSize }
							src={
								authorDetails.avatar_urls[
									attributes.avatarSize
								]
							}
							alt={ authorDetails.name }
						/>
					</div>
				) }
				<div className="cycle-blocks-profile__content">
					{ ( ! RichText.isEmpty( byline ) || isSelected ) && (
						<RichText
							className="cycle-blocks-profile__byline"
							multiline={ false }
							aria-label={ __( 'Post author byline text', 'cycle-blocks' ) }
							placeholder={ __( 'Write byline…', 'cycle-blocks' ) }
							value={ byline }
							onChange={ ( value ) =>
								setAttributes( { byline: value } )
							}
						/>
					) }

					{ authorDetails?.name && (
						<p className="cycle-blocks-profile__name">
							{ authorDetails?.name || __( 'Post Author', 'cycle-blocks' ) }
						</p>
					) }
					{ authorDetails?.description && showBio && (
						<p className="cycle-blocks-profile__bio">
							{ authorDetails?.description }
						</p>
					) }
				</div>
				{ showRecentPosts && latestPosts && (
					<div className="cycle-blocks-profile__recent-post">
						{ ( ! RichText.isEmpty( authorTitle ) || isSelected ) && (
							<RichText
								className="cycle-blocks-profile__author_title"
								tagName={ "h4" }
								multiline={ false }
								aria-label={ __( 'Author title', 'cycle-blocks' ) }
								placeholder={ __( 'Write title…', 'cycle-blocks' ) }
								value={ authorTitle }
								onChange={ ( value ) =>
									setAttributes( { authorTitle: value } )
								}
							/>
						) }
							<ul className={ post_class }>
								{ latestPosts.map( ( post, i ) => {
									const titleTrimmed = invoke( post, [
										'title',
										'rendered',
										'trim',
									] );

									const {
										featuredImageInfo: {
											url: imageSourceUrl,
											alt: featuredImageAlt,
										} = {},
									} = post;

									const renderFeaturedImage =
										displayFeaturedImage && ( imageSourceUrl || mediaSizes );

									const image_size = postLayout === 'grid' ? 'medium' : 'thumbnail';

									const featuredImage = renderFeaturedImage && (
										<img
											src={
												imageSourceUrl ? imageSourceUrl
												: mediaSizes[ image_size ] ? mediaSizes[ image_size ].source_url
												: mediaSizes[ 'full' ].source_url ? mediaSizes[ 'full' ].source_url
												: ''
											}
											alt={ featuredImageAlt }
											className={ `attachment-${ image_size } wp-post-image` }
										/>
									);

									return (
										<li key={ i }>
											{ displayFeaturedImage && featuredImage && (
												<div className="cycle-blocks-profile_recent-post__featured-image">
													<a href={ post.link } rel="noreferrer noopener">{ featuredImage }</a>
												</div>
											) }
											<div className="cycle-blocks-profile_recent-post__content">
												{ post.date_gmt && (
													<time
														dateTime={ format( 'c', post.date_gmt ) }
														className="cycle-blocks-profile__post-date"
													>
														{ dateI18n( dateFormat, post.date_gmt ) }
													</time>
												) }
												<a href={ post.link } rel="noreferrer noopener">
													{ titleTrimmed ? (
														<RawHTML>{ titleTrimmed }</RawHTML>
													) : (
														__( '(no title)', 'cycle-blocks' )
													) }
												</a>
											</div>
										</li>
									);
							} ) }
						</ul>
					</div>
				) }
			</div>
		</>
	);
}
