/**
 * Generate a string with enough randomness that it can be reasonably assumed
 * to be unique on the page.
 *
 * @returns {string} Unique-ish ID string.
 */
const sufficientlyUniqueId = () => `_${ Math.random().toString( 36 ).substring( 2, 18 ) }`;

export default sufficientlyUniqueId;
