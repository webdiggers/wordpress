<?php

/**
 * Post Renew Plugin
 *
 * @package     Post Renew
 * @author      webdiggers
 * @copyright   
 * @license     GPL-2.0+
 *
 * @postnrew
 * Plugin Name: Post Renew
 * Plugin URI:
 * Description: This plugin will help in renew your post date to current date automatically according to your timeframe.
 * Version:     0.0.1
 * Author:      webdiggers
 * Author URI:  https://github.com/webdiggers/wordpress
 * Text Domain: https://github.com/webdiggers/wordpress
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

// Plugin Defines
define( "WPS_FILE", __FILE__ );
define( "WPS_DIRECTORY", dirname(__FILE__) );
define( "WPS_TEXT_DOMAIN", dirname(__FILE__) );
define( "WPS_DIRECTORY_BASENAME", plugin_basename( WPS_FILE ) );
define( "WPS_DIRECTORY_PATH", plugin_dir_path( WPS_FILE ) );
define( "WPS_DIRECTORY_URL", plugins_url( null, WPS_FILE ) );

// Require the main class file
require_once( WPS_DIRECTORY . '/include/main-class.php' );
