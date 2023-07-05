<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}

class WhateverOptions #Options class#
{

    public function introMessage()
    {
        //display a message on the main plugin page
        return array('Plugin intro message..', 'warning'); // array('message','type success|warning|error|info')
    }
    public function getOptionNamePrefix()
    {
        return $this->plugin_custom_name . '_';
    }

    /**
     * Define your options meta data here as array with elements in the format key=>display-name and/or key=>array(display-name, choice1...)
     */
    public function getOptionMetaData()
    {
        return array();
    }

    /**
     * @return array of string name of options
     */
    public function getOptionNames()
    {
        return array_keys($this->getOptionMetaData());
    }

    /**
     * Override this method to initialize options to default values before it will be saved to the database in Plugin.class by initOptions() function with add_option 
     * @return void
     */
    protected function initOptions()
    {
    }


    /**
     * @return string display name of the plugin
     */
    public function getPluginDisplayName()
    {
        return ucfirst(basename(dirname(__FILE__)));
    }

    /**
     * Get the prefixed version input $name suitable for storing in WP options
     * @param  string Defined in settings.php
     * @return string
     */
    public function prefix($name)
    {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) { // 0 but not false
            return $name; // already prefixed
        }
        return $optionNamePrefix . $name;
    }

    /**
     * Remove the prefix from the input $name.
     * @param  $name string
     * @return string $optionName without the prefix.
     */
    public function &unPrefix($name)
    {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) {
            return substr($name, strlen($optionNamePrefix));
        }
        return $name;
    }

    /**
     * A wrapper function to get_option() it prefixes the input avoiding name conflicts
     * @param $optionName string
     * @param $default string 
     * @return string
     */
    public function getOption($optionName, $default = null)
    {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        $retVal = get_option($prefixedOptionName);
        if (!$retVal && $default) {
            $retVal = $default;
        }
        return $retVal;
    }

    /**
     * A wrapper function to delete_option() it prefixes the input avoiding name conflicts
     * @param  $optionName string
     * @return bool from delegated call to delete_option()
     */
    public function deleteOption($optionName)
    {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return delete_option($prefixedOptionName);
    }

    /**
     * A wrapper function to add_option() it prefixes the input avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value
     * @return null from delegated call to delete_option()
     */
    public function addOption($optionName, $value)
    {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return add_option($prefixedOptionName, $value);
    }

    /**
     * A wrapper function to update_option() it prefixes the input avoiding name conflicts
     * @param  $optionName string
     * @param  $value mixed
     * @return null from delegated call to delete_option()
     */
    public function updateOption($optionName, $value)
    {
        $value = $this->filterValue($optionName, $value); // filter the value before recording
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return update_option($prefixedOptionName, $value);
    }

    /**
     * @param  string
     * @return string role name
     */
    public function getRoleOption($optionName)
    {
        $roleAllowed = $this->getOption($optionName);
        if (!$roleAllowed || $roleAllowed == '') {
            $roleAllowed = 'Administrator';
        }
        return $roleAllowed;
    }

    /**
     * @param  $roleName
     * @return string a WP capability or '' if unknown input role
     */
    protected function roleToCapability($roleName)
    {
        switch ($roleName) {
            case 'Super Admin':
                return 'manage_options';
            case 'Administrator':
                return 'manage_options';
            case 'Editor':
                return 'publish_pages';
            case 'Author':
                return 'publish_posts';
            case 'Contributor':
                return 'edit_posts';
            case 'Subscriber':
                return 'read';
            case 'Anyone':
                return 'read';
        }
        return '';
    }

    /**
     * @param $roleName string a standard WP role name like 'Administrator'
     * @return bool
     */
    public function isUserRoleEqualOrBetterThan($roleName)
    {
        if ('Anyone' == $roleName) {
            return true;
        }
        $capability = $this->roleToCapability($roleName);
        return current_user_can($capability);
    }

    /**
     * @param string
     * @return bool indicates if the user has adequate permissions
     */
    public function canUserDoRoleOption($optionName)
    {
        $roleAllowed = $this->getRoleOption($optionName);
        if ('Anyone' == $roleAllowed) {
            return true;
        }
        return $this->isUserRoleEqualOrBetterThan($roleAllowed);
    }

    /**
     * @return void
     */
    public function createSettingsMenu()
    {
        global $plugin_custom_name;
        //create new top-level menu
        add_menu_page(
            $this->getPluginDisplayName() . ' Plugin Settings',
            $this->getPluginDisplayName(),
            'administrator',
            get_class($this),
            array(&$this, 'settingsPage')
            /*,plugins_url('/images/icon.png', __FILE__)*/
        ); // if you call 'plugins_url; be sure to "require_once" it

        //call register settings function
        add_action('admin_init', array(&$this, 'registerSettings'));
    }

    public function registerSettings()
    {
        $settingsGroup = get_class($this) . '-settings-group';
        $optionMetaData = $this->getOptionMetaData();
        foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {

            register_setting($settingsGroup, $aOptionMeta);
        }
    }

    /**
     * Creates options page.
     * @return void
     */

    public function settingsPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', $this->plugin_custom_name));
        }


        $optionMetaData = $this->getOptionMetaData();

        // Save Posted Options
        if ($optionMetaData != null) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if (isset($_POST[$aOptionKey])) {
                    $this->updateOption($aOptionKey, $_POST[$aOptionKey]);
                }
            }
        }

        // HTML for the page
        $settingsGroup = get_class($this) . '-settings-group';
        if (!empty($this->introMessage()[0])) {
            echo $this->alert($this->introMessage()[0], $this->introMessage()[1]);
        }
?>
        <div class="wrap">

            <h2><?php echo $this->getPluginDisplayName() . ' ';
                _e('Settings', $this->plugin_custom_name); ?></h2>

            <hr>
            <div style="height:50px"></div>
            <form method="post" action="">
                <?php settings_fields($settingsGroup); ?>
                <table class="plugin-options-table">
                    <tbody>
                        <?php
                        if ($optionMetaData != null) {
                            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                                $displayText = is_array($aOptionMeta) ? $aOptionMeta[0] : $aOptionMeta;
                        ?>
                                <tr valign="top">
                                    <th scope="row">
                                        <p><label for="<?php echo $aOptionKey ?>"><?php echo $displayText ?></label></p>
                                    </th>
                                    <td>
                                        <?php $this->createFormControl($aOptionKey, $aOptionMeta, $this->getOption($aOptionKey)); ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes', $this->plugin_custom_name) ?>" />
                </p>
            </form>
        </div>
        <?php

    }

    /**
     * Helper-function outputs the correct form element (input tag, select tag) for the given item
     * @param  string name of the option (un-prefixed)
     * @param  mixed meta-data for $aOptionKey (string display-name or array(display-name, option1, option2, ...)
     * @param  string current value for $aOptionKey
     * @return void
     */
    protected function createFormControl($aOptionKey, $aOptionMeta, $savedOptionValue)
    {
        if (is_array($aOptionMeta) && count($aOptionMeta) >= 2) { // Drop-down list
            $choices = array_slice($aOptionMeta, 1);
        ?>
            <p><select name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>">
                    <?php
                    foreach ($choices as $aChoice) {
                        $selected = ($aChoice == $savedOptionValue) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $aChoice ?>" <?php echo $selected ?>><?php echo esc_attr($this->getOptionValueI18nString($aChoice)) ?></option>
                    <?php
                    }
                    ?>
                </select></p>
        <?php

        } else { // Simple input field
        ?>
            <p><input type="text" name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>" value="<?php echo esc_attr($savedOptionValue) ?>" size="50" /></p>
<?php

        }
    }

    // Add filters protection for any input fields name
    protected function filterValue($optionName, $optionValue)
    {
        if ($optionName == 'ATextInput') {
            $optionValue = htmlspecialchars(str_replace(' ', '%20', $optionValue));
        }
        // eof added protection
        return $optionValue;
    }

    /**
     * Provide i18n display strings for the values of options while still keeping the value of that option that is actually saved in the DB.
     * Define option values in getOptionMetaData() as canonical names (what you want them to literally be, like 'true') 
     * and then add each one to the switch statement in this function, returning the "__()" i18n name of that string.
     * @param string
     * @return string
     */
    protected function getOptionValueI18nString($optionValue)
    {

        switch ($optionValue) {
            case 'true':
                return __('true', $this->plugin_custom_name);
            case 'false':
                return __('false', $this->plugin_custom_name);

            case 'Administrator':
                return __('Administrator', $this->plugin_custom_name);
            case 'Editor':
                return __('Editor', $this->plugin_custom_name);
            case 'Author':
                return __('Author', $this->plugin_custom_name);
            case 'Contributor':
                return __('Contributor', $this->plugin_custom_name);
            case 'Subscriber':
                return __('Subscriber', $this->plugin_custom_name);
            case 'Anyone':
                return __('Anyone', $this->plugin_custom_name);
        }
        return $optionValue;
    }

    /**
     * Query MySQL DB for its version
     * @return string|false
     */
    protected function getMySqlVersion()
    {
        global $wpdb;
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
            return $rows[0]->mysqlversion;
        }
        return false;
    }
}
