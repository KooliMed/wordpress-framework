<?php
defined('WP_UNINSTALL_PLUGIN') or exit;
class uninstall 
{
    public function __construct()
    {
        $this->unInstallDatabaseTables();
        $this->otherUninstall();
    }

    /**
     * Override to add any additional actions to be done at uninstall time
     * See: http://plugin.michael-simpson.com/?page_id=33
     * @return void
     */
    protected function otherUninstall()
    {
    }

    /**
     * Drop plugin-created tables on uninstall.
     * Because we by default create a table with the same name as the plugin, this will just delete (if exists) that table with the same name as the plugin.
     * @return void
     */
    protected function unInstallDatabaseTables()
    {
        global $wpdb;
        $options_prefix = ucfirst(basename(dirname(__FILE__)));
        $plugin_options = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE '{$options_prefix}%'");
        foreach ($plugin_options as $option) {
            delete_option($option->option_name);
        }
        $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->base_prefix . basename(dirname(__FILE__)));
    }

}
new uninstall();
