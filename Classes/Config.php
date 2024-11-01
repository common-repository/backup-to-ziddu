<?php
/**
 * A class with functions the perform a backup of WordPress
 *
  */
class WPB2Z_Config
{
   private
        $db,
        $options
        ;

    public function __construct()
    {
        $this->db = WPB2Z_Factory::db();
    }


    public function set_schedule($day, $time, $frequency)
    {

if($frequency=="Daily"){
$date1 =date("Y-m-d"); 
$date1 = date("Y-m-d", strtotime($date1 ." +1 day") );
}else if($frequency=="Weekly") {
$date1 = date("Y-m-d", strtotime($date1 ." +7 day") );
}else if($frequency=="Fortnightly"){
$date1 = date("Y-m-d", strtotime($date1 ." +15 day") );
}else if($frequency=="Monthly"){
$date1 = date("Y-m-d", strtotime($date1 ." +30 day") );
}
        $timestamp = wp_next_scheduled('run_ziddu_backup_hook');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'run_ziddu_backup_hook');
        }

       $server_time = strtotime($date1);
 	
	wp_schedule_event($server_time, $frequency, 'run_ziddu_backup_hook');

        return $this;
    }

	public function unlink(){
	
		$wpdb = WPB2Z_Factory::db();
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$table_name = $wpdb->prefix . 'wpb2z_login';
		 $wpdb->query("TRUNCATE TABLE ".$table_name);
		$timesch = wp_next_scheduled('run_ziddu_backup_hook');
		wp_unschedule_event($timesch, 'run_ziddu_backup_hook');
	}
}