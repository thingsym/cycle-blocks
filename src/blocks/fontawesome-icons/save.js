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
		iconColor,
		backgroundColor,
		textAlign,
	} = attributes;

	const innerClassName = classnames( `${ iconClassName }` );

	const iconStyles = {
		fontSize: iconSize ? iconSize : null,
		color: iconColor || null,
	};

	const blockProps = useBlockProps.save( {
		className: classnames( {
			[ `has-text-align-${ textAlign }` ]: textAlign,
		} ),
		style: { backgroundColor: backgroundColor || null }
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
