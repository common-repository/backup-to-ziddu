<?php
/**
 * A class with functions the perform a backup of WordPress
 *
 */
class WPB2Z_BackupController
{
    private $config;

    public static function construct()
    {
        return new self();
    }

    public function __construct($output = null)
    {
        $this->config = WPB2Z_Factory::get('config');
        }

  
     public static function execute()
    {
$source = realpath(get_theme_root());
$source = $source.DIRECTORY_SEPARATOR;



 $dump_location = $source;
	if (!is_writable($dump_location)) {
 		return false;
	  }

	$my_file =  $dump_location.'file'.time().'.sql';
//echo "File".$my_file;
	$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);

	fwrite($handle,"-- WordPress Backup to Ziddu SQL Dump\n");
	fwrite($handle,"-- Version " . BACKUP_TO_Ziddu_VERSION . "\n");
	fwrite($handle,"-- http://ziddu.com\n");
	fwrite($handle,"-- Generation Time: " . date("F j, Y", $blog_time) . " at " . date("H:i", $blog_time) . "\n\n");
	fwrite($handle,'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";' . "\n\n");

        //I got this out of the phpMyAdmin database dump to make sure charset is correct
	fwrite($handle,"/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
	fwrite($handle,"/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n");
	fwrite($handle,"/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n");
	fwrite($handle,"/*!40101 SET NAMES utf8 */;\n\n");

	fwrite($handle,"--\n-- Create and use the backed up database\n--\n\n");

//echo "<br>".DB_NAME;
	fwrite($handle,"CREATE DATABASE IF NOT EXISTS " . DB_NAME . ";\n");
	fwrite($handle,"USE " . DB_NAME . ";\n\n");

$wpdb = WPB2Z_Factory::db();


	  $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);

	        foreach ($tables as $t) {
	            $table = $t[0];
	    fwrite($handle,"--\n-- Table structure for table `$table`\n--\n\n");
            $table_create = $wpdb->get_row("SHOW CREATE TABLE $table", ARRAY_N);
		fwrite($handle,$table_create[1] . ";\n\n");
        $row_count = 0;
        $table_count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($table_count == 0) {
          fwrite($handle,"--\n-- Table `$table` is empty\n--\n\n");
         }
		else {
                         fwrite($handle,"--\n-- Dumping data for table `$table`\n--\n\n");
            
            for ($i = 0; $i < $table_count; $i = $i + 10) {

                $table_data = $wpdb->get_results("SELECT * FROM $table LIMIT " . 10 . " OFFSET $i", ARRAY_A);
                if ($table_data === false) {
                    throw new Exception($db_error . ' (ERROR_4)');
                }

                $fields = '`' . implode('`, `', array_keys($table_data[0])) . '`';
              fwrite($handle,"INSERT INTO `$table` ($fields) VALUES\n");

                $out = '';
                foreach ($table_data as $data) {
                    $data_out = '(';
                    foreach ($data as $value) {
                        $value = addslashes($value);
                        $value = str_replace("\n", "\\n", $value);
                        $value = str_replace("\r", "\\r", $value);
                        $data_out .= "'$value', ";
                    }
                    $out .= rtrim($data_out, ' ,') . "),\n";
                    $row_count++;
                }
              fwrite($handle,rtrim($out, ",\n") . ";\n\n");
               
           }
        }

  }

      	fclose($handle);




$destination = substr(plugin_dir_path( __FILE__ ),0,-6).'compressed'.time().'.zip';	
Utils::Zip($source,$destination);

//echo "Zip completed";

 $wpdb = WPB2Z_Factory::db();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . 'wpb2z_login';
$myrows = $wpdb->get_results( "SELECT memid,password FROM ". $table_name );


foreach ($myrows as $id) {
	$memid = $id->memid;
	$password =  $id->password;
}
//$memid =77;
	// upload to server
$target_url = 'http://uploads.ziddu.com/app-uploadwp.php';
$tobehashed = 'Z@Wp1~'.$password.'~z#wP2';
$md5val = md5($tobehashed);



//echo $target_url ;
//echo "<br>";
$post = array('dtype' => 'wp','memid' => $memid ,'uploadfile' => '@'.$destination ,'key' =>$md5val);
$ch = curl_init($target_url);
//curl_setopt($ch, CURLOPT_URL,$target_url);
curl_setopt($ch, CURLOPT_POST,true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_response = curl_exec($ch);

//echo "response".$curl_response;
$decoded = json_decode($curl_response);

//echo "<br>".$decoded->status;

curl_close ($ch);
//echo "<br>";
	$wpdb = WPB2Z_Factory::db();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . 'wpb2z_upload_files';

if($decoded->status=="Fail"){
$wpdb->insert( $table_name, array( 'file' => $destination , 'uploadurl' =>$decoded->msg,'date' => date("Y-m-d"),'time' => date("Y-m-d  H:i:s"),'tag' =>'schedule'), array('%s','%s','%s','%s'));
}
else{
$wpdb->insert( $table_name, array( 'file' => $destination , 'uploadurl' =>$decoded->uploadedfilelink,'time' => date("Y-m-d  H:i:s"),'date' => date("Y-m-d"),'tag' =>'schedule'), array('%s','%s','%s','%s'));
}


unlink($my_file);
unlink($destination);
    }
	
}