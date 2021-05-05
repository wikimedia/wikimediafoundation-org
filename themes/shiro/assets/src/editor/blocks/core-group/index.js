import { InspectorControls } from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';

import BorderRadiusSelector from './BorderRadiusSelector';

import './style.scss';

const withRadiusSelector = createHigherOrderComponent( GroupBlockEdit => {
	/**
	 * Insert the icon selector in the inspector controls for the button block.
	 */
	return function WithBorderRadiusSelector( props ) {
		const { name, attributes, setAttributes } = props;

		return (
			<>
				<GroupBlockEdit { ...props } />
				{ name === 'core/group' && (
					<InspectorControls>
						<BorderRadiusSelector
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					</InspectorControls>
				) }
			</>
		);
	};
} );

export const
	name = 'core/group',
	filters = [
		{
			hook: 'editor.BlockEdit',
			namespace: 'shiro/core-group',
			callback: withRadiusSelector,
		},
	];
