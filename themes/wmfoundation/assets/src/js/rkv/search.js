(function($) {

	$( '#searchFilter' ).on( 'submit', function(e) {
		e.preventDefault();

		var post_types = [];
		post_types = $( '#searchFilter input:checkbox:checked' ).map( function() {
			return $(this).val();
		}).get();

		var keyword = $( '#keyword' ).val(),
			data = {
				s: keyword,
				post_type: post_types,
			};

		doSearch( data );
	})

	function doSearch( formData ) {
		var data = formData || {};

		updateUri( formData );

		data.action = 'ajax_search';

		$( '#search-results' ).css( { opacity: 0.5 } );

		$.ajax({
			type: 'POST',
			url: wmfoundation.ajax_url,
			data: data,
			success: function( result ) {
				var resultData = result.data || {};

				if ( resultData.posts_html ) {
					$( '#search-results' ).empty().append( resultData.posts_html );
				}

				if ( resultData.pagination ) {
					$( '#pagination' ).empty().append( resultData.pagination );
				}

				if ( resultData.pagination === '' ) {
					$( '#pagination' ).empty();
				}

				$( '#search-results' ).css( { opacity: 1 } );
			},

			error: function() {
				$( '#search-results' ).empty();
				$( '#pagination' ).empty();

				$( '#search-results' ).css( { opacity: 1 } );
			}
		})
	}

	function updateUri( data ) {
		var urlData = data || {},
			url = window.location,
			baseurl = url.protocol + '//' + url.host + '/',
			params = decodeURIComponent( $.param( urlData ) );

		window.history.pushState( '', '', baseurl + '?' + params );
	}
})(jQuery);