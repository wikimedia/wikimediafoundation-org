import './style.scss';
import apiFetch from '@wordpress/api-fetch';
import {
	__experimentalLinkControlSearchInput as LinkControlSearchInput,
} from '@wordpress/block-editor';

const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;

const {
	Button,
	TextControl,
	ToggleControl,
	Popover,
} = wp.components;

const { withSelect, useSelect, withDispatch } = wp.data;
const { useState, useEffect } = wp.element;
const { compose } = wp.compose;

/**
 *
 */
const ProfileFields = ( { authorName, postMeta, setPostMeta } ) => {
	let links = null;
	let authors = null;
	// const { connectedUser } = useSelect( select => ( { connectedUser: select( 'core' ).getEntityRecord( 'postType', 'guest-author', postMeta.connected_user ) } ), { postMeta } );
	// console.dir(connectedUser);

	const [ suggestions, setSuggestions ] = useState( [] );
	const [ author, setAuthor ] = useState(authorName);
	const [ isSearchingAuthor, setIsSearchingAuthor ] = useState( false );

	useEffect( () => {
		return () => {
			findAuthor( author );
		};
	}, [ author ] );

	useEffect( () => {
		return () => {
			setAuthor( authorName );
		};
	} );
	/**
	 *
	 */
	const findAuthor = search => {
		const searchParams = new URLSearchParams( {
			search,
			per_page: 20,
			type: 'post',
			subtype: 'guest-author',
		} );
		apiFetch( { path: `/wp/v2/search?${searchParams}` } ).then( authors => {
			setSuggestions( authors.map( guest => {
				return {
					id: guest.id,
					title: guest.title,
				};
			} )
			);
		} );
	};

	/**
	 *
	 */
	const selectAuthor = ( id, name ) => {
		setPostMeta( { connected_user: id } );
		setAuthor( name );
		setIsSearchingAuthor( false );
	};

	if ( suggestions.length ) {
		authors = suggestions.map( author => {
			return (
				<Button key={ author.id }
					onClick={ () => selectAuthor( author.id, author.title ) }
				>{ author.title }</Button>
			);
		} );
	}

	if ( postMeta.contact_links.length ) {
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
					value={ author }
					onChange={ value => setAuthor( value ) }
					onFocus={ () => setIsSearchingAuthor( true ) } />
				{ isSearchingAuthor && suggestions.length > 0 && <Popover>{ authors }</Popover> }
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

const ProfileFieldsComposed = compose( [
	withSelect( select => {
		return {
			postMeta: select( 'core/editor' ).getEditedPostAttribute( 'meta' ),
			postType: select( 'core/editor' ).getCurrentPostType(),
			authorName: select( 'core' ).getEntityRecord( 'postType', 'guest-author', select( 'core/editor' ).getEditedPostAttribute( 'meta' )?.connected_user )?.author_name,
		};
	} ),
	withDispatch( dispatch => {
		return {
			/**
			 * Sets postmeta manipulated by our controls.
			 */
			setPostMeta( newMeta ) {
				dispatch( 'core/editor' ).editPost( { meta: newMeta } );
			},
		};
	} ),
] )( ProfileFields );

registerPlugin( 'profile-fields', {
	render: () => {
		return ( <ProfileFieldsComposed/> );
	},
} );
