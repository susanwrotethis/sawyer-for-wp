<?php
/*
Plugin Name: Sawyer for WP
Plugin URI: https://github.com/susanwrotethis/sawyer-for-wp
GitHub Plugin URI: https://github.com/susanwrotethis/sawyer-for-wp
Description: The Sawyer app allows families to book children's activities offered by partners in select communiites. Sawyer for WP enables partners to embed their activity schedule in their WordPress websites. 
Version: 0.1
Author: Susan Walker
Author URI: https://susanwrotethis.com
License: GPL v2 or later
Text Domain: swt-sawyer
Domain Path: /lang/
*/

// Exit if loaded from outside of WP
if ( !defined( 'ABSPATH' ) ) exit;

// SCRIPT LOADING AND LANGUAGE SUPPORT SETUP BEGINS HERE /////////////////////////////////
if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ).'inc/admin.php' );
}

// Load plugin textdomain
function swt_sawyer_load_textdomain()
{
	load_plugin_textdomain( 'swt-sawyer', false, dirname( plugin_basename( __FILE__ ) ).'/lang/' );
}
add_action( 'plugins_loaded', 'swt_sawyer_load_textdomain' );

// SHORTCODE FUNCTION BEGINS HERE ////////////////////////////////////////////////////////
function swt_sawyer_shortcode( $atts, $content=null )
{
	$options = maybe_unserialize( get_option( 'swt_sawyer_options', array() ) );
	
	if ( !isset( $options['company'] ) || !$options['company'] ) {
		return 'The company slug from the Sawyer app needs to be added in Settings.';
	}

	return '<style>html,body{-webkit-overflow-scrolling : touch !important;overflow: auto !important;height: 100% !important;}.sawyer-scroll-wrapper{-webkit-overflow-scrolling: touch;overflow-y: scroll;}@media only screen and (max-width: 35.5em) {.sawyer_frame {width: 90vw}}</style><div id="scroll_wrapper" class="sawyer-scroll-wrapper"><iframe id="sawyer_frame" class="sawyer_frame" src="https://www.hisawyer.com/'.$options['company'].'/schedules" width="100%" height="100%" style="border: none;"></iframe></div><script src="https://www.hisawyer.com/assets/embed/embed-f7fa7b9984e050f6170e556b0424351ec28384e990b0e28b94dd9139383304c3.js"></script><script type="text/javascript">iFrameResize({tolerance:10, heightCalculationMethod:"bodyScroll", checkOrigin: false});</script>';
}
add_shortcode( 'sawyer-wp', 'swt_sawyer_shortcode' );