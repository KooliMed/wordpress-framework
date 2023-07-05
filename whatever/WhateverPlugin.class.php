<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}
class WhateverPlugin extends WhateverChain #plugin class#
{
    use WhateverFunctions; #Functions use#
    use WhateverAdminTemplate; #AdminTemplate use#

    public function __construct()
    {
        $this->plugin_custom_name = $this->getPluginDisplayName();
    }

    /**
     * @return array of option meta data.
     */
    public function getOptionMetaData()
    {
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'ATextInput' => array(__('Type some text', 'my-awesome-plugin')),
            'AmAwesome' => array(__('I like this framework', 'my-awesome-plugin'), 'false', 'true'),
            'CanDoSomething' => array(
                __('Select user role', 'my-awesome-plugin'),
                'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone'
            )
        );
    }

    //    protected function getOptionValueI18nString($optionValue) {
    //        $i18nValue = parent::getOptionValueI18nString($optionValue);
    //        return $i18nValue;
    //    }

    protected function initOptions()
    {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr) > 1) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }


    protected function getMainPluginFileName()
    {
        return basename(dirname(__FILE__)) . '.php';
    }

    /**
     * Called by install() to create any database tables if needed.
     * The databse requests (create, insert..) are located in Database.php
     * @return void
     */
    protected function installDatabaseTables()
    {
        global $plugin_custom_name;
        $table = $GLOBALS['wpdb']->prefix . $this->getPluginDisplayName();
        if ($GLOBALS['wpdb']->get_var("show tables like '" . $table . "'") != $table) {
            include_once 'Database.php';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            if ($x_table != null)
                dbDelta($x_table);
        }
    }

    /**
     * Perform actions when upgrading from version X to version Y
     * @return void
     */
    public function upgrade()
    {
    }

    public function namespace_costum_assets()
    {
        wp_enqueue_script('my-script', plugins_url('/assets/js/script.js', __FILE__));
        wp_enqueue_style('my-style', plugins_url('/assets/css/style.css', __FILE__));
    }

    public function addActionsAndFilters()
    {
        global $plugin_custom_name;
        // Add options administration page
        add_action('plugins_loaded', array(&$this, 'addMenuPages'));

        // Adding scripts & styles to all pages
        if (strpos(strtolower($_SERVER['REQUEST_URI']), strtolower($this->getPluginDisplayName())) !== false) {
            add_action('admin_init', array(&$this, 'namespace_costum_assets'));
        }

        // Add Actions & Filters

        if ($this->getSettingsSlug() == $this->getSettingsSlug()) {
            add_action("current_screen", array($this, 'km_add_help_tab'), 20);
        }

        // Register short codes
        // Register AJAX hooks

        // add Setting Link On Plugins Page
        add_filter('plugin_action_links_' . basename(dirname(__FILE__)) . '/' . strtolower(basename(dirname(__FILE__))) . '.php', array(&$this, 'addSettingLinkOnPluginsPage'));
    }
}

$DynamicName = ucfirst(basename(dirname(__FILE__))) . 'Plugin';
if (!class_exists(ucfirst(basename(dirname(__FILE__))) . 'PluginClass'))
    class_alias($DynamicName, ucfirst(basename(dirname(__FILE__))) . 'PluginClass');
