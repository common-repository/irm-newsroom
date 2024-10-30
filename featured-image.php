<?php

// error_reporting(E_ALL); 
// ini_set( 'display_errors','1');

$blog_header_file = "../../../wp-blog-header.php";
require( $blog_header_file );

if(!isset($_GET['id'])) { die('Missing ID'); }
if(!is_numeric($_GET['id'])) { die('ID is Invalid'); }

$id = $_GET['id']; // set the post id

$get_post = get_post($id); // load the post

$src = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full', false );
echo $src[0]; // the url of featured image

return;

//echo the_post_thumbnail('full');

//echo wp_get_attachment_image_src( $attachment_id = $id, $size = 'full', $icon = false );

// load post attachments

// -1 shows all attachments = 1 shows just single

//echo "<br>\$get_post->ID ".$get_post->ID;
//echo "<br>\$id".$id;

$args = array(
	'post_type'   => 'attachment',
	'numberposts' => 1,
	'post_status' => 'any',
	'post_parent' => $get_post->ID,
	'exclude'     => get_post_thumbnail_id(),
);

$attachments = get_posts( $args );

if ( $attachments ) {
	foreach ( $attachments as $attachment ) {
		echo apply_filters( 'the_title', $attachment->post_title );
		//the_attachment_link( $attachment->ID, false );
		the_attachment_link( $attachment->ID, true );
	}
}


?>