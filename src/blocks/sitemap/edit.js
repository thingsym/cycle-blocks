'use strict';

/**
 * WordPress dependencies
 */
import {
	Disabled,
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function sitemapEdit( { attributes, setAttributes } ) {
	const {
		displayPage,
		displayPost,
		displayCategory,
		displayAuthor,
	} = attributes;

	const inspectorControls = (
		<InspectorControls>
			<PanelBody title={ __( 'Sitemap settings', 'cycle-blocks' ) } >
				<ToggleControl
					label={ __( 'Display Page', 'cycle-blocks' ) }
					checked={ displayPage }
					onChange={ () =>
						setAttributes( {
							displayPage: !displayPage,
						} )
					}
				/>
				<ToggleControl
					label={ __( 'Display Post', 'cycle-blocks' ) }
					checked={ displayPost }
					onChange={ () =>
						setAttributes( {
							displayPost: !displayPost,
						} )
					}
				/>
				<ToggleControl
					label={ __( 'Display Category', 'cycle-blocks' ) }
					checked={ displayCategory }
					onChange={ () =>
						setAttributes( {
							displayCategory: !displayCategory,
						} )
					}
				/>
				<ToggleControl
					label={ __( 'Display Author', 'cycle-blocks' ) }
					checked={ displayAuthor }
					onChange={ () =>
						setAttributes( {
							displayAuthor: !displayAuthor,
						} )
					}
				/>
			</PanelBody>
		</InspectorControls>
	);

	return (
		<>
			{ inspectorControls }
			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="cycle-blocks/sitemap"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}
