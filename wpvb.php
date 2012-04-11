<?php
/**
 * @package VbWP
 * @version 1.6
 */
/*
Plugin Name: VbWP 
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: Wordpress - vBulletin Single Sign On
Author: Ndang, Ivan
Version: 0.1
Author URI: http://technolyze.net/
*/

define('VB_COOKIE_PREFIX', 'bb');
define('VB_TIMENOW', time());
define('VB_COOKIETIMEOUT', 900);
/*
 * login
 */
//add_action( 'wp_authenticate_user', 'add_vb_login' );

/*
 * logout
 */
include_once(ABSPATH . 'wp-includes/pluggable.php'); 

var_dump($_SESSION);
var_dump($_COOKIE);exit;


add_action( 'wp_logout', 'add_vb_logout' );
function add_vb_logout() 
{
	$user = wp_get_current_user();
	global $wpdb;
	$prefix_length = strlen(VB_COOKIE_PREFIX);
	foreach ($_COOKIE AS $key => $val)
	{
		$index = strpos($key, VB_COOKIE_PREFIX);
		if ($index == 0 AND $index !== false)
		{
			setcookie($key, null);
		}
	}
	$updateVisit = $wpdb->update( 
		'user', 
		array( 
			'lastactivity' => VB_TIMENOW-VB_COOKIETIMEOUT,	// string
			'lastvisit' => VB_TIMENOW	// integer (number) 
		), 
		array( 'username' => $user->user_login ), 
		array( 
			'%d',	// value1
			'%d'	// value2
		), 
		array( '%s' ) 
	);
	$userId = $wpdb->get_row("SELECT * FROM user WHERE username = '".$user->user_login."'");
	$wpdb->query("DELETE FROM session WHERE userid = '".$userId->userid."' ");
}

add_action( 'wp_logout', 'add_vb_logout' );

add_action('wp_authenticate_user', 'wpbb_login');

add_action('register_post', 'wpbb_register_hint');
?>
