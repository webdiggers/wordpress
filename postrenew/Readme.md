# wordpress/postrenew
post date update plugin
This will update your outdated posts with curent date automatically

How to install
- Copy postrenew to your plugins folder of your wordpress.
- Install plugin "wp-crontrol" to manage cron jobs and events
- Copy code blow to your active theme functions.php file
add_action( 'wp_enqueue_scripts', 'twentytwentytwo_styles' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';

add_action( 'wp_enqueue_scripts', 'twentytwentytwo_styles' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';

add_filter( 'cron_schedules', 'example_add_cron_interval' );
function example_add_cron_interval( $schedules ) { 
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => esc_html__( 'Every Five Minutes' ), );
    return $schedules;
}

if ( ! wp_next_scheduled( 'bl_cron_hook' ) ) {
    wp_schedule_event( time(), 'five_seconds', 'bl_cron_hook' );
}

add_action( 'bl_cron_hook', 'bl_cron_exec' );
function bl_cron_exec(){
	write_log(time());
	write_log('junaid TK');
}

function write_log ( $log )  {
	$timeframe = get_option('postrenew_timeframe');
	global $wpdb;
            $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND post_modified <= NOW() - INTERVAL ".$timeframe[0]."");
            
            foreach($posts as $post)
            {
                $newdate = date('Y-m-d H:i:s');
                $wpdb->query("UPDATE $wpdb->posts SET post_modified = '$newdate', post_modified_gmt = '$newdate'  WHERE ID = $post->ID" );
            }
}

- Create a new event in cron events from Tools > Cron Events
add hook point to call (bl_cron_hook)
