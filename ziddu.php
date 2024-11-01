<?php
/*
Plugin Name: Ziddu
Plugin URI: http://www.ziddu.com
Description: Keep your valuable WordPress website, its media and database backed up to ziddu in minutes with this sleek, easy to use plugin.
Version: 1
Author: <a href="http://www.ziddu.com/">Ziddu</a>
*/
require_once 'Classes/Factory.php';
require_once 'Classes/Config.php';
require_once 'Classes/Utils.php';
require_once 'Classes/BackupController.php';


//More cron shedules
add_filter('cron_schedules', 'backup_to_ziddu_cron_schedules');

//back up and upload  hook
add_action('run_ziddu_backup_hook', 'run_ziddu_backup');


//add action menu
add_action('admin_menu', 'backup_to_ziddu_admin_menu');

//Register database install
register_activation_hook(__FILE__, 'wpb2z_install');

//for the activate the plugin DB is created.
function wpb2z_install()
{
    $wpdb = WPB2Z_Factory::db();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = $wpdb->prefix . 'wpb2z_login';
    dbDelta("CREATE TABLE $table_name (
        name varchar(50) NOT NULL,
        password varchar(255) NOT NULL,
	uname varchar(255) NOT NULL,
	memid varchar(255) NOT NULL,
        UNIQUE KEY name (name)
    );");

    $table_name = $wpdb->prefix . 'wpb2z_upload_files';
    dbDelta("CREATE TABLE $table_name (
        file varchar(255) NOT NULL,
        uploadurl varchar(500),
	date date NOT NULL,
        time datetime NOT NULL,
	tag varchar(255) NOT NULL,
        UNIQUE KEY file (file)
    );");

}

/**
 * A wrapper function that adds an options page to setup Ziddu Backup
 * @return void
 */
function backup_to_ziddu_admin_menu()
{
    $imgUrl = rtrim(WP_PLUGIN_URL, '/') . '/backup-to-ziddu/image/favicon.png';

    $text = __('Ziddu', 'wpbtz');
    add_menu_page($text, $text, 'activate_plugins', 'backup-to-ziddu', 'backup_to_ziddu_admin_menu_contents',$imgUrl, '90');

    $text = __('Backup Settings', 'wpbtz');
    add_submenu_page('backup-to-ziddu', $text, $text, 'activate_plugins', 'backup-to-ziddu', 'backup_to_ziddu_admin_menu_contents');

    $text = __('Backup Monitor', 'wpbtz');
    add_submenu_page('backup-to-ziddu', $text, $text, 'activate_plugins', 'backup-to-ziddu-monitor', 'backup_to_ziddu_monitor');

     
   
}

/**
 * A wrapper function that includes the backup to Ziddu options page
 * @return void
 */
function backup_to_ziddu_admin_menu_contents()
{
    $uri = rtrim(WP_PLUGIN_URL, '/') . '/backup-to-ziddu';
        include 'Views/wpb2Ziddu-options.php';
 
}

/**
 * A wrapper function that includes the backup to Ziddu monitor page
 * @return void
 */
function backup_to_ziddu_monitor()
{
      $uri = rtrim(WP_PLUGIN_URL, '/') . '/backup-to-ziddu';
        include 'Views/wpb2Ziddu-monitor.php';
}


/**
 * Adds a set of custom intervals to the cron schedule list
 * @param  $schedules
 * @return array
 */
function backup_to_ziddu_cron_schedules($schedules)
{
    $new_schedules = array(
        'every_min' => array(
            'interval' => 60,
            'display' => 'WPB2Z - Monitor'
        ),
        'Daily' => array(
            'interval' => 86400,
            'display' => 'WPB2Z - Daily'
        ),
        'Weekly' => array(
            'interval' => 604800,
            'display' => 'WPB2Z - Weekly'
        ),
        'Fortnightly' => array(
            'interval' => 1209600,
            'display' => 'WPB2Z - Fortnightly'
        ),
        'Monthly' => array(
            'interval' => 2419200,
            'display' => 'WPB2Z - Monthly'
        ),
        'two_monthly' => array(
            'interval' => 4838400,
            'display' => 'WPB2Z - Once Every 8 weeks'
        ),
        'three_monthly' => array(
            'interval' => 7257600,
            'display' => 'WPB2Z - Once Every 12 weeks'
        ),
    );

    return array_merge($schedules, $new_schedules);
}

/**
 * @return void
 */


/**
 * @return void
 */
function run_ziddu_backup()
{
       
        WPB2Z_BackupController::execute();

}

?>
