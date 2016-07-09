<?php
/*
Plugin Name: Disembellish
Description: Disable various core embellishments you may not want (emoji, capital P, archive type in page title)
Version:     0.2
Author:      Andrew J Klimek
Author URI:  https://andrewklimek.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Disembellish is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by the Free 
Software Foundation, either version 2 of the License, or any later version.

Disembellish is distributed in the hope that it will be useful, but WITHOUT 
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A 
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
Disembellish. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


/**
 * Disable capital P
 */

foreach ( array( 'the_content', 'the_title', 'wp_title', 'comment_text' ) as $filter ) {
	$priority = has_filter( $filter, 'capital_P_dangit' );
	if ( $priority !== FALSE ) {
		remove_filter( $filter, 'capital_P_dangit', $priority );
	}
}

/**
 * Disable the emoji's
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

/**
 * Disable smilies
 */
foreach ( array( 'the_content', 'the_excerpt', 'the_post_thumbnail_caption', 'comment_text' ) as $filter ) {
	$priority = has_filter( $filter, 'convert_smilies' );
	if ( $priority !== FALSE ) {
		remove_filter( $filter, 'convert_smilies', $priority );
	}
}

add_filter( 'comment_text', 'convert_smilies',    20 );


/**
 * Disable auto <p> insertion
 */

//remove_filter( 'the_content', 'wpautop' );
//remove_filter( 'the_excerpt', 'wpautop' );


/**
 * Remove "Category:" or "Author:" or ETC from archive page titles
 */
function remove_type_from_archive_title( $title ){
	$pos = strpos( $title, ': ' );
	if ( $pos ) {
		$title = substr( $title, 2 + $pos );
	}
	return $title;
}
// add_filter( 'get_the_archive_title', 'remove_type_from_archive_title' );