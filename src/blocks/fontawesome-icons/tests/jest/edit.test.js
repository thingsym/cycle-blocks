/**
* External dependencies
*/
import { shallow } from 'enzyme';

/**
 * Internal dependencies
 */
import { default as fontawesomeIconsEdit } from '../../edit.js';

describe( 'fontawesomeIconsEdit', () => {
	it( 'fontawesomeIconsEdit renders', () => {
		const wrapper = shallow( <fontawesomeIconsEdit /> );
		expect( wrapper ).toBeTruthy();
	} );
} );
