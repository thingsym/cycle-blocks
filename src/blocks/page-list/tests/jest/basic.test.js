/**
 * Internal dependencies
 */
 import { metadata, name, settings } from '../../index';

describe( 'archives', () => {
	test( 'basic', () => {
		expect( name ).toBe( 'cycle-blocks/page-list' );

		expect( metadata ).toBeTruthy();
		expect( metadata.category ).toBe( 'cycle-blocks' );
		expect( metadata.textdomain ).toBe( 'cycle-blocks' );

		expect( settings ).toBeTruthy();
	} );
} );
