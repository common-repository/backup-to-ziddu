<?php
/**
 * This file contains the contents of the Ziddu admin monitor page.
 *
  */
?>
<style>
td{border:1px solid #CCC; font-size:10px; padding:5px;color:#000;} 
 table th { background:#838383; color:#fff; font-size:12px; padding:5px; }
table td { background:#f6ebeb; }
</style>
<?php

if (array_key_exists('start_backup', $_POST)) {
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
$wpdb = WPB2Z_Factory::db();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . 'wpb2z_login';
$myrows = $wpdb->get_results( "SELECT memid,password FROM ". $table_name );

foreach ($myrows as $id) {
	$memid = $id->memid;
        $password = $id->password;
}

if (isset($memid)){
$target_url = 'http://uploads.ziddu.com/app-uploadwp.php';


$tobehashed = 'Z@Wp1~'.$password.'~z#wP2';
$md5val = md5($tobehashed);

$post = array('dtype' => 'wp','memid' => $memid ,'uploadfile' => '@'.$destination,'key' =>$md5val );
$ch = curl_init($target_url);
//curl_setopt($ch, CURLOPT_URL,$target_url);
curl_setopt($ch, CURLOPT_POST,true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_response = curl_exec($ch);
$decoded = json_decode($curl_response);
curl_close ($ch);

$wpdb = WPB2Z_Factory::db();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . 'wpb2z_upload_files';
if($decoded->status=="Fail"){
	$wpdb->insert( $table_name, array( 'file' => $destination , 'uploadurl' =>$decoded->msg,'date' => date("Y-m-d"),'time' => date("Y-m-d  H:i:s"),'tag' =>'instant'), array('%s','%s','%s','%s'));
?><h5 style="color:red"><?php $decoded->msg ?> <h5> <?php
}
else{
	$wpdb->insert( $table_name, array( 'file' => $destination , 'uploadurl' =>$decoded->uploadedfilelink,'date' => date("Y-m-d  H:i:s"),'time' => date("Y-m-d  H:i:s"),'tag' =>'instant'), array('%s','%s','%s','%s'));
$started = "YES";
}
//echo "file".$my_file;

unlink($my_file);
unlink($destination);

?>
<?php
}else{
	unlink($my_file);
	unlink($destination);
	?>
<h5 style="color:red">Please Login For BACK-UP<h5>
<?php
} 
 
}

?>
<div style="margin-top:15px"><img width="100px" 
                             src="<?php echo $uri ?>/image/ziddu_logo.png"
                             alt="Wordpress Backup to Ziddu Logo"></div>
<h2><?php _e('WordPress Backup to Ziddu v1.0', 'wpbtd'); ?></h2>

        <?php if (isset($started)): ?>
            Back Up completed.
        <?php else: ?>
<?php
 $wpdb = WPB2Z_Factory::db();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . 'wpb2z_upload_files';
$myrows = $wpdb->get_results( "SELECT * FROM ". $table_name ." where tag='instant' and date ='". date("Y-m-d")."'");
//echo "SELECT * FROM ". $table_name ." where tag='instant' and date = ".date("Y-m-d")."";
//echo $myrows;
//echo  date("Y-m-d");

if(empty($myrows))
{ ?>
 <form id="backup_to_ziddu_options" nam45e="backup_to_ziddu_options" action="admin.php?page=backup-to-ziddu-monitor" method="post">
           <input type="submit" id="start_backup" name="start_backup" class="button-primary" value="<?php _e('Start Backup', 'wpbtd'); ?>">
</form>
<?php }
else{ ?>
 
<?php } ?>
         
        <?php endif; ?>

<?php
 $wpdb = WPB2Z_Factory::db();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . 'wpb2z_upload_files';
$myrows = $wpdb->get_results( "SELECT file, uploadurl,date,tag,time FROM ". $table_name ." order by date desc,uploadurl desc");

?>
<br>

<h1>Backup file list is as follows</h1>
<br>
<?php
if(empty($myrows))
{
     echo "There is no file uploaded to server";
}
else{ ?>
<table border=1 cellpadding="0" cellspacing="0" bordercolor=" #CCC">
<tr><th>Date & Time </th><th>Backup Link</th></tr>
<?php
}
foreach ($myrows as $id ) 
	{  ?>

<tr><td> 
<?php 
//echo $id->time." date and time <br>";
//$timestamp1 = strtotime($id->date);
//$date_format=date("d M Y",$timestamp1);
//	print $date_format; echo "&nbsp,";
//$timestamp = strtotime($id->time);
//$time =  date('H:i A', $timestamp);
$timestamp = strtotime($id->time);
$time =  date('dS M, Y H:i A', $timestamp);


print $time; 
 ?>

</td>
<?php if (strpos($id->uploadurl,'http') !== false) {?>
<td>
   <a href="<?php echo $id->uploadurl; ?>" target="_blank">Ziddu Link</a></td>
<?php } else{?>
 <td><?php echo $id->uploadurl; ?></td>
<?php } ?>
</tr>
<?php } ?>
</table>
