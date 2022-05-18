const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { Fragment } = wp.element;

const {
	PanelBody,
	Button,
	IconButton,
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
			<PanelBody title={ __( 'Contact Links', 'shiro-admin' ) }>
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
			</PanelBody>
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
