/**
 * Convert an array to a dictionary object keyed by a specified object property.
 *
 * @example
 *     const arr = [ { value: 'a' }, { value: 'b' } ];
 *     console.log( toDictionary( arr, 'value' ) );
 *     {
 *         a: { value: 'a' },
 *         b: { value: 'b' },
 *     }
 * @param {object[]} arr      Array to convert.
 * @param {string}   keyField Field on each object to use as dictionary keys.
 * @returns {object} Dictionary of objects in arr, keyed by [field].
 */
const toDictionary = ( arr, keyField ) => arr.reduce(
	( memo, obj ) => ( {
		...memo,
		[ obj[keyField] ]: obj,
	} ),
	{}
);

export default toDictionary;
