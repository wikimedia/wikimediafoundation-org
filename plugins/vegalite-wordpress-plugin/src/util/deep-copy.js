/**
 * Create a deep copy of an object, so that arrays and objects do not share
 * references between equivalent properties on the two objects.
 *
 * @param {object} obj An object to clone.
 * @returns {object} Cloned object.
 */
const deepCopy = ( obj ) => {
	// For arrays, recursively process array member items into a new array.
	if ( Array.isArray( obj ) ) {
		return obj.map( deepCopy );
	}

	// For objects, recursively process object properties.
	if ( typeof obj === 'object' ) {
		return Object.keys( obj ).reduce(
			( newObj, key ) => {
				newObj[ key ] = deepCopy( obj[ key ] );
				return newObj;
			},
			{}
		);
	}

	// Return non-object values directly.
	return obj;
};

export default deepCopy;
