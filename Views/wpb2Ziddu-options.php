<?php
/**
 * This file contains the contents of the Ziddu admin options page.
 *
 
 */
 ?>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script>
/**
     * Display the Ziddu authorize url, hide the authorize button and then show the continue button.
     * @param url
     */
jQuery(document).ready(function ($) {
 $('#frequency').change(function() {
            var len = $('#day option').size();
            if ($('#frequency').val() == 'daily') {
                $('#day').append($("<option></option>").attr("value", "").text('<?php _e('Daily', 'wpbtz'); ?>'));
                $('#day option:last').attr('selected', 'selected');
                $('#day').attr('disabled', 'disabled');
            } else if (len == 8) {
                $('#day').removeAttr('disabled');
                $('#day option:last').remove();
            }
        });
});
    function ziddu_authorize(url) {
       
        document.getElementById('continue').style.visibility = 'visible'; 	       	 
  	document.getElementById('authorize').style.visibility = 'hidden';
        document.getElementById('loginDiv').style.visibility = 'visible'; 

    }
function hideDiv(){
var email =$('#userName').val();
var password = $('#password').val();
if(email!= "" && password !=""){
 $('#info').hide();
}
else{
 $('#info').show();
}
}
function login(){
var url='http://www.ziddu.com/wpb2z/wp-login.php';

var email =$('#userName').val();
var password = $('#password').val();
if(email!= "" && password !=""){

	url = url + "?email=" + email;
	url = url + "&password=" + password;
url = url + '&dtype=wp'

//url="http://www.ziddu.com/wpb2z/wp-login.php?email=aleem@ziddu.com&password=1234&dtype=wp";
$.ajax({
	type:'GET',
	url:url,
	dataType:'json',
	success: function(json){
	if(json.status=="Fail"){
		$('#error').val(json.msg);
	}
$('#uname').val(json.name);
$('#memid').val(json.memid);
$('#regist').submit();
	}
	});

}
else{
alert("Please Enter Ziddu ID and Password");
return false;
}
//console.log("hello");

}
</script>

<?php if(in_array('curl',get_loaded_extensions())){ ?>

 <div style="margin-top:15px"><img width="100px" 
                             src="<?php echo $uri ?>/image/ziddu_logo.png"
                             alt="Wordpress Backup to Ziddu Logo"></div>
<h2><?php _e('WordPress Backup to Ziddu v1.0', 'wpbtz'); ?></h2>

<?php

$config = WPB2Z_Factory::get('config');

if (array_key_exists('wpb2z_save_changes', $_POST)) {

if($_POST['frequency'] == "Daily"){
$day1 = '';
}
else{
$day1 = 'Mon';
}

$time1 = '00:00';
  WPB2Z_BackupController::execute();
                    $config->set_schedule($day1, $time1, $_POST['frequency']); 
      
    }
else if(isset($_POST["userName"])&&$_POST["memid"]!=""){
      
$email=$_POST["userName"];
$password=$_POST["password"];
$memid=$_POST["memid"];
$uname = $_POST["uname"];

$wpdb = WPB2Z_Factory::db();
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$table_name = $wpdb->prefix . 'wpb2z_login';
	$wpdb->insert( $table_name, array( 'name' => $email, 'password' => $password,'memid' => $memid,'uname' => $uname), array('%s','%s','%s','%s'));


}
else if(array_key_exists('unlink', $_POST)){
 $config->unlink();
}

$wpdb = WPB2Z_Factory::db();
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . 'wpb2z_login';
$myrows = $wpdb->get_results( "SELECT name,memid,uname FROM ". $table_name );
$memid1 ="text";
$name = "";
foreach ( $myrows as $id ) 
	{
$uname1 =$id->uname;
$name = $id->name;
$memid1 = $id->memid;

}

 if( $memid1!="text"||isset($_POST["userName"])&&$_POST["memid"]!=""||array_key_exists('wpb2z_save_changes', $_POST)){
echo "Welcome <b>".$uname1 . "!</b><br><br>";
?>
 <form id="backup_to_ziddu_options" name="backup_to_ziddu_options"
          action="admin.php?page=backup-to-ziddu" method="post">
 <input type="submit" id="unlink" name="unlink" class="button-primary" value="<?php _e('Unlink Account', 'wpbtz'); ?>">

</form>
<?php if (array_key_exists('wpb2z_save_changes', $_POST)) { ?>
<br>
<br>
<?php
$timestamp = wp_next_scheduled('run_ziddu_backup_hook');

echo "============================================<br><br>";
if ($timestamp) {
	echo "Your Wordpress backup is in process.<br>";
}
echo "<br><br>============================================";
}


  if ($timestamp) {
echo "<br><br>Your Next Scheduled backup is <b>";
$frequency = wp_get_schedule( 'run_ziddu_backup_hook' );
       


	$date_format=date("d M Y",$timestamp);
	print $date_format; echo "&nbsp,";
$time =  date('H:i A', $timestamp);
print $time;
print "</b>";
  	 
  }
	   
	   
?>
<form id="backup_to_ziddu_options" name="backup_to_ziddu_options"
          action="admin.php?page=backup-to-ziddu" method="post">
<?php $frequency = wp_get_schedule( 'run_ziddu_backup_hook' );
if($frequency==""){
$frequency= "Weekly";
}
 ?>
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row"><label for="frequency"><?php _e('Backup Frequency', 'wpbtz'); ?></label></th>
            <td>
                <select id="frequency" name="frequency">
                    <option value="Daily" <?php echo $frequency == 'Daily' ? ' selected="selected"' : "" ?>>
                        <?php _e('Daily', 'wpbtz') ?>
                    </option>
                    <option value="Weekly"  <?php echo $frequency == 'Weekly' ? ' selected="selected"' : "" ?>>
                        <?php _e('Weekly', 'wpbtz') ?>
                    </option>
                    <option value="Fortnightly" <?php echo $frequency == 'Fortnightly' ? ' selected="selected"'
                            : "" ?>>
                        <?php _e('Fortnightly', 'wpbtz') ?>
                    </option>
                    <option value="Monthly" <?php echo $frequency == 'Monthly' ? ' selected="selected"' : "" ?>>
                        <?php _e('Monthly', 'wpbtz') ?>
                    </option>
                   
                </select>
                <span class="description"><?php _e('How often the backup to Ziddu is to be performed.', 'wpbtz'); ?></span>
            </td>
        </tr>
        </tbody>
    </table>
    
	<p class="submit">
        <input type="submit" id="wpb2z_save_changes" name="wpb2z_save_changes" class="button-primary" value="<?php _e('Save Changes', 'wpbtz'); ?>">
    </p>
        <?php wp_nonce_field('backup_to_ziddu_options_save'); ?>
    </form>

<?php	}
	else{ ?>
	<h3><?php _e('Thank you for installing WordPress Backup to Ziddu!', 'wpbtz'); ?></h3>
    <p><?php _e('In order to use this plugin, you will need to authorize with your Ziddu account.', 'wpbtz'); ?></p>
    <div id="info"></div>
 <p>
<?php
if(isset($_POST["error"])){?>
	    <div  style="color:red"><?php echo $_POST["error"]; ?></div>
<?php } ?>
	<form id="regist" name="regist"  method="post" >
        <input type="button" name="authorize" id="authorize" value="<?php _e('Authorize', 'wpbtz'); ?>"
               class="button-primary" onclick="ziddu_authorize('xyz')"/><br/>
<div id="loginDiv" style="visibility: hidden;">
<table><tr><td>
	Ziddu ID  :</td><td> <input type="text" name="userName" id="userName" onblur="hideDiv();"/> </td></tr>
     <tr><td>  Password  :</td> <td> <input type="password" name="password" id="password" onblur="hideDiv();"/> </td>

<input type="hidden" name="memid" id="memid" />
<input type="hidden" name="uname" id="uname" />
<input type="hidden" name="error" id="error" />
<tr><td></td><td>
	<input  type="button" name="continue" id="continue" onclick="login();"
               class="button-primary" value="<?php _e('Login', 'wpbtz'); ?>"/>
</td></tr>
</table>
</div>
    </form>
    </p>
	<?php }	?>

<?php

} 
else{
echo "Curl is not install on server";
}?>

 