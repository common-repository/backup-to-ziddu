=== WordPress Backup to Ziddu ===
Contributors: Ziddu.com
Donate link: http://www.ziddu.com
Tags: backup, ziddu, wordpress backup
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Keep your valuable WordPress website, its media and database backed up to Ziddu in minutes with this sleek, easy to use plugin.

== Description ==

[WordPress Backup to Ziddu](http://www.ziddu.com) has been created to give you peace of mind that your blog is backed up on a regular basis.

Just choose a day, time and how often you wish your backup to be performed and kick back and wait for your websites files
and a SQL dump of its database to be dropped in your Ziddu!

Checkout the website - http://www.ziddu.com

= Setup =

Once installed, the authorization process is easy -

1. When you first access the plugin's options page, it will ask you to authorize the plugin with Ziddu.

2. Please Fill the Credentials.

3. Finally, click continue to setup your backup.

= Minimum Requirements =

1. PHP 5.2.16 or higher with [cURL support](http://www.php.net/manual/en/curl.installation.php)

2. [A Ziddu account](http://www.ziddu.com/register.php)

Note: Version 1.0 of the plugin supports PHP 5.2.16 or higher and can be [downloaded here.](http://downloads.wordpress.org/plugin/backup-to-ziddu.zip)

= Errors and Warnings =

During the backup process the plugin may experience problems that will be raised as an error or a warning depending on
its severity.

If you get any errors/warnings, please contact admin@ziddu.com

== Installation ==

1. Upload the contents of `backup-to-ziddu.zip` to the `/wp-content/plugins/` directory or use WordPress' built-in plugin install tool
2. Once installed, you can access the plugins settings page under the new Backup menu
3. The first time you access the settings you will be prompted to authorize it with Ziddu

== Frequently Asked Questions ==

= How do I restore my website? =

For restoring the data and SQL dump you have to click on 'Backup Monitor' and check the Backup file list. From the list, click on the 'Ziddu link' of desired date and simply download the file and restore it.
In short, this is the feature! :-)

= Does the plugin backup the WordPress database? =

It sure does. Your database tables will be dumped to a SQL file that can be used to import into a database when you need to restore or move your website.

== Screenshots ==

1. Backup Settings: In order to use this plugin, you need to authorize with your Ziddu account.
2. Backup Settings: Submit your 'Ziddu ID' and 'Password'.
3. Backup Settings: Choose the Frequency i.e., How often the backup to Ziddu is to be performed.
4. Backup Monitor: By clicking 'Start Backup', you can take the backup of your site Once in a Day.
5. Backup Monitor: Here, the backup file list will be displayed. You can download the backup file by visiting the desired date's 'Ziddu Link'.

== Changelog ==

= 1.0 =
* Added functionality for wordpress backup to ziddu.com
* Tested with WordPress 3.9.1

== Upgrade Notice ==

* After every update make sure that you check that your settings are still correct and run a test backup.
