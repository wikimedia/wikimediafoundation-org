import './style.scss';
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
} = wp.components;

const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;

/**
 *
 */
const ProfileFields = ( { postType, postMeta, setPostMeta } ) => {
	let links = null;

	/**
	 *
	 */
	const suggestionsRender = props => (
		<div className="components-dropdown-menu__menu">
			{ props.suggestions.map( ( suggestion, index ) => {
				console.log( suggestion );
				return (
					<div className="components-button components-dropdown-menu__menu-item is-active has-text has-icon" >{ suggestion.title }</div> );
			} ) }
		</div>
	);

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
			<LinkControlSearchInput
				allowDirectEntry={ false }
				placeholder="Search here..."
				renderSuggestions={ value => {
					console.dir( value );
					return suggestionsRender( value );
				} }
				suggestionsQuery={ {
					type: 'post',
					subtype: 'guest-author',
				} }
				value={ postMeta.connected_user }
				withCreateSuggestion={ false }
				withURLSuggestion={ false }
				onChange={ connected_user => setPostMeta( { connected_user } ) }
			/>
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
		return ( <ProfileFieldsComposed /> );
	},
} );
