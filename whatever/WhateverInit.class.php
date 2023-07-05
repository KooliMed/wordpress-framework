<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}

class WhateverInit #init class#
{
    public function construct()
    {
        // Run initialization
        // Initialize i18n
        add_action('plugins_loadedi', array($this, '_i18n_init'));
    }

    // setter for the plugin name based on the file name, used for classes initialisation..
    public function plugin_custom_name_Set($plugin_custom_name)
    {
        $this->plugin_custom_name = $plugin_custom_name;
    }

    // getter for the plugin name
    public function plugin_custom_name()
    {
        return $this->plugin_custom_name;
    }

    /**
     * Check the PHP version and give a useful error message if the user's version is less than the required version
     * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
     * an error message on the Admin page
     */
    function _noticePhpVersionWrong()
    {
        global $minimalRequiredPhpVersion;
        echo '<div class="updated fade">' .
            __('Error: The plugin requires a newer version of PHP to be running.',  $this->plugin_custom_name()) .
            '<br/>' . __('Minimal version of PHP required: ', $this->plugin_custom_name()) . '<strong>' . $minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', $this->plugin_custom_name()) . '<strong>' . phpversion() . '</strong>' .
            '</div>';
    }

    function _PhpVersionCheck()
    {
        global $minimalRequiredPhpVersion;
        if (version_compare(phpversion(), $minimalRequiredPhpVersion) < 0) {
            add_action('admin_notices', '_noticePhpVersionWrong');
            return false;
        }
        return true;
    }

    /**
     * Initialize internationalization (i18n) for this plugin.
     * @return void
     */
    function _i18n_init()
    {
        $pluginDir = dirname(plugin_basename(__FILE__));
        load_plugin_textdomain($this->plugin_custom_name(), false, $pluginDir . '/languages/');
    }

    function Plugin_run_init($file)
    {

        // Order of these included files is important. Do not change it if you do not know what you are doing.
        require_once(ucfirst($this->plugin_custom_name()) . 'Functions.class.php');
        require_once(ucfirst($this->plugin_custom_name()) . 'AdminTemplate.php');
        require_once(ucfirst($this->plugin_custom_name()) . 'Options.class.php');
        require_once(ucfirst($this->plugin_custom_name()) . 'Installer.class.php');
        require_once(ucfirst($this->plugin_custom_name()) . 'Chain.class.php');
        require_once(ucfirst($this->plugin_custom_name()) . 'Plugin.class.php');
        // Get more logs for Installation debugging, put it in the correct place
      
        // Fetch and create a Class from the plugin dir name to avoid collision when using multiple copies of this framework to create multiple plugins in same wordpress
        $DynamicName = ucfirst($this->plugin_custom_name()) . 'Plugin';
        $PluginClass = new $DynamicName(); // notice that this is not class_alias($DynamicName, 'PluginClass');
        $aPlugin = new $PluginClass();

        // Install the plugin
        // NOTE: this file gets run each time you *activate* the plugin.
        // So in WP when you "install" the plugin, all that does it dump its files in the plugin-templates directory
        // but it does not call any of its code.
        // So here, the plugin tracks whether or not it has run its install operation, and we ensure it is run only once
        // on the first activation
        if (!$aPlugin->isInstalled()) {
            $aPlugin->install();
        } else {
            // Perform any version-upgrade activities prior to activation (e.g. database changes)
            $aPlugin->upgrade();
        }

        // Add callbacks to hooks
        $aPlugin->addActionsAndFilters();

        if (!$file) {
            $file = __FILE__;
        }
        // Register the Plugin Activation Hook
        register_activation_hook($file, array(&$aPlugin, 'activate'));

        // Register the Plugin Deactivation Hook
        register_deactivation_hook($file, array(&$aPlugin, 'deactivate'));
    }
}
