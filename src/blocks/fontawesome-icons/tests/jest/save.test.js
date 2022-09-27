/**
* External dependencies
*/
import { shallow } from 'enzyme';

/**
 * Internal dependencies
 */
import { default as fontawesomeIconsSave } from '../../save.js';

describe( 'fontawesomeIconsSave', () => {
	it( 'fontawesomeIconsSave renders', () => {
		const wrapper = shallow( <fontawesomeIconsSave /> );
		expect( wrapper ).toBeTruthy();
	} );
} );
