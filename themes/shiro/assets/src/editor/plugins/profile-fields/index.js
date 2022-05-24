import './style.scss';
import { store, useEntityProp } from '@wordpress/core-data';

const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;

const {
	Button,
	TextControl,
	ToggleControl,
	ComboboxControl,
} = wp.components;

const { useSelect } = wp.data;
const { useState, useMemo } = wp.element;

/**
 *
 */
const ProfileFields = () => {
	let links = null;

	const postType = useSelect(
		select => select( 'core/editor' ).getCurrentPostType(),
		[]
	);
	const [ postMeta, setPostMeta ] = useEntityProp( 'postType', postType, 'meta' );
	/**
	 * Set post meta values.
	 */
	const [ suggestions, setSuggestions ] = useState( [] );
	const { connectedUser } = useSelect( select => {
		const { getEntityRecord } = select( store );
		const user = getEntityRecord( 'postType', 'guest-author', postMeta.connected_user );
		return user ? {
			connectedUser: {
				value: user.id,
				label: user.author_name,
			},
		} : {
			connectedUser: {
				value: postMeta.connected_user,
				label: 'Loading...',
			},
		};
	}, [ postMeta.connected_user ] );
	const { isLoading, allUsers } = useSelect(
		select => {
			const { getEntityRecords, isResolving } = select(
				store
			);
			return {
				allUsers: getEntityRecords( 'postType', 'guest-author', {
					per_page: -1,
				} ),
				isLoading: isResolving( 'core', 'getEntityRecords', [] ),
			};
		},
		[]
	);
	const usersOptions = useMemo( () => {
		const fetchedUsers = ( allUsers ?? [] ).map( user => {
			return {
				value: user.id,
				label: user.author_name,
			};
		} );

		// Ensure the current process owner is included in the dropdown list.
		const foundUser = fetchedUsers.findIndex(
			( { value } ) => postMeta.connected_user === value
		);

		if ( foundUser < 0 && postMeta.connected_user ) {
			return [
				connectedUser,
				...fetchedUsers,
			];
		}

		return fetchedUsers;
	}, [ allUsers, postMeta.connected_user ] );
	/**
	 *
	 */
	const updateConnectedUser = id => {
		setPostMeta( {
			...postMeta,
			connected_user: id,
		} );
	};
	const [ isSearchingAuthor, setIsSearchingAuthor ] = useState( false );
	/**
	 *
	 */
	// const findAuthor = search => {
	// 	const searchParams = new URLSearchParams( {
	// 		search,
	// 		per_page: 20,
	// 		type: 'post',
	// 		subtype: 'guest-author',
	// 	} );
	// 	apiFetch( { path: `/wp/v2/search?${searchParams}` } ).then( authors => {
	// 		setSuggestions( authors.map( guest => {
	// 			return {
	// 				value: guest.id,
	// 				label: guest.title,
	// 			};
	// 		} )
	// 		);
	// 	} );
	// };

	/**
	 *
	 */
	// const selectAuthor = ( id, name ) => {
	// 	setPostMeta( { connected_user: id } );
	// 	setConnectedUser( {
	// 		value: id,
	// 		label: name,
	// 	} );
	// 	setIsSearchingAuthor( false );
	// };
	//
	// if ( suggestions && suggestions.length ) {
	// 	authors = suggestions.map( author => {
	// 		return (
	// 			<Button key={ author.id }
	// 				onClick={ () => selectAuthor( author.id, author.title ) }
	// 			>{ author.title }</Button>
	// 		);
	// 	} );
	// }

	if ( postMeta && postMeta.contact_links.length ) {
		links = postMeta.contact_links.map( ( link, index ) => {
			return ( <li key={ index } className={ 'profile-fields__contact-link' }>
				<TextControl label={ __( 'Title', 'shiro-admin' ) }
					value={ postMeta.contact_links[index].title }
					onChange={ value => {
						const contact_links = [ ...postMeta.contact_links ];
						contact_links[index].title = value;
						setPostMeta( { contact_links } );
					} }
				/>
				<TextControl label={ __( 'Link', 'shiro-admin' ) }
					value={ postMeta.contact_links[index].link }
					onChange={ value => {
						const contact_links = [ ...postMeta.contact_links ];
						contact_links[index].link = value;
						setPostMeta( { contact_links } );
					} }
				/>
				<Button
					isDestructive
					isSmall
					onClick={ () => {
						const contact_links = [ ...postMeta.contact_links ];
						contact_links.splice( index, 1 );
						setPostMeta( { contact_links } );
					} }
				>{ __( 'Remove Link' ) }</Button>
			</li> );
		} );
	}
	return (
		<PluginDocumentSettingPanel
			icon={ 'admin-users' }
			title={ __( 'Metadata', 'shiro-admin' ) }
		>
			<TextControl label={ __( 'Last Name' ) }
				value={ postMeta.last_name }
				onChange={ value => setPostMeta( { last_name: value } ) }
			/>
			<TextControl label={ __( 'Role' ) }
				value={ postMeta.profile_role }
				onChange={ value => setPostMeta( { profile_role: value } ) }
			/>
			<ToggleControl checked={ postMeta.profile_featured }
				label={ __( 'Featured?' ) }
				onChange={ value => setPostMeta( { profile_featured: value } ) }
			/>
			<div>
				<TextControl label={ 'Author' }
					// value={ connectedUser.label }
					// onChange={ value => setAuthor( value ) }
					// onFocus={ () => setIsSearchingAuthor( true ) }
				/>
				<ComboboxControl isLoading={ isLoading }
					label={ 'Connected User' }
					options={ usersOptions }
					value={ postMeta.connected_user }
					onChange={ updateConnectedUser }
				/>
			</div>
			<h2>{ __( 'Contact Links', 'shiro-admin' ) }</h2>
			<ul>
				{ links }
			</ul>
			<Button
				isDefault
				onClick={ () => setPostMeta( {
					contact_links: [ ...postMeta.contact_links, {
						title: '',
						link: '',
					} ],
				} ) }
			>{ __( 'Add Contact Link' ) }</Button>
		</PluginDocumentSettingPanel>
	);
};

// const ProfileFieldsComposed = compose( [
// 	withSelect( select => {
// 		return {
// 			postMeta: select( 'core/editor' ).getEditedPostAttribute( 'meta' ),
// 			postType: select( 'core/editor' ).getCurrentPostType(),
// 			authorName: select( 'core' ).getEntityRecord( 'postType', 'guest-author', select( 'core/editor' ).getEditedPostAttribute( 'meta' )?.connected_user )?.author_name,
// 		};
// 	} ),
// 	withDispatch( dispatch => {
// 		return {
// 			/**
// 			 * Sets postmeta manipulated by our controls.
// 			 */
// 			setPostMeta( newMeta ) {
// 				dispatch( 'core/editor' ).editPost( { meta: newMeta } );
// 			},
// 		};
// 	} ),
// ] )( ProfileFields );

registerPlugin( 'profile-fields', {
	render: () => {
		return ( <ProfileFields/> );
	},
} );
