import React from 'react';

/**
 * Error boundary component from https://reactjs.org/docs/error-boundaries.html
 */
class ErrorBoundary extends React.Component {
	constructor( props ) {
		super( props );
		this.state = { hasError: false };
	}

	static getDerivedStateFromError( error ) {
		// Update state so the next render will show the fallback UI.
		return { hasError: true };
	}

	componentDidCatch( error, errorInfo ) {
		// eslint-disable-next-line
		console.error( error, errorInfo );
	}

	render() {
		if ( this.state.hasError ) {
			return (
				<p>An error was encountered rendering this component.</p>
			);
		}

		return this.props.children;
	}
}

export default ErrorBoundary;
