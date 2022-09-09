'use strict';

/**
 * WordPress dependencies
 */
import {
	registerBlockType,
	unstable__bootstrapServerSideBlockDefinitions,
} from '@wordpress/blocks';
import { __, _x } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import * as cycle_blocks_sitemap from '../blocks/sitemap/index.js';
/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 *
 */
const registerBlock = ( block ) => {
	if ( ! block ) {
		return;
	}

	let { metadata, settings, name } = block;

	if ( metadata ) {
		// for ServerSide Blocks
		unstable__bootstrapServerSideBlockDefinitions({ [name]: metadata });
	}

	[ metadata, settings ] = applyTextdomainMetadata( metadata, settings );

	registerBlockType( { name, ...metadata }, settings );
};

const applyTextdomainMetadata = ( metadata, settings ) => {
	if ( metadata ) {
		if ( !! metadata.title ) {
			metadata.title = _x( metadata.title, 'block title', 'cycle-blocks' );
			settings.title = metadata.title;
		}
		if ( !! metadata.description ) {
			metadata.description = _x( metadata.description, 'block description', 'cycle-blocks' );
			settings.description = metadata.description;
		}
		if ( !! metadata.keywords ) {
			metadata.keywords = __( metadata.keywords, 'cycle-blocks' );
			settings.keywords = metadata.keywords;
		}
	}

	return [ metadata, settings ];
}

[
	// Common blocks are grouped at the top to prioritize their display
	// in various contexts — like the inserter and auto-complete components.
	cycle_blocks_sitemap,
].forEach( registerBlock );
