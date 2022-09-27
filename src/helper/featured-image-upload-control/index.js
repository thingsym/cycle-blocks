'use strict';

/**
 * WordPress dependencies
 */
import {
	BaseControl,
	Button,
	ResponsiveWrapper,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import {
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { store as coreStore } from '@wordpress/core-data';

export default function FeaturedImageUploadControl( { value, setAttributes, setMediaSizes } ) {
	const IMAGE_BACKGROUND_TYPE = 'image';
	const VIDEO_BACKGROUND_TYPE = 'video';
	const ALLOWED_MEDIA_TYPES = [ 'image' ];

	const featuredImageId = value;

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

	const onSelectMedia = ( media ) => {
		if ( ! media || ! media.url ) {
			setAttributes( {
				featuredImageId: undefined
			} );
			return;
		}
		let mediaType;
		// for media selections originated from a file upload.
		if ( media.media_type ) {
			if ( media.media_type === IMAGE_BACKGROUND_TYPE ) {
				mediaType = IMAGE_BACKGROUND_TYPE;
			} else {
				// only images and videos are accepted so if the media_type is not an image we can assume it is a video.
				// Videos contain the media type of 'file' in the object returned from the rest api.
				mediaType = VIDEO_BACKGROUND_TYPE;
			}
		} else { // for media selections originated from existing files in the media library.
			if (
				media.type !== IMAGE_BACKGROUND_TYPE &&
				media.type !== VIDEO_BACKGROUND_TYPE
			) {
				return;
			}
			mediaType = media.type;
		}

		setAttributes( {
			featuredImageId: media.id,
		} );
	};

	const onRemoveImage = () => {
		setAttributes( {
			featuredImageId: undefined,
		} );
	};

	const uploadFallback = (
		<p>
			{ __( 'Can not upload the image. you need permission to upload media.', 'cycle-blocks' ) }
		</p>
	);

	return (
		<BaseControl
			label={ __( 'Featured image', 'cycle-blocks' ) }
			className="block-editor-post-featured-image-control__row"
		>
			<MediaUploadCheck fallback={ uploadFallback }>
				<MediaUpload
					title={ __( 'Featured image', 'cycle-blocks' ) }
					onSelect={ onSelectMedia }
					allowedTypes={ ALLOWED_MEDIA_TYPES }
					modalClass="editor-post-featured-image__media-modal"
					value={ featuredImageId }
					render={ ( { open } ) => (
						<div className="editor-post-featured-image__container">
							<Button
								className={
									! featuredImageId
										? 'editor-post-featured-image__toggle'
										: 'editor-post-featured-image__preview'
								}
								onClick={ open }
							>
							{ !! featuredImageId && mediaSizes && (
								<ResponsiveWrapper
									naturalWidth={ mediaSizes[ 'thumbnail' ].width }
									naturalHeight={ mediaSizes[ 'thumbnail' ].height }
									isInline
								>
									<img
										src={ mediaSizes[ 'thumbnail' ].source_url }
										alt=""
									/>
								</ResponsiveWrapper>
							) }
							{ ! featuredImageId &&
								( __( 'Set featured image', 'cycle-blocks' ) )
							}
							</Button>
						</div>
					) }
				/>
			</MediaUploadCheck>
			{ !! featuredImageId && (
				<MediaUploadCheck>
					<Button
						onClick={ onRemoveImage }
						variant="link"
						isDestructive
					>
						{ __( 'Remove image', 'cycle-blocks' ) }
					</Button>
				</MediaUploadCheck>
			) }
		</BaseControl>
	);
}
