import {
	Popover,
	TextControl,
	Button,
	KeyboardShortcuts,
} from '@wordpress/components';
import { getTextContent, slice, useAnchorRef } from '@wordpress/rich-text';
import { useState, useEffect, createRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { getSiteLanguage } from './utils';
import { prefixSearch, fulltextSearch, abortAllRequest } from './api';
import { LanguageSelector } from './language-selector';

export const InlineEditUI = ( {
	contentRef,
	settings,
	onClose,
	onApply,
	value,
	activeAttributes,
} ) => {
	const [ title, setTitle ] = useState( activeAttributes.title );
	const [ lang, setLang ] = useState( activeAttributes.lang );
	const [ languageSelector, setLanguageSelector ] = useState( false );
	const [ searchList, setSearchList ] = useState( [] );
	const [ hoveredIndex, setHoverIndex ] = useState( -1 );
	const [ loading, setLoading ] = useState( false );
	const [ focused, setFocused ] = useState( false );
	const [ langCodeAdjustment, setLangCodeAdjustment ] = useState( false );
	const inputRef = createRef();

	const anchorRef = useAnchorRef( {
		ref: contentRef,
		value,
		settings,
	} );

	useEffect( () => {
		setTitle( activeAttributes.title || getTextContent( slice( value ) ) );
		setLang( activeAttributes.lang || getSiteLanguage() );
	}, [ activeAttributes ] );

	useEffect( () => {
		if ( title ) {
			const term = title.trim();
			setLoading( true );
			prefixSearch( lang, term, ( prefixData ) => {
				if ( ! prefixData.length ) {
					fulltextSearch( lang, term, ( fulltextData ) => {
						setSearchList( fulltextData );
						setLoading( false );
					} );
				} else {
					setSearchList( prefixData );
					setLoading( false );
				}
				setHoverIndex( -1 );
			} );
		} else {
			abortAllRequest();
			setSearchList( [] );
			setLoading( false );
		}
	}, [ title, lang ] );

	useEffect( () => {
		if ( lang && lang.length > 3 ) {
			setLangCodeAdjustment( true );
		} else {
			setLangCodeAdjustment( false );
		}
	}, [ lang ] );

	return (
		<Popover
			anchorRef={ anchorRef }
			onClose={ onClose }
			position="bottom center"
			className="wikipediapreview-edit-inline"
			noArrow={ false }
			expandOnMobile={ true }
		>
			{ ! languageSelector ? (
				<div>
					<div className="wikipediapreview-edit-inline-search">
						<p className="wikipediapreview-edit-inline-search-label">
							<span>
								{ __(
									'Wikipedia Preview',
									'wikipedia-preview'
								) }
							</span>
							&nbsp;
							<span className="wikipediapreview-edit-inline-search-label-beta">
								{ __( 'beta', 'wikipedia-preview' ) }
							</span>
						</p>
						<TextControl
							className={ `wikipediapreview-edit-inline-search-input ${
								langCodeAdjustment ? 'lang-code-adjustment' : ''
							}` }
							ref={ inputRef }
							value={ title }
							onChange={ setTitle }
							onFocus={ () => setFocused( true ) }
							onBlur={ () => setFocused( false ) }
							placeholder={ __(
								'Search Wikipedia',
								'wikipedia-preview'
							) }
							autoFocus={ true } // eslint-disable-line jsx-a11y/no-autofocus
						/>
						<div className="wikipediapreview-edit-inline-search-icon" />
						<div className="wikipediapreview-edit-inline-search-tools">
							{ title && (
								<Button
									onClick={ () => {
										setTitle( '' );
										inputRef.current.focus();
									} }
									className="wikipediapreview-edit-inline-search-close"
								/>
							) }
							<div
								className={ `wikipediapreview-edit-inline-search-language 
							${ focused ? `focused` : '' }` }
								onClick={ () => setLanguageSelector( true ) }
								role="presentation"
							>
								<div
									className={ `wikipediapreview-edit-inline-search-language-code ${
										focused ? `focused` : ''
									}` }
								>
									{ lang }
								</div>
								<div className="wikipediapreview-edit-inline-search-language-dropdown"></div>
							</div>
						</div>
						{ loading && (
							<div className="wikipediapreview-edit-inline-search-loading"></div>
						) }
					</div>
					{ loading && ! searchList.length && (
						<div className="wikipediapreview-edit-inline-info">
							<bdi>
								{ __(
									'Loading search results…',
									'wikipedia-preview'
								) }
							</bdi>
						</div>
					) }
					{ ! loading && title && ! searchList.length && (
						<div className="wikipediapreview-edit-inline-info">
							<bdi>
								{ __(
									'No results found',
									'wikipedia-preview'
								) }
							</bdi>
						</div>
					) }
					{ searchList && searchList.length ? (
						<div className="wikipediapreview-edit-inline-list">
							{ searchList.map( ( item, index ) => {
								return (
									<div
										className={ `wikipediapreview-edit-inline-list-item ${
											index === hoveredIndex
												? 'hovered'
												: ''
										}` }
										key={ item.title }
										role="link"
										tabIndex={ index }
										onClick={ () => {
											onApply( value, item.title, lang );
										} }
										onKeyUp={ () => {
											onApply( value, item.title, lang );
										} }
									>
										<div
											className="wikipediapreview-edit-inline-list-item-img"
											style={
												item.thumbnail
													? {
															backgroundImage: `url(${ item.thumbnail })`,
													  }
													: {}
											}
										/>
										<span className="wikipediapreview-edit-inline-list-item-title">
											{ item.title }
										</span>
										<span className="wikipediapreview-edit-inline-list-item-description">
											{ item.description }
										</span>
									</div>
								);
							} ) }
						</div>
					) : null }
				</div>
			) : (
				<LanguageSelector
					setLanguageSelector={ setLanguageSelector }
					setLang={ setLang }
					lang={ lang }
				/>
			) }
			<KeyboardShortcuts
				bindGlobal={ true }
				shortcuts={ {
					down: () => {
						setHoverIndex(
							( hoveredIndex + 1 ) % searchList.length
						);
					},
					up: () => {
						setHoverIndex(
							hoveredIndex
								? hoveredIndex - 1
								: searchList.length - 1
						);
					},
					enter: () => {
						if ( hoveredIndex === -1 && ! languageSelector ) {
							const matchedItem = searchList.find(
								( list ) =>
									list.title.toLowerCase() ===
									title.toLowerCase().trim()
							);
							if ( matchedItem ) {
								onApply( value, matchedItem.title, lang );
							}
						} else if ( ! languageSelector ) {
							onApply(
								value,
								searchList[ hoveredIndex ].title,
								lang
							);
						}
					},
				} }
			/>
		</Popover>
	);
};
