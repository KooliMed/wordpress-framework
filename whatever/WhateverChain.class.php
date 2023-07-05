<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}
class WhateverChain extends WhateverInstaller #Chain class#
{
    public function install()
    {
        // Initialize Plugin Options
        $this->initOptions();
        // Initialize DB Tables used by the plugin
        $this->installDatabaseTables();
        // Other Plugin initialization
        $this->otherInstall();
        // Record the installed version
        $this->saveInstalledVersion();
        // To avoid running install() more then once
        $this->markAsInstalled();
    }


    /**
     * @return void
     */
    public function upgrade()
    {
    }

    /**
     * @return void
     */
    public function activate()
    {
    }

    /**
     * Define a constant if ever the plugin is desactivated. Will be used to clinch plugin status in other pages.
     * @return void
     */
    public function deactivate()
    {
        define('LET_PLUGIN_DISABLED', 'LET_PLUGIN_DISABLED');
    }

    /**
     * Initialize Plugin Options
     * @return void
     */
    protected function initOptions()
    {
    }

    public function addActionsAndFilters()
    {
    }

    /**
     * Initialize DB Tables used by the plugin
     * @return void
     */
    protected function installDatabaseTables()
    {
    }



    /**
     * Other Plugin initialization
     * @return void
     */
    protected function otherInstall()
    {
    }


    /**
     * Push the settings in the Plugins menu by default.
     * @return void
     */
    public function addMenuPages()
    {
        add_action('admin_menu', array(&$this, 'addSettingsMenuPage'));
       
    }

    protected function requireExtraPluginFiles()
    {
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    /**
     * @return string Slug name for the URL to the Settings page.
     */
    protected function getSettingsSlug()
    {
        return get_class($this) . 'Settings';
    }

    public function getPluginMenuName()
    {
        return trim($this->getPluginHeaderValueWithSpace('Main Menu'));
    }

    public function addSettingsMenuPage()
    {
        $this->requireExtraPluginFiles();
        $displayName = ucfirst($this->getPluginMenuName());
        add_menu_page(
            $displayName,
            $displayName,
            'manage_options',
            $this->getSettingsSlug(),
            array(&$this, 'settingsPage',
        ),'',25
        );

        // when it's magic, silence is golden
        $arrayAdminTemplatePages = $this->getFilesInFolder(__DIR__ . '/pages/');
        foreach ($arrayAdminTemplatePages as $TemplatePage) {
            $label = preg_replace('/(?<=[a-z])[A-Z]|[A-Z](?=[a-z])/', ' $0', $this->extractFileName($TemplatePage));
            $this->addTemplateMenuPage($label);
        }
        // eof magic
    }
}