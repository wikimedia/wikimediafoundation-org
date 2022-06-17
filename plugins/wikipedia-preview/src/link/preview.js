import { Popover } from '@wordpress/components';
import {
	useState,
	useEffect,
	useLayoutEffect,
	useCallback,
} from '@wordpress/element';
import { useAnchorRef } from '@wordpress/rich-text';
import { __ } from '@wordpress/i18n';
import { getPreviewHtml } from 'wikipedia-preview';

export const PreviewEditUI = ( {
	contentRef,
	settings,
	value,
	activeAttributes,
	onClose,
	onForceClose,
	onEdit,
	onRemove,
} ) => {
	const [ previewHtml, setPreviewHtml ] = useState( null );
	const anchorRef = useAnchorRef( {
		ref: contentRef,
		value,
		settings,
	} );
	const onClickPopoverOutside = useCallback( ( e ) => {
		if ( e.target.className === 'components-popover__content' ) {
			onForceClose();
		}
	}, [] );

	useEffect( () => {
		const { title, lang } = activeAttributes;
		if ( title && lang ) {
			getPreviewHtml( title, lang, ( preview ) => {
				setPreviewHtml( preview );
			} );
		}
	}, [ activeAttributes ] );

	useLayoutEffect( () => {
		document
			.querySelector( '.wikipediapreview-header-closebtn' )
			?.addEventListener( 'click', onForceClose );
		return () => {
			document
				.querySelector( '.wikipediapreview-header-closebtn' )
				?.removeEventListener( 'click', onForceClose );
		};
	}, [ previewHtml ] );

	return (
		<div>
			<Popover
				anchorRef={ anchorRef }
				onClose={ onClose }
				position="bottom center"
				noArrow={ false }
				expandOnMobile={ true }
				className="wikipediapreview-edit-preview-popover"
				onClick={ onClickPopoverOutside }
			>
				<div className="wikipediapreview-edit-preview-container">
					<div
						className="wikipediapreview-edit-preview"
						dangerouslySetInnerHTML={ { __html: previewHtml } }
					></div>
					{ previewHtml && (
						<ControllerEditUI
							onEdit={ onEdit }
							onRemove={ onRemove }
						/>
					) }
				</div>
			</Popover>
		</div>
	);
};

const ControllerEditUI = ( { onEdit, onRemove } ) => {
	return (
		<div className="wikipediapreview-edit-preview-controllers">
			<div
				className="wikipediapreview-edit-preview-controllers-change"
				onClick={ onEdit }
				role="presentation"
			>
				{ __( 'Change', 'wikipedia-preview' ) }
			</div>
			<div
				className="wikipediapreview-edit-preview-controllers-remove"
				onClick={ onRemove }
				role="presentation"
			>
				{ __( 'Remove', 'wikipedia-preview' ) }
			</div>
		</div>
	);
};
