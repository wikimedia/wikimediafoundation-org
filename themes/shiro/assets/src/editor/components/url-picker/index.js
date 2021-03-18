import { BlockControls, __experimentalLinkControl as LinkControl } from '@wordpress/block-editor';
import { Popover, ToolbarButton, ToolbarGroup, KeyboardShortcuts } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { link, linkOff } from '@wordpress/icons';
import { rawShortcut, displayShortcut } from '@wordpress/keycodes';

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
	setAttributes,
	anchorRef,
} ) {
	const [ isURLPickerOpen, setIsURLPickerOpen ] = useState( false );
	const urlIsSet = !! url;
	const urlIsSetandSelected = urlIsSet && isSelected;
	/**
	 *
	 */
	const openLinkControl = () => {
		setIsURLPickerOpen( true );
		return false; // prevents default behaviour for event
	};

	/**
	 * Unset the URL, effectively removing the link.
	 */
	const unlinkButton = () => {
		setAttributes( {
			url: undefined,
		} );
		setIsURLPickerOpen( false );
	};
	const linkControl = ( isURLPickerOpen || urlIsSetandSelected ) && (
		<Popover
			anchorRef={ anchorRef?.current }
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
				onChange={ ( { url } ) => setAttributes( { url } ) }
			/>
		</Popover>
	);
	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					{ ! urlIsSet && (
						<ToolbarButton
							icon={ link }
							name="link"
							shortcut={ displayShortcut.primary( 'k' ) }
							title={ __( 'Link' ) }
							onClick={ openLinkControl }
						/>
					) }
					{ urlIsSetandSelected && (
						<ToolbarButton
							icon={ linkOff }
							isActive
							name="link"
							shortcut={ displayShortcut.primaryShift( 'k' ) }
							title={ __( 'Unlink' ) }
							onClick={ unlinkButton }
						/>
					) }
				</ToolbarGroup>
			</BlockControls>
			{ isSelected && (
				<KeyboardShortcuts
					bindGlobal
					shortcuts={ {
						[ rawShortcut.primary( 'k' ) ]: openLinkControl,
						[ rawShortcut.primaryShift( 'k' ) ]: unlinkButton,
					} }
				/>
			) }
			{ linkControl }
		</>
	);
}

export default URLPicker;
