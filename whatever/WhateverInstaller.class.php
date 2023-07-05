<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}
class WhateverInstaller extends WhateverOptions #Installer class#
{ 

    const optionInstalled = '_installed';
    const optionVersion = '_version';

    /**
     * @return bool indicating if the plugin is installed already
     */
    public function isInstalled() {
        return $this->getOption(self::optionInstalled) == true;
    }

    /**
     * Note in DB that the plugin is installed
     * @return null
     */
    protected function markAsInstalled() {
        return $this->updateOption(self::optionInstalled, true);
    }

    /**
     * Set a version string in the options.
     * @return null
     */
    protected function getVersionSaved() {
        return $this->getOption(self::optionVersion);
    }

    /**
     * Set a version string in the options.
     * @return null
     */
    protected function setVersionSaved($version) {
        return $this->updateOption(self::optionVersion, $version);
    }

    /**
     * @return string name of the main plugin file that has the header section with
     * "Plugin Name", "Version", "Description", "Text Domain", etc.
     */
    protected function getMainPluginFileName() {
        global $plugin_custom_name;
        return $this->getPluginDisplayName() . 'php';
    }

    /**
     * Read the string from the comment header of the main plugin file
     * @param string plugin header key
     * @return string if found, otherwise null, no space !important
     */
    public function getPluginHeaderValue($key) {
        $data = file_get_contents($this->getPluginDir() . DIRECTORY_SEPARATOR . $this->getMainPluginFileName());
        $match = array();
        preg_match('/' . $key . ':\s*(\S+)/', $data, $match);
        if (count($match) >= 1) {
            return $match[1];
        }
        return null;
    }

    /**
     * Read the string (allow space) from the comment header of the main plugin file
     * @param string plugin header key
     * @return string if found, otherwise null, space allowed
     */
    public function getPluginHeaderValueWithSpace($key)
    {
        $data = file_get_contents($this->getPluginDir() . DIRECTORY_SEPARATOR . $this->getMainPluginFileName());
        $match = array();
        preg_match('/' . $key . ':\s*(.+)/', $data, $match);
        if (count($match) >= 1) {
            return $match[1];
        }
        return null;
    }

    protected function getPluginDir() {
        return dirname(__FILE__);
    }

    public function getVersion() {
        return $this->getPluginHeaderValue('Version');
    }


    /**
     * Check if the version saved in the options is earlier than the one returned by getVersion().
     * true indicates that new version is installed and upgrade actions should be taken.
     * @return bool 
     */
    public function isInstalledCodeAnUpgrade() {
        return $this->isSavedVersionLessThan($this->getVersion());
    }

    /**
     * Used to see if the installed code is an earlier version than the input version
     * @param string
     * @return bool true if the saved version is earlier (by natural order) than the input version
     */
    public function isSavedVersionLessThan($aVersion) {
        return $this->isVersionLessThan($this->getVersionSaved(), $aVersion);
    }

    /**
     * Check if the installed code is the same or earlier than the input version.
     * @param string
     * @return bool true if the saved version is earlier (by natural order) than the input version
     */
    public function isSavedVersionLessThanEqual($aVersion) {
        return $this->isVersionLessThanEqual($this->getVersionSaved(), $aVersion);
    }

    /**
     * @param  string
     * @param  string
     * @return bool true if version_compare of V1 and V2 shows V1 as the same or earlier
     */
    public function isVersionLessThanEqual($version1, $version2) {
        return (version_compare($version1, $version2) <= 0);
    }

    /**
     * @param  string
     * @param  string
     * @return bool true if version_compare of V1 and V2 shows V1 as earlier
     */
    public function isVersionLessThan($version1, $version2) {
        return (version_compare($version1, $version2) < 0);
    }

    /**
     * Save installed version to options.
     * @return void
     */
    protected function saveInstalledVersion() {
        $this->setVersionSaved($this->getVersion());
    }
}