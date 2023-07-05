<?php
if (!defined('ABSPATH')) {
    exit;
}
/*
   Plugin Name: Whatever
   Main Menu: Whatever Settings
   Plugin URI: https://www.koolimed.com
   Author: Kooli Med
   Description: No more need to spend days to create advanced plugins for wordpress. This framework, creates plugin for you with most needed features in seconds; a setting page, many admin pages, a database table with an admin page, a menu..etc All you have to do is renaming the folder. The fastest and unique framework with no collision when using multiple copies on the same wp.
   License: GPLv3
  */
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 

// Ensure no code runs if disabled.
if (defined('LET_PLUGIN_DISABLED')) {
    return;
}

// Change files names, traits and classes according to the plugin name, if needed.
if (basename(__FILE__, ".php") != basename(dirname(__FILE__))) {
    require_once('filescheck.php');
}

$minimalRequiredPhpVersion = '5.6';
// Get all files needed to run
require_once(ucfirst(basename(dirname(__FILE__))) . 'Init.class.php');

// init the main class
$DynamicName = ucfirst(basename(dirname(__FILE__))) . 'Init';
$InitClass = new $DynamicName();

// set the name of the plugin
$InitClass->plugin_custom_name_Set(basename(dirname(__FILE__)));

// Run the version check.
// If it is successful, continue with initialization for this plugin
if ($InitClass->_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    $InitClass->Plugin_run_init(__FILE__);
} else {
    WP()->die('You must upgrade your php version to use this plugin.');
}

return
    $comment = '
   /* 
   Display Name: ' . basename(dirname(__FILE__)) . ' (One word, no space allowed)
   Version: 1.0.1
   Text Domain: ' . basename(dirname(__FILE__)) . '
   */';
