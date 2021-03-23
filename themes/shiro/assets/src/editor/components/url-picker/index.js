import { BlockControls, __experimentalLinkControl as LinkControl } from '@wordpress/block-editor';
import { Popover, ToolbarButton, ToolbarGroup, KeyboardShortcuts } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { rawShortcut, displayShortcut } from '@wordpress/keycodes';

import './style.scss';

/**
 * For selecting URLs in the Toolbar, like in core/button.
 * (In fact it's largely swiped from the core/button code.)
 *
 * This implementation is a little simpler than the core one and is currently
 * intended for more limited use-cases. In fact at the moment it's not intended
 * to be used outside the cta component, but it could be.
 */
function URLPicker( {
	isSelected,
	url,
	onChangeLink,
} ) {
	const [ isURLPickerOpen, setIsURLPickerOpen ] = useState( false );
	const urlIsSet = !! url;
	const urlIsSetandSelected = urlIsSet && isSelected;
	/**
	 * Handle opening url entry interface when toolbar button is clicked.
	 */
	const openLinkControl = () => {
		setIsURLPickerOpen( true );
		return false; // prevents default behaviour for event
	};

	/**
	 * Handle removing url (unsetting link) and closing url entry interface.
	 */
	const removeLink = () => {
		onChangeLink( undefined );
		setIsURLPickerOpen( false );
	};
	const linkControl = ( isURLPickerOpen || urlIsSetandSelected ) && (
		<Popover
			position="bottom center"
			onClose={ () => setIsURLPickerOpen( false ) }
		>
			<LinkControl
				className="wp-block-navigation-link__inline-link-input"
				// This empty array removes the "open in new tab" option.
				// For CTAs, the behavior is likely to always be the same, and
				// implementing this feature has been complicated. If necessary,
				// it can be added at a later date.
				settings={ [] }
				value={ {
					url,
				} }
				onChange={ link => onChangeLink( link.url, link ) }
			/>
		</Popover>
	);
	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					{ ! urlIsSet && (
						<ToolbarButton
							className="url-picker__link-button"
							icon="admin-links"
							name="link"
							shortcut={ displayShortcut.primary( 'k' ) }
							title={ __( 'Link' ) }
							onClick={ openLinkControl }
						/>
					) }
					{ urlIsSetandSelected && (
						<ToolbarButton
							className="url-picker__link-button"
							icon="editor-unlink"
							isActive
							name="link"
							shortcut={ displayShortcut.primaryShift( 'k' ) }
							title={ __( 'Unlink' ) }
							onClick={ removeLink }
						/>
					) }
				</ToolbarGroup>
			</BlockControls>
			{ isSelected && (
				<KeyboardShortcuts
					bindGlobal
					shortcuts={ {
						[ rawShortcut.primary( 'k' ) ]: openLinkControl,
						[ rawShortcut.primaryShift( 'k' ) ]: removeLink,
					} }
				/>
			) }
			{ linkControl }
		</>
	);
}

export default URLPicker;
