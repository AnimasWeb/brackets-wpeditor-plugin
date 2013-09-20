<?php
/**
 * Plugin Name: WordPress Theme Editor for Brackets
 * Plugin URI: https://github.com/AnimasWeb/brackets-wpeditor-plugin
 * Description: This works with the Brackets extension to allow editing theme files in Brackets.
 * Version: 1.0
 * Author: Leo Lutz
 * Author URI: http://animasweb.com
 * License: MIT
 */

add_action('admin_menu', 'register_wteb_page');
add_action('wp_ajax_wteb_delete_key', 'wteb_delete_key');
add_action('wp_ajax_wteb_verify_key', 'wteb_verify_key');
add_action('wp_ajax_nopriv_wteb_verify_key', 'wteb_verify_key');

function register_wteb_page() {
  add_submenu_page('themes.php', 'brackets', 'Brackets', 'manage_options', 'wteb_brackets', 'wteb_admin_page'); 
}

function wteb_admin_page() {
  include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wteb_admin_page.php';
}

function wteb_delete_key() {
  $name = trim($_POST['name']);

  $keys = wteb_get_keys();
  unset($keys[$name]);
  wteb_set_keys($keys);
  die('Key deleted');
}

function wteb_verify_key() {
  global $wpdb; // this is how you get access to the database
  $data = wteb_decrypt(true);
  $success = ($data !== false) ? 'true' : 'false';
  die("{\"success\":$success}"); // this is required to return a proper result
}

function wteb_decrypt($return = false) {
  require 'lib/aes.class.php';
  require 'lib/aesctr.class.php';
  
  $keys = wteb_get_keys();
  
  if(! array_key_exists('name', $_POST) || ! array_key_exists($_POST['name'], $keys)) {
    if($return) {
      return false;
    }
    die('Invalid key name');
  }
  $key = $keys[$_POST['name']];
  
  $data = json_decode(AesCtr::decrypt($_POST['data'], $key, 256));
  if($return && $data === null) {
    return false;
  }
  return $data;
}

function wteb_get_keys() {
  $keys = get_option('wteb_keys');
  if($keys == false) {
    $keys = array();
  }
  return $keys;
}

function wteb_set_keys($keys) {
  update_option('wteb_keys', $keys);
}

function wteb_rand_string($length) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

  $str = '';
  $size = strlen( $chars );
  for( $i = 0; $i < $length; $i++ ) {
    $str .= $chars[ rand( 0, $size - 1 ) ];
  }

  return $str;
}

// PROCESS FORM POSTS
function wteb_process_forms() {  
  if(!empty($_POST) && array_key_exists('form_action', $_POST)) {
    if($_POST['form_action'] == 'wteb_add_key') {
      $name = trim($_POST['name']);
      if(empty($name)) {
        return 'Must have a name';
      } else {
        $keys = wteb_get_keys();
        if(array_key_exists($name, $keys)) {
          return 'Key name already used';
        } else {
          $keys[$name] = wteb_rand_string('1024');
          wteb_set_keys($keys);
          return '';
        }
      }
    }
  }
  return '';
}
$wteb_error_message = wteb_process_forms();