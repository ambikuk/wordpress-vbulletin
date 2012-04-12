<?php

define('WPVB_DB_HOST', 'localhost');
define('WPVB_DB_NAME', 'nurbaya');
define('WPVB_DB_USER', 'dermawan');
define('WPVB_DB_PASSWORD', 'bandoeng');

/**
 * @file
 * Wordpress vB module database functions.
 *
 * Forked from Drupal vB module, http://drupal.org/project/drupalvb
 */

/**
 * Add vB database connection and connect to remote system database.
 *
 * If remote system database connection is identical to Drupal's database
 * connection, we skip switching the connection and alter the prefix only.
 *
 * @see wpvb_db_disconnect(), wpvb_get()
 */
function wpvb_db_connect() {
	$wpdb = wpvb_db();
}

/**
 * Disconnect from remote system database.
 *
 * @see wpvb_db_connect(), wpvb_get()
 */
function wpvb_db_disconnect() {
}

/**
 * Helper function to recall Wordpress's default database table prefix.
 *
 * @see wpvb_set_default_prefix()
 */
function wpvb_get_default_db_prefix() {
}

/**
 * Helper function to reset Drupal's default database table prefix.
 *
 * @see wpvb_get_default_prefix()
 */
function wpvb_set_default_db_prefix() {
}

/**
 * Check if a configured remote system database connection is valid.
 *
 * @see wpvb_settings_system()
 */
function wpvb_db_is_valid() {
}

/**
 * Generic database callback for querying pre-defined data from remote system.
 *
 * @param string $op
 *   The get-operation to perform in the remote system.
 */
function wpvb_get($op) {
  $function = 'wpvb_get_'. $op;
  if (!function_exists($function)) {
    return FALSE;
  }
  $args = func_get_args();
  array_shift($args);
  $result = call_user_func_array($function, $args);
  return $result;
}

/**
 * Query remote system database.
 *
 * @see db_query()
 */
function wpvb_db_query($query) {
  $args = func_get_args();
  array_shift($args);
  $query = db_prefix_tables($query);
  if (isset($args[0]) and is_array($args[0])) { // 'All arguments in one array' syntax
    $args = $args[0];
  }
  _db_query_callback($args, TRUE);
  $query = preg_replace_callback(DB_QUERY_REGEXP, '_db_query_callback', $query);
  $result = _db_query($query);
  return $result;
}

/**
 * Query remote system database with range.
 *
  * @see db_query_range
 */
function wpvb_db_query_range($query) {
}

/**
 * Return the last insert id.
 *
 * Borrowed from Drupal 6.
 */
function wpvb_db_last_insert_id($table, $field) {
}

/**
 * Initialize wpvb's user mapping table upon installation.
 *
 * Note: We can't do this in a single query, because Drupal's and vB's tables
 * need not to be in the same database.
 *
 * @see wpvb.admin-pages.inc
 */
function _wpvb_init_user_map() {
	global $wpdb;
	$vpdb = wpvb_db();
  $users = $vbusers = array();
  // Fetch all users in Wordpress.
  $result = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users");
  foreach($result as $row) {
    $users[$row->user_login] = $row->ID;
  }
  // Fetch all users in vBulletin.
  $result = $vbdb->get_results("SELECT userid, username FROM $vbdb->user");
  // Insert all vB users who already exist in Drupal with corresponding username
  // into our mapping table.
	foreach($result as $vbrow) {
		if(isset($users[$vbrow->username])) {
			$wpdb->insert('vb_users', array(
				'wp_id' => $users[$vbrow->username],
				'vb_id' => $vbrow->userid
			), array(
				'%d',
				'%d'
			));
		}
	}
}

/**
 * @return wpdb 
 */
function wpvb_db() {
	static $vbdb;
	if(!isset($vbdb)) {
		$vbdb = new wpdb(WPVB_DB_USER, WPVB_DB_PASSWORD, WPVB_DB_NAME, WPVB_DB_HOST);
		$vbdb->set_prefix(get_option('wpvb_db_prefix', ''));
	}
	return $vbdb;
}

function request_uri() {

  if (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
  }
  else {
    if (isset($_SERVER['argv'])) {
      $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['argv'][0];
    }
    elseif (isset($_SERVER['QUERY_STRING'])) {
      $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
    }
    else {
      $uri = $_SERVER['SCRIPT_NAME'];
    }
  }
  // Prevent multiple slashes to avoid cross site requests via the FAPI.
  $uri = '/' . ltrim($uri, '/');

  return $uri;
}

function user_password($length = 10) {
  // This variable contains the list of allowable characters for the
  // password. Note that the number 0 and the letter 'O' have been
  // removed to avoid confusion between the two. The same is true
  // of 'I', 1, and 'l'.
  $allowable_characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

  // Zero-based count of characters in the allowable list:
  $len = strlen($allowable_characters) - 1;

  // Declare the password as a blank string.
  $pass = '';

  // Loop the number of times specified by $length.
  for ($i = 0; $i < $length; $i++) {

    // Each iteration, pick a random character from the
    // allowable string and append it to the password:
    $pass .= $allowable_characters[mt_rand(0, $len)];
  }

  return $pass;
}