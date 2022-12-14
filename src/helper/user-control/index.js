/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { ComboboxControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { useState } from '@wordpress/element';

const AUTHORS_QUERY = {
	per_page: -1,
	_fields: 'id,name',
	context: 'view',
};

export default function UserControl( { value, onChange, who } ) {
	const [ filteredAuthorsList, setFilteredAuthorsList ] = useState();
	const authorsList = useSelect( ( select ) => {
		const { getUsers } = select( coreStore );

		if ( who ) {
			AUTHORS_QUERY.who = who;
		}
		return getUsers( AUTHORS_QUERY );
	}, [] );

	if ( ! authorsList ) {
		return null;
	}

	const options = authorsList.map( ( author ) => {
		return {
			label: author.name,
			value: author.id,
		};
	} );

	return (
		<ComboboxControl
			label={ __( 'User', 'cycle-blocks' ) }
			help={ __( 'Select the user to display, if it is blank it will use the post/page author.', 'cycle-blocks' ) }
			value={ value }
			onChange={ onChange }
			options={ filteredAuthorsList || options }
			onFilterValueChange={ ( inputValue ) =>
				setFilteredAuthorsList(
					options.filter( ( option ) =>
						option.label
							.toLowerCase()
							.startsWith( inputValue.toLowerCase() )
					)
				)
			}
		/>
	);
}
