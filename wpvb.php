<?php
/**
 * @package VbWP
 * @version 1.6
 */
/*
Plugin Name: VbWP 
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: ambikuk salawean
Version: 1.6
Author URI: http://ma.tt/
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


?>
