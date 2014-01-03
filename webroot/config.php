<?php
/**
 * Config-file for Tahiti. Change settings here to affect installation.
 *
 */
 
/**
 * Set the error reporting.
 *
 */
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly


/**
 * Define Tahiti paths.
 *
 */
define('TAHITI_INSTALL_PATH', __DIR__ . '/..');
define('TAHITI_THEME_PATH', TAHITI_INSTALL_PATH . '/theme/render.php');

// Image paths
define('IMG_PATH', __DIR__ . '/img/');
define('CACHE_PATH', __DIR__ . '/cache/');

// Define the basedir for the gallery
define('GALLERY_PATH', __DIR__ . '/img/gallery');
define('GALLERY_BASEURL', 'gallery/');


/**
 * Include bootstrapping functions.
 *
 */
include(TAHITI_INSTALL_PATH . '/src/bootstrap.php');


/**
 * Start the session.
 *
 */
session_name(preg_replace('/[:\.\/-_]/', '', __DIR__));
session_start();


/**
 * Create the Tahiti variable.
 *
 */
$tahiti = array();


/**
 * Settings for the database.
 *
 */
//$tahiti['database']['dsn']          = ''; 
//$tahiti['database']['username']       = '';
//$tahiti['database']['password']       = '';
//$tahiti['database']['driver_options'] = ;


/**
 * Site wide settings.
 *
 */
$tahiti['charset']      = 'utf-8'; // Meta charset
$tahiti['lang']         = 'sv'; // Language
$tahiti['title_append'] = ' | Tahiti';


/**
 * Navbar
 *
 */
$tahiti['navbar'] = array(
  'class' => 'navbar',
  'items' => array(
    'home'         => array('text'=>'Home',      'url'=>'hello.php'),
    'source' => array('text'=>'Source',      'url'=>'source.php'),
    'other'     => array('text'=>'Other',     'url'=>'?p=3'),
  ),
  'callback_selected' => function($url) {
    if(basename($_SERVER['SCRIPT_FILENAME']) == $url) {
      return true;
    }
  }
);

//$tahiti['navbar'] = null; // Remove comment to disable navbar


/**
 * Theme related settings.
 *
 */
//$tahiti['stylesheet'] = 'css/style.css';
$tahiti['stylesheets'] = array('css/style.css');
$tahiti['favicon']     = 'favicon.ico';


/**
 * Settings for JavaScript.
 *
 */
$tahiti['modernizr'] = 'js/modernizr.js';
$tahiti['jquery']    = '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js';
//$tahiti['jquery'] = null; // To disable jQuery

$tahiti['javascript_include'] = array();
//$tahiti['javascript_include'] = array('js/main.js'); // To add extra javascript files


/**
 * Google analytics.
 *
 */
//$tahiti['google_analytics'] = ''; // Set key to use Google Analytics and remove comment