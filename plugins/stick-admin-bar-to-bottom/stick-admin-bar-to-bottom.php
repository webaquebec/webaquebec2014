<?php
/*
Plugin Name: Stick Admin Bar To Bottom
Description: Annoyed by the new Admin Bar that is covering the top 28 pixels of your website, but you don't want it completely gone? This plugin sticks the Admin Bar to the bottom of your screen!
Author: Coen Jacobs
Version: 1.2
Author URI: http://coenjacobs.me
*/

// Prevent accidental update of this plugin as the Libéo team modified it
add_filter( 'http_request_args', 'sm_prevent_update_check', 10, 2 );
function sm_prevent_update_check( $r, $url ) {
    if ( ! preg_match( '#://api\.wordpress\.org/plugins/update-check/(?P<version>[0-9.]+)/#', $url, $matches ) )
            return $r; // Not a plugin update request. Bail immediately.
            
    switch ( $matches['version'] ) {
            case '1.0':
                    $plugins = unserialize( $r[ 'body' ][ 'plugins' ] );
                    break;
            case '1.1':
                    $plugins = json_decode( $r[ 'body' ][ 'plugins' ] , true);
                    break;
            default:
                    return $r;
                    break;
    }

    if($matches['version'] == '1.1'){
        unset( $plugins['plugins'][plugin_basename( __FILE__ )] );
        unset( $plugins['active'][ array_search( plugin_basename( __FILE__ ), $plugins['active']) ] );
    }
    else{
        unset( $plugins->plugins[plugin_basename( __FILE__ )] );
        unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
    }
    
    switch ( $matches['version'] ) {
            case '1.0':
                    $r[ 'body' ][ 'plugins' ] = serialize( $plugins );
                    break;
            case '1.1':
                    $r[ 'body' ][ 'plugins' ] = json_encode( $plugins );
                    break;
    }

    return $r;
}

function stick_admin_bar_to_bottom_css() {
    if(!is_admin()){
    	$version = get_bloginfo( 'version' );
    
    	if ( version_compare( $version, '3.3', '<' ) ) {
    		$css_file = 'wordpress-3-1.css';
    	} else {
    		$css_file = 'wordpress-3-3.css';
    	}
    	wp_enqueue_style( 'stick-admin-bar-to-bottom', plugins_url( 'css/' . $css_file, __FILE__ ) );
	}
}

add_action( 'admin_init', 'stick_admin_bar_to_bottom_css' );
add_action( 'wp_enqueue_scripts', 'stick_admin_bar_to_bottom_css' );

?>
