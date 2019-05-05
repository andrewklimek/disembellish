<?php
/*
Plugin Name: Disembellish
Plugin URI:  https://github.com/andrewklimek/disembellish
Description: Disable various core embellishments you may not want (emoji, capital P, archive type in page title)
Version:     1.4.1
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
 * Remove <meta name="generator" content="WordPress {version}">
 */
// add_filter('get_the_generator_xhtml', '__return_empty_string');
// use the above filter if this messes up rss or other types.
remove_action( 'wp_head', 'wp_generator' );

/**
 * Remove big un-used css from front end added by WP 5 for the block editor
 */
function disable_gutenberg_block_css() {
	wp_dequeue_style( 'wp-block-library' );
}
add_action( 'wp_enqueue_scripts', 'disable_gutenberg_block_css', 999 );


/**
 * Replace "Powered by Wordpress" H1 on login page
 */
add_filter('login_headerurl', function(){ return home_url(); });
add_filter('login_headertitle', function(){ return get_bloginfo( 'name', 'display' ); });



/**
 * System emails sent from admin email & blog name rather than wordpress@ and WordPress
 */
add_filter('wp_mail_from', function($email){
	if( substr($email,0,10) === 'wordpress@')
		$email = get_option('admin_email');
	return $email;
});
add_filter('wp_mail_from_name', function($name){
	if($name === 'WordPress')
		$name = str_replace( '&#039;', "'", get_option('blogname') );
	return $name;
});


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
add_filter( 'emoji_svg_url', '__return_null' );


/**
 * Disable smilies
 */
//foreach ( array( 'the_content', 'the_excerpt', 'the_post_thumbnail_caption', 'comment_text' ) as $filter ) {
//	$priority = has_filter( $filter, 'convert_smilies' );
//	if ( $priority !== FALSE ) {
//		remove_filter( $filter, 'convert_smilies', $priority );
//	}
//}
// This is probably the better way to do it:
register_activation_hook( __FILE__, function(){ update_option( 'use_smilies', false ); } );
register_deactivation_hook( __FILE__, function(){ update_option( 'use_smilies', true ); } );

// Don't use role="navigation" on nav elements.  Moving this to my theme since it's actually something that would show up on an html validator
// add_filter( 'navigation_markup_template', function($template){ return str_replace( ' role="navigation"', '', $template ); });

/**
 * Remove resource types - Only saves a few bytes, a waste unless you're caching the page
 */
// add_filter( 'style_loader_tag', function( $tag ) { return str_replace( array( " type='text/css' media='all' /", "type='text/css' "), "", $tag ); } );
// add_filter( 'script_loader_tag', function( $tag ) { return str_replace( "type='text/javascript' ", "", $tag ); } );

/**
 * Disable auto <p> insertion
 */

//remove_filter( 'the_content', 'wpautop' );
//remove_filter( 'the_excerpt', 'wpautop' );


// remove wp-embed
function disable_embeds_code_init() {

// Remove the REST API endpoint.
remove_action( 'rest_api_init', 'wp_oembed_register_route' );

// Turn off oEmbed auto discovery.
add_filter( 'embed_oembed_discover', '__return_false' );

// Don't filter oEmbed results.
remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

// Remove oEmbed discovery links.
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

// Remove oEmbed-specific JavaScript from the front-end and back-end.
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
// add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );

// Remove all embeds rewrite rules.
// add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );

// Remove filter of the oEmbed result before any HTTP requests are made.
remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
}
add_action( 'init', 'disable_embeds_code_init', 9999 );
