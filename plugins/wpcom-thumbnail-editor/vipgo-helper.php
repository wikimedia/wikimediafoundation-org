<?php

add_filter( 'jetpack_photon_domain', function() {
    return home_url();
} );
