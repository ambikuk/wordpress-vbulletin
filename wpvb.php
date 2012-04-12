<?php
/**
 * @package VbWP
 * @version 1.6
 */
/*
Plugin Name: VbWP Beta
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: Wordpress - vBulletin Single Sign On
Author: Ndang, Ivan
Version: 0.1
Author URI: http://technolyze.net/
*/

define('VB_COOKIE_PREFIX', 'bb');
define('VB_TIMENOW', time());
define('VB_COOKIETIMEOUT', 900);

add_action('wp_logout', 'add_vb_logout');
add_action('wp_login', 'add_vb_login');
add_action('user_register','add_vb_add_user');
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
	$user = wp_get_current_user();
	wpvb_clear_cookies($user->user_id);
}

function add_vb_login(){
	global $wpdb;
	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login = '%s' ",$_POST['log']));
//	var_dump($user->ID);exit; 
	wpvb_set_login_cookies($user->ID);
}

function add_vb_add_user(){
	global $wpdb;
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users order by ID desc");
	
	$post = $_POST;
	
	array_push($post,$user->ID);
	
	wpvb_create_user($post);
	
	var_dump($_POST);exit;

}