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
 * Wraps a set of ContactLinks.
 */
const ContactLinkList = props => {
	const { list, setListItem } = props;
	return ( <ul>
		{ list.map( ( item, index ) => {
			return ( <ContactLink
				index={ index }
				item={ item }
				setLink={ setListItem }
			/> );
		} ) }
	</ul> );
};

/**
 * Individual, editable, ContactLink.
 * Manage a single instance of a contact vector, i.e. email, social, etc.
 */
const ContactLink = props => {
	const { index, item, setLink } = props;
	return ( <li key={ index } className={ 'profile-fields__contact-link' }>
		<TextControl label={ __( 'Title', 'shiro-admin' ) }
			value={ item.title }
			onChange={ value => setLink( index, { title: value } ) }
		/>
		<TextControl label={ __( 'Link', 'shiro-admin' ) }
			value={ item.link }
			onChange={ value => setLink( index, { link: value } ) }
		/>
		<Button
			isDestructive
			isSmall
			onClick={ () => setLink( index, null ) }
		>{ __( 'Remove Link' ) }</Button>
	</li> );
};

/**
 * Panel providing an interface to several pieces of Profile metadata.
 */
const ProfileFields = () => {
	const postType = useSelect( select => select( 'core/editor' ).getCurrentPostType(), [] );

	const [ postMeta, setPostMeta ] = useEntityProp( 'postType', postType, 'meta' );

	/**
	 * "Rehydrates" the post ID for the Connected User into a full post object,
	 * then extracts only the values we need for the Combobox.
	 */
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

	/**
	 * Stores the "search value," i.e. whatever the user is typing, for the
	 * Connected User combobox. We store this in state so that it can be used
	 * in the following section to optionally modify the authors returned for
	 * the option set.
	 */
	const [ userSearch, setUserSearch ] = useState( '' );

	/**
	 * Get a selection of guest authors for the Connected User UI.
	 * Optionally searches as well, since there are a lot more guest authors
	 * than we would want to get in a single request.
	 */
	const { isLoading, allUsers } = useSelect(
		select => {
			const { getEntityRecords, isResolving } = select(
				store
			);
			return {
				allUsers: getEntityRecords( 'postType', 'guest-author', {
					per_page: 10,
					search: userSearch,
				} ),
				isLoading: isResolving( 'core', 'getEntityRecords', [] ),
			};
		},
		[ userSearch ]
	);

	/**
	 * Modified version of the authors returned to allUsers that winnows the
	 * data down for use in a Combobox.
	 */
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
	}, [ allUsers, postMeta.connected_user, connectedUser ] );

	/**
	 * Updates the connected_user postmeta. Used as a callback.
	 */
	const updateConnectedUser = id => {
		setPostMeta( {
			...postMeta,
			connected_user: id,
		} );
	};

	// We only want to load this on profiles.
	if ( postType !== 'profile' ) {
		return null;
	}

	return (
		<PluginDocumentSettingPanel
			icon={ 'admin-users' }
			title={ __( 'Metadata', 'shiro-admin' ) }
		>
			<ToggleControl checked={ postMeta.profile_featured }
				label={ __( 'Featured Profile?' ) }
				onChange={ value => setPostMeta( { profile_featured: value } ) }
			/>
			<TextControl label={ __( 'Last Name' ) }
				value={ postMeta.last_name }
				onChange={ value => setPostMeta( { last_name: value } ) }
			/>
			<TextControl label={ __( 'Role' ) }
				value={ postMeta.profile_role }
				onChange={ value => setPostMeta( { profile_role: value } ) }
			/>
			<ComboboxControl isLoading={ isLoading }
				label={ 'Connected User' }
				options={ usersOptions }
				value={ postMeta.connected_user }
				onChange={ updateConnectedUser }
				onFilterValueChange={ setUserSearch }
			/>
			<h2>{ __( 'Contact Links', 'shiro-admin' ) }</h2>
			<ContactLinkList
				list={ postMeta.contact_links }
				setListItem={ ( index, data ) => {
					const contact_links = [ ...postMeta.contact_links ];
					if ( data === null ) {
						// If data is null, that means delete this entry.
						contact_links.splice( index, 1 );
					} else {
						contact_links[index] = {
							...contact_links[index],
							data,
						};
					}
					setPostMeta( { contact_links } );
				} }
			/>
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

registerPlugin( 'profile-fields', {
	render: () => {
		return ( <ProfileFields/> );
	},
} );
