/**
* External dependencies
*/
import { shallow } from 'enzyme';
import { shallowToJson } from 'enzyme-to-json';

/**
 * Internal dependencies
 */
import { default as fontawesomeIconsEdit } from '../../edit.js';

describe( 'fontawesomeIconsEdit snapshot', () => {
	it( 'fontawesomeIconsEdit snapshot', () => {
		const elem = shallowToJson( shallow( <fontawesomeIconsEdit /> ) );
		expect( elem ).toMatchSnapshot();
	} );
} );
