'use strict';

/**
 * WordPress dependencies
 */
import {
	PanelBody,
	TextControl,
	SelectControl,
	RangeControl,
	ToggleControl,
	Disabled,
	Placeholder,
} from '@wordpress/components';
import { useMemo } from '@wordpress/element';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies
 */
import FeaturedImageUploadControl from '../../helper/featured-image-upload-control/index.js';

export default function pageListEdit( { attributes, setAttributes } ) {
	const {
		postId,
		layout,
		sortColumn,
		sortOrder,
		displayAncestor,
		columns,
		depth,
		displayFeaturedImage,
		featuredImageId,
	} = attributes;

	const getLayoutOptions = useMemo( () => {
		const selectOption = [
			{
				label: __( 'List', 'cycle-blocks' ),
				value: 'list',
			},
			{
				label: __( 'Card', 'cycle-blocks' ),
				value: 'card',
			},
			{
				label: __( 'Topics', 'cycle-blocks' ),
				value: 'topics',
			},
			{
				label: __( 'Article', 'cycle-blocks' ),
				value: 'article',
			}
		];

		return selectOption;
	}, [] );

	const getSortColumnOptions = useMemo( () => {
		const selectOption = [
			{
				label: __( 'menu_order', 'cycle-blocks' ),
				value: 'menu_order'
			},
			{
				label: __( 'post_title', 'cycle-blocks' ),
				value: 'post_title'
			},
			{
				label: __( 'post_date', 'cycle-blocks' ),
				value: 'post_date'
			},
			{
				label: __( 'post_modified', 'cycle-blocks' ),
				value: 'post_modified'
			},
			{
				label: __( 'ID', 'cycle-blocks' ),
				value: 'ID'
			}
		];

		return selectOption;
	}, [] );

	const getSortOrderOptions = useMemo( () => {
		const selectOption = [
			{
				label: __( 'ASC', 'cycle-blocks' ),
				value: 'ASC'
			},
			{
				label: __( 'DESC', 'cycle-blocks' ),
				value: 'DESC'
			}
		];

		return selectOption;
	}, [] );

	const inspectorControls = (
		<InspectorControls>
			<PanelBody title={ __( 'Settings', 'cycle-blocks' ) } >
				<TextControl
					label={ __( 'Post ID', 'cycle-blocks' ) }
					value={ postId }
					onChange={ ( value ) =>
						setAttributes( {
							postId: value ? value : '',
						} )
					}
				/>
				<SelectControl
					label={ __( 'Layout', 'cycle-blocks' ) }
					options={ getLayoutOptions }
					value={ layout }
					onChange={ ( value ) =>
						setAttributes( {
							layout: value ? value : '',
						} )
					}
				/>
				<SelectControl
					label={ __( 'Sort Column', 'cycle-blocks' ) }
					options={ getSortColumnOptions }
					value={ sortColumn }
					onChange={ ( value ) =>
						setAttributes( {
							sortColumn: value ? value : '',
						} )
					}
				/>
				<SelectControl
					label={ __( 'Sort Order', 'cycle-blocks' ) }
					options={ getSortOrderOptions }
					value={ sortOrder }
					onChange={ ( value ) =>
						setAttributes( {
							sortOrder: value ? value : '',
						} )
					}
				/>
				{ layout === 'list' && (
					<ToggleControl
						label={ __( 'Display Ancestor', 'cycle-blocks' ) }
						checked={ displayAncestor }
						onChange={ () =>
							setAttributes( {
								displayAncestor: ! displayAncestor,
							} )
						}
					/>
				) }
				{ layout === 'list' && (
					<RangeControl
						label={ __( 'Depth', 'cycle-blocks' ) }
						value={ depth }
						onChange={ ( value ) =>
							setAttributes( {
								depth: value
							} )
						}
						min={ 1 }
						max={ 4 }
					/>
				) }
				{ layout === 'card' && (
					<RangeControl
						label={ __( 'Columns', 'cycle-blocks' ) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ 2 }
						max={ 4 }
						required
					/>
				) }
				{ layout !== 'list' && (
					<ToggleControl
						label={ __( 'Display featured image', 'cycle-blocks' ) }
						checked={ displayFeaturedImage }
						onChange={ () =>
							setAttributes( {
								displayFeaturedImage: ! displayFeaturedImage
							} )
						}
					/>
				) }
				{ layout !== 'list' && displayFeaturedImage && (
					<FeaturedImageUploadControl
						setAttributes={ setAttributes }
						value={ featuredImageId }
					/>
				) }
			</PanelBody>
		</InspectorControls>
	);

	return (
		<>
			{ inspectorControls }
			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="cycle-blocks/page-list"
						attributes={ attributes }
						EmptyResponsePlaceholder={ () => (
							<Placeholder>
							{ __( 'Not Found pages. If the Post ID is blank or not a post/page, it cannot be viewed in the editor.', 'cycle-blocks' ) }
							</Placeholder>
						) }
					/>
				</Disabled>
			</div>
		</>
	);
}
