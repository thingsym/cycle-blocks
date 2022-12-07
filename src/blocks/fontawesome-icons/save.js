'use strict';

/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

export default function fontawesomeIconsSave( { attributes, setAttributes } ) {
	const {
		iconClassName,
		iconSize,
		enableInlineBlock,
		iconColor,
		backgroundColor,
		textAlign,
	} = attributes;

	const innerClassName = classnames(
		`${ iconClassName }`
	);

	const iconStyles = {
		fontSize: iconSize ? iconSize : null,
		color: iconColor || null,
	};

	const blockProps = useBlockProps.save( {
		className: classnames(
			'cycle-blocks-fontawesome-icons',
			{
				[ `has-text-align-${ textAlign }` ]: textAlign,
				[ `has-inline-icon` ]: enableInlineBlock,
			}
		),
		style: {
			backgroundColor: backgroundColor || null,
			padding: enableInlineBlock && iconSize ? iconSize : null,
		}
	} );

	return (
		<div { ...blockProps }>
			<i
				className={ innerClassName }
				style={ iconStyles }
			/>
		</div>
	);
}
