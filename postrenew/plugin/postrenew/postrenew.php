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
add_filter( 'cron_schedules', 'every_five_minutes' );
function every_five_minutes( $schedules ) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => esc_html__( 'Every Five Minutes' ), );
    return $schedules;
}

if ( !wp_next_scheduled( 'bl_cron_hook' ) ) {

    wp_schedule_event( time(), 'five_minutes', 'bl_cron_hook' );
}


add_action( 'bl_cron_hook', 'bl_cron_exec' );
function bl_cron_exec(){
    write_log();
}

function write_log ()  {
    $timeframe = get_option('postrenew_timeframe');
    global $wpdb;
    $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND post_date <= NOW() - INTERVAL ".$timeframe[0]."");

    foreach($posts as $post)
    {
        $newdate = date('Y-m-d H:i:s');
        $wpdb->query("UPDATE $wpdb->posts SET post_modified = '$newdate', post_modified_gmt = '$newdate', post_date = '$newdate', post_date_gmt = '$newdate' WHERE ID = $post->ID" );
    }
}