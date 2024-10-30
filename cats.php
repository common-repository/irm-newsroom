<?php

// error_reporting(E_ALL); 
// ini_set( 'display_errors','1');

$blog_header_file = "../../../wp-blog-header.php";
require( $blog_header_file );

if ( !defined('ABSPATH') ) {
	/** Set up WordPress environment */
	require_once( dirname( __FILE__ ) . '/wp-load.php' );
}

$args = array('hide_empty' => FALSE);
echo wp_list_categories($args);

?>