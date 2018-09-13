<?php
// Disable the Stats Jetpack Module
add_filter( 'jetpack_active_modules', 'vipgo_override_jp_modules', 99, 9 );
 
function vipgo_override_jp_modules( $modules ) {
    $disabled_modules = array(
        'stats',
    );
     
    foreach ( $disabled_modules as $module_slug ) {
        $found = array_search( $module_slug, $modules, true );
        if ( false !== $found ) {
            unset( $modules[ $found ] );
        }
    }
    return $modules;
}
// Remove Stats from the list of Jetpack modules
add_filter( 'jetpack_get_available_modules', 'vipgo_jetpack_hide_stats_module' );
function vipgo_jetpack_hide_stats_module( $modules ) {
    if( isset( $modules['stats'] ) ) {
        unset( $modules['stats'] );
    }
    return $modules;
}