import PropTypes from 'prop-types';
import { ReactNode, useState, useRef, useEffect } from 'react';

import { createBlock } from '@wordpress/blocks';
import { useSelect, useDispatch } from '@wordpress/data';

import InnerBlocksDisplaySingle from './inner-block-single-display';
import Navigation from './navigation';

/**
 * InnerBlockSlider component.
 *
 * @param {object} props - Component props.
 * @param {string} props.parentBlockId - Parent block clientId.
 * @param {Array} props.allowedBlocks - Allowed block types.
 * @param {string} props.currentBlock - Block which will be inserted by the inserter.
 * @param {Array}  props.template - Initial block template.
 * @param {number} props.slideLimit - Maximum allowed slides.
 * @returns {ReactNode} Component.
 */
const InnerBlockSlider = ( {
	parentBlockId,
	allowedBlocks,
	currentBlock,
	template,
	slideLimit,
} ) => {
	const innerBlockTemplate = template || [ [ currentBlock ] ];

	const slideBlocks = useSelect(
		( select ) => select( 'core/block-editor' ).getBlock( parentBlockId ).innerBlocks
	);

	const [ currentItemIndex, setCurrentItemIndex ] = useState( 0 );

	// Track state in a ref, to allow us to determine if slides are added or removed.
	const slideCount = useRef( slideBlocks.length );

	const { insertBlock } = useDispatch( 'core/block-editor' );

	/**
	 * Custom "Add Block" appender.
	 *
	 * @returns {void}
	 */
	const addSlide = () => {
		const created = createBlock( currentBlock );
		insertBlock( created, undefined, parentBlockId );
	};

	/**
	 * If a slide is added, switch to the new slide. If one is deleted, make sure we don't
	 * show a non-existent slide.
	 */
	useEffect( () => {
		if ( slideBlocks.length > slideCount.current ) {
			// Slide added
			setCurrentItemIndex( slideBlocks.length - 1 );
		} else if ( slideBlocks.length < slideCount.current ) {
			// Slide deleted
			if ( currentItemIndex + 1 > slideBlocks.length ) {
				setCurrentItemIndex( slideBlocks.length - 1 );
			}
		}

		// Update ref with new value..
		slideCount.current = slideBlocks.length;
	}, [ slideBlocks.length, currentItemIndex, slideCount ] );

	return (
		<div className="inner-block-slider">
			<InnerBlocksDisplaySingle
				allowedBlocks={ allowedBlocks }
				className="slides"
				currentItemIndex={ currentItemIndex }
				parentBlockId={ parentBlockId }
				template={ innerBlockTemplate }
			/>

			<Navigation
				addSlide={ addSlide }
				addSlideEnabled={ slideBlocks.length < slideLimit }
				currentPage={ currentItemIndex + 1 }
				nextEnabled={ currentItemIndex + 1 < slideBlocks.length }
				prevEnabled={ currentItemIndex + 1 > 1 }
				setCurrentPage={ ( page ) => setCurrentItemIndex( page - 1 ) }
				totalPages={ slideBlocks.length }
			/>
		</div>
	);
};

InnerBlockSlider.defaultProps = {
	slideLimit: 10,
	template: null,
};

InnerBlockSlider.propTypes = {
	parentBlockId: PropTypes.string.isRequired,
	allowedBlocks: PropTypes.arrayOf( PropTypes.string ),
	currentBlock: PropTypes.string.isRequired,
	template: PropTypes.array,
};

export { InnerBlockSlider };
