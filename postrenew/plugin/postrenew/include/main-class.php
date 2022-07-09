<?php

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

class Postrenew {

    public function __construct() {

        // Plugin uninstall hook
        register_uninstall_hook( WPS_FILE, array('Postrenew', 'plugin_uninstall') );

        // Plugin activation/deactivation hooks
        register_activation_hook( WPS_FILE, array($this, 'plugin_activate') );
        register_deactivation_hook( WPS_FILE, array($this, 'plugin_deactivate') );

        // Plugin Actions
        add_action( 'plugins_loaded', array($this, 'plugin_init') );
        add_action( 'wp_enqueue_scripts', array($this, 'plugin_enqueue_scripts') );
        add_action( 'admin_enqueue_scripts', array($this, 'plugin_enqueue_admin_scripts') );
        add_action( 'admin_menu', array($this, 'plugin_admin_menu_function') );

    }

    public static function plugin_uninstall() {
        delete_option( 'postrenew_settings' );
     }

    /**
     * Plugin activation function
     * called when the plugin is activated
     * @method plugin_activate
     */
    public function plugin_activate() {
        delete_option('postrenew_timeframe');
     }

    /**
     * Plugin deactivate function
     * is called during plugin deactivation
     * @method plugin_deactivate
     */
    public function plugin_deactivate() { }

    /**
     * Plugin init function
     * init the polugin textDomain
     * @method plugin_init
     */
    function plugin_init() {
        // before all load plugin text domain
        load_plugin_textDomain( WPS_TEXT_DOMAIN, false, dirname(WPS_DIRECTORY_BASENAME) . '/languages' );
    }

    function plugin_admin_menu_function() {

        //create main top-level menu with empty content
        add_menu_page( __('Post Renew', WPS_TEXT_DOMAIN), __('Post Renew', WPS_TEXT_DOMAIN), 'administrator', 'pr-general', null, 'dashicons-admin-generic', 4 );

        // create top level submenu page which point to main menu page
        add_submenu_page( 'pr-general', __('General', WPS_TEXT_DOMAIN), __('General', WPS_TEXT_DOMAIN), 'manage_options', 'pr-general', array($this, 'plugin_settings_page') );

        // add the support page
        add_submenu_page( 'pr-general', __('Plugin Support Page', WPS_TEXT_DOMAIN), __('Support', WPS_TEXT_DOMAIN), 'manage_options', 'pr-support', array($this, 'plugin_support_page') );

    	//call register settings function
    	// add_action( 'admin_init', array($this, 'plugin_register_settings') );

    }

    /**
     * Register the main Plugin Settings
     * @method plugin_register_settings
     */
    // function plugin_register_settings() {
    //     register_setting( 'wps-settings-group', 'example_option' );
    // 	register_setting( 'wps-settings-group', 'another_example_option' );
    // }

    /**
     * Enqueue the main Plugin admin scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_admin_scripts() {
        wp_register_style( 'wps-admin-style', WPS_DIRECTORY_URL . '/assets/dist/css/admin-style.css', array(), null );
        wp_register_script( 'wps-admin-script', WPS_DIRECTORY_URL . '/assets/dist/js/admin-script.min.js', array(), null, true );
        wp_enqueue_script('jquery');
        wp_enqueue_style('wps-admin-style');
        wp_enqueue_script('wps-admin-script');
    }

    /**
     * Enqueue the main Plugin user scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_scripts() {
        wp_register_style( 'wps-user-style', WPS_DIRECTORY_URL . '/assets/dist/css/user-style.css', array(), null );
        wp_register_script( 'wps-user-script', WPS_DIRECTORY_URL . '/assets/dist/js/user-script.min.js', array(), null, true );
        wp_enqueue_script('jquery');
        wp_enqueue_style('wps-user-style');
        wp_enqueue_script('wps-user-script');
    }

    /**
     * Plugin main settings page
     * @method plugin_settings_page
     */
    function plugin_settings_page() { 
        $timeframedata = get_option('postrenew_timeframe');
        
        if(isset($_POST['savedata']))
        {
            if($timeframedata != '')
            {
                update_option( 'postrenew_timeframe',array($_POST['postrenew_timeframe']));
                $timeframedata = get_option('postrenew_timeframe');
                echo '<div class="notice notice-success is-dismissible">
                <p>Timeframe updated successfully.</p>
            </div>';
            }
            else
            {
                add_option( 'postrenew_timeframe', array($_POST['postrenew_timeframe']));
                echo '<div class="notice notice-success is-dismissible">
                <p>Timeframe updated successfully.</p>
            </div>';
            }   
            
        }
        else if(isset($_POST['runupdate']))
        {
            global $wpdb;
            $args = array(
                'posts_per_page' => 5,
                'post_type' => 'post',
                'orderby' => 'ID',
                'order' => 'DESC',
                'date_query' => array(
                    'after' => date('Y-m-d', strtotime('-1 days')) 
                )
            ); 
            $posts = get_posts($args);
            //print_r($posts); die();
            foreach($posts as $post)
            {
                $newdate = date('Y-m-d H:i:s');
                $wpdb->query("UPDATE $wpdb->posts SET post_modified = '$newdate', post_modified_gmt = '$newdate'  WHERE ID = $post->ID" );
            }
            echo '<div class="notice notice-success is-dismissible">
            <p>Posts Updated successfully</p>
        </div>';
        }
        
        ?>
    
        <div class="wrap card">

            <h1><?php _e( 'Post Renew', WPS_TEXT_DOMAIN ); ?></h1>
            <form method="post">
            <table class="form-table">
                <tbody>
                <tr>
                        <th scope="row"><?php _e( 'Time Frame', WPS_TEXT_DOMAIN ); ?></th>
                        <td>
                            <select name="postrenew_timeframe">
                                <option value="24" <?php echo ($timeframedata != '' && $timeframedata[0] == '24')?'selected':''?>>24 <?php _e( 'Hours', WPS_TEXT_DOMAIN ); ?></option>
                                <option value="48" <?php echo ($timeframedata != '' && $timeframedata[0] == '48')?'selected':''?>>48 <?php _e( 'Hours', WPS_TEXT_DOMAIN ); ?></option>
                                <option value="72" <?php echo ($timeframedata != '' && $timeframedata[0] == '72')?'selected':''?>>72 <?php _e( 'Hours', WPS_TEXT_DOMAIN ); ?></option>
                                
                            </select>
                            <p class="description"><?php _e( 'Set Time to update post date after interval of time.', WPS_TEXT_DOMAIN ); ?></p>
                            <button class="button button-secondary" name="runupdate" id="wps-run">Update Posts Now</button>
                        </td>
                    </tr>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row"></th>
                        <td>
                        <button class="button button-primary" name="savedata" id="wps-save">Save</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
            </form>
        </div>

    <?php }

    /**
     * Plugin support page
     * in this page there are listed some useful debug informations
     * and a quick link to write a mail to the plugin author
     * @method plugin_support_page
     */
    function plugin_support_page() {

        global $wpdb, $wp_version;
        $plugin = get_plugin_data( WPS_FILE, true, true );
        $wptheme = wp_get_theme();
        $current_user = wp_get_current_user();

        // set the user full name for the support request
        $user_fullname = ($current_user->user_firstname || $current_user->user_lastname) ?
        	($current_user->user_lastname . ' ' . $current_user->user_firstname) : $current_user->display_name;    ?>

        <div class="wrap card">

			<!-- support page title -->
			<h1><?php _e( 'Post Renew Support', WPS_TEXT_DOMAIN ); ?></h1>

            <!-- support page description -->
			<p><?php _e( 'Found an issue? Feel Free to Report.', WPS_TEXT_DOMAIN ); ?></p>

			<div class="support-debug">

				<div class="plugin">

					<ul>
						<li class="support-plugin-version"><strong><?php _e($plugin['Name']); ?></strong> version: <?php _e($plugin['Version']); ?></li>
						<li class="support-credits"><?php _e( 'Plugin author:', WPS_TEXT_DOMAIN ); ?> <a href="<?php echo $plugin['AuthorURI']; ?>"><?php echo $plugin['AuthorName']; ?></a></li>
					</ul>

				</div>

				

			</div>

            <div class="support-action">
                
                    <a class="button button-primary" style="text-decoration: none" href="https://github.com/webdiggers/wordpress/issues">Report an issue</a>
                
            </div>

        </div>

    <?php }

}

new Postrenew;
