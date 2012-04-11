<?php
/**
 * @package VbWP
 * @version 1.6
 */
/*
Plugin Name: VbWP Beta
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: ambikuk salawean
Version: 1.6
Author URI: http://ma.tt/
*/

define('VB_COOKIE_PREFIX', 'bb');
define('VB_TIMENOW', time());
define('VB_COOKIETIMEOUT', 900);

add_action( 'wp_logout', 'add_vb_logout' );
add_action('wp_authenticate_user', 'add_vb_login');
add_action('register_post', 'wpbb_register_hint');
/*
 * login
 */
//add_action( 'wp_authenticate_user', 'add_vb_login' );

/*
 * logout
 */
include_once(ABSPATH . 'wp-includes/pluggable.php'); 
include_once ('wpvb.lib.php');
include_once ('wpvb.lib.inc.php');


add_action( 'wp_logout', 'add_vb_logout' );
function add_vb_logout() 
{
	wpvb_clear_cookies(1);
}

function add_vb_login(){
	wpvb_set_login_cookies(1);
}
