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
  $args = func_get_args();
  $count = array_pop($args);
  $from = array_pop($args);
  array_shift($args);

  $query = db_prefix_tables($query);
  if (isset($args[0]) and is_array($args[0])) { // 'All arguments in one array' syntax
    $args = $args[0];
  }
  _db_query_callback($args, TRUE);
  $query = preg_replace_callback(DB_QUERY_REGEXP, '_db_query_callback', $query);
  $query .= ' LIMIT '. (int)$count .' OFFSET '. (int)$from;
  $result = _db_query($query);
  return $result;
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
  $users = $vbusers = array();
  // Fetch all users in Drupal.
  $result = db_query("SELECT uid, name FROM {users}");
  while ($user = db_fetch_array($result)) {
    $users[$user['name']] = $user['uid'];
  }
  // Fetch all users in vBulletin.
  $result = wpvb_db_query("SELECT userid, username FROM {user}");
  // Insert all vB users who already exist in Drupal with corresponding username
  // into our mapping table.
  while ($vbuser = db_fetch_array($result)) {
    if (isset($users[$vbuser['username']])) {
      db_query("INSERT INTO {wpvb_users} (uid, userid) VALUES (%d, %d)", $users[$vbuser['username']], $vbuser['userid']);
    }
  }
}

/**
 * @return wpdb 
 */
function wpvb_db() {
	static $wpdb;
	if(!isset($wpdb)) {
		$wpdb = new wpdb(WPVB_DB_USER, WPVB_DB_PASSWORD, WPVB_DB_NAME, WPVB_DB_HOST);
		$wpdb->set_prefix(get_option('wpvb_db_prefix', ''));
	}
	return $wpdb;
}