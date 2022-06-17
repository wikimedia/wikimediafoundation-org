import { TextControl, KeyboardShortcuts } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { filterLanguages, defaultFilter } from './languages';

export const LanguageSelector = ( { setLanguageSelector, setLang, lang } ) => {
	const [ value, setValue ] = useState( '' );
	const [ items, setItems ] = useState( [] );
	const [ hoveredIndex, setHoverIndex ] = useState( -1 );

	const selectLanguage = ( languageCode ) => {
		setLang( languageCode );
		setLanguageSelector( false );
	};

	useEffect( () => {
		setItems( defaultFilter() );
	}, [] );

	return (
		<div className="wikipediapreview-edit-inline-language-selector">
			<div className="wikipediapreview-edit-inline-language-selector-header">
				<div>{ __( 'Languages', 'wikipedia-preview' ) }</div>
				<div
					className="wikipediapreview-edit-inline-language-selector-header-close"
					onClick={ () => setLanguageSelector( false ) }
					role="presentation"
				></div>
			</div>
			<TextControl
				className="wikipediapreview-edit-inline-language-selector-input"
				value={ value }
				onChange={ ( target ) => {
					setValue( target );
					setItems( filterLanguages( target, lang ) );
				} }
				placeholder={ __( 'Search languages', 'wikipedia-preview' ) }
				autoFocus={ true } // eslint-disable-line jsx-a11y/no-autofocus
			/>
			<div className="wikipediapreview-edit-inline-language-selector-search-icon" />
			{ ! value ? (
				<div className="wikipediapreview-edit-inline-language-selector-label">
					{ __( 'All languages', 'wikipedia-preview' ) }
				</div>
			) : null }
			<div className="wikipediapreview-edit-inline-language-selector-results">
				{ items.length ? (
					items.map( ( item, index ) => (
						<div
							className={ `wikipediapreview-edit-inline-language-selector-results-item ${
								index === hoveredIndex ? 'hovered' : ''
							}` }
							data-code={ item.code }
							onClick={ () => {
								selectLanguage( items[ index ].code );
							} }
							onMouseEnter={ () => setHoverIndex( -1 ) }
							role="presentation"
							key={ item.code }
						>
							{ item.name }
						</div>
					) )
				) : (
					<div className="wikipediapreview-edit-inline-language-selector-results-none">
						<bdi>
							{ __( 'No results found', 'wikipedia-preview' ) }
						</bdi>
					</div>
				) }
			</div>
			<KeyboardShortcuts
				shortcuts={ {
					down: () => {
						setHoverIndex( ( hoveredIndex + 1 ) % items.length );
					},
					up: () => {
						setHoverIndex(
							hoveredIndex ? hoveredIndex - 1 : items.length - 1
						);
					},
					enter: () => {
						selectLanguage( items[ hoveredIndex ].code );
					},
				} }
			/>
		</div>
	);
};
