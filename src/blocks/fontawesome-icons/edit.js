'use strict';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __, _x } from '@wordpress/i18n';
import {
	PanelBody,
	BaseControl,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	FontSizePicker,
	PanelColorSettings,
	ContrastChecker,
	BlockControls,
	AlignmentToolbar,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import IconSelectControl from '../../helper/icon-select-control/index.js';
import { IconSettings } from './icon-settings.js';

export default function fontawesomeIconsEdit( { attributes, setAttributes } ) {
	const {
		iconClassName,
		iconSize,
		enableInlineBlock,
		iconColor,
		backgroundColor,
		textAlign,
	} = attributes;

	const innerClassName = classnames( `${ iconClassName }` );

	const iconStyles = {
		fontSize: iconSize ? iconSize : null,
		color: iconColor || null,
	};

	const onChangeIconSize = ( value ) =>
		setAttributes( {
			iconSize: value,
		}
	);

	const onChangeBackgroundColor = ( value ) =>
		setAttributes( {
			backgroundColor: value,
		}
	);

	const onChangeIconColor = ( value ) =>
		setAttributes( {
			iconColor: value,
		}
	);

	const blockProps = useBlockProps( {
		className: classnames( {
			[ `has-text-align-${ textAlign }` ]: textAlign,
			[ `has-inline-icon` ]: enableInlineBlock,
		} ),
		style: {
			backgroundColor: backgroundColor || null,
			padding: enableInlineBlock && iconSize ? iconSize : null,
		}
	} );

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Icon settings',
						'cycle-blocks'
					)}
				>

					<ToggleControl
						label={ __( 'Enable inline block', 'editor-bridge' ) }
						value={ enableInlineBlock }
						checked={ enableInlineBlock }
						onChange={ () => {
							setAttributes( {
								enableInlineBlock: ! enableInlineBlock
							} );
						} }
					/>

					<BaseControl
						label={ __( 'Icon Size', 'cycle-blocks' ) }
					>
						<FontSizePicker
							value={ iconSize }
							onChange={ onChangeIconSize }
						/>
					</BaseControl>
					<IconSelectControl
						label={ __( 'Icons', 'cycle-blocks' ) }
						valueType="class"
						value={ iconClassName }
						options={ IconSettings }
						onChange={ ( value ) => {
							setAttributes( {
								iconClassName: value,
							} );
						} }
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __( 'Color Settings', 'cycle-blocks' ) }
					initialOpen={ false }
					colorSettings={ [
						{
							value: iconColor,
							onChange: onChangeIconColor,
							label: __( 'Icon Color', 'cycle-blocks' ),
						},
						{
							value: backgroundColor,
							onChange: onChangeBackgroundColor,
							label: __(
								'Background Color',
								'cycle-blocks'
							),
						},
					] }
				>
					<ContrastChecker
						backgroundColor={ backgroundColor }
						iconColor={ iconColor }
					/>
				</PanelColorSettings>
			</InspectorControls>

			<BlockControls>
				<AlignmentToolbar
					value={ textAlign }
					onChange={ ( nextAlign ) => {
						setAttributes( { textAlign: nextAlign } );
					} }
				/>
			</BlockControls>

			<i
				className={ innerClassName }
				style={ iconStyles }
			/>
		</div>
	);

}
