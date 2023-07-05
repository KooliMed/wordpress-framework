<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}
trait WhateverFunctions  #Functions trait#
{

    /**
     * Hides the left side menu
     */
    protected function hide_menu($classes)
    {
        return $classes . " folded";
    }
    
    function output()
    {
        return 'Functions';
    }

    protected function toggle_menu_display($menu1, $menu2)
    {
        if (is_admin()) {
            $my_current_screen = get_current_screen();
            if ($my_current_screen->base == $menu1) { 
                add_filter("admin_body_class", "hide_menu", 10, 1);
            }
            if ($my_current_screen->base == $menu2) { 
                add_filter('submenu_file', 'highlight_menu', 10, 0);
            }
        }
        return $my_current_screen;
    }


    /**
     * Adds a help tab on screen top, when page loads
     * @return void
     */
    public function km_add_help_tab()
    {
        // Add help tab if current screen is this plugin
        get_current_screen()->add_help_tab(array(
            "id"    => "km_help_tab",
            "title" => __("Help"),
            "content"   => "<p>" . __("
        <h3>Title</h3>
        <div class='justify'>
        ....
        </div>
                    ") . "</p>",
            'callback' => false,
            'priority' => 10,
        ));
    }


    /**
     * return a bar with notification message
     * @var type | success, error, info, warning
     * @return void
     */
    protected function alert($msg, $type)
    {
        return '<div id="kmmsg" class="notice notice-' . $type . ' is-dismissible" onclick="hidekmmsg()">
				<p><strong>' . $msg . '</strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
    }


    /** Returns all php files under a specefic folder
     * @return array
     */
    protected function getFilesInFolder($folderName)
    {
        $arrayOfFiles = new RegexIterator(new DirectoryIterator($folderName), "/\\.php\$/i");
        return $arrayOfFiles;
    }

    /** Returns file name without the .extention
     * @return string
     */
    protected function extractFileName($fileName)
    {
        return substr($fileName, 0, -4);
    }

    // Get current screen name
    protected  function km_get_current_screen()
    {
        return get_current_screen()->base;
    }

    /**
     * Checks whteher a submenu exists
     *
     * @param [type] $subMenuName
     * @return boolen
     */
    protected function submenuExists($subMenuName)
    {
        global $submenu;
        $main_menu = $this->getSettingsSlug();
        if (isset($submenu[$main_menu]) && in_array($this->getSettingsSlug() . $subMenuName, wp_list_pluck($submenu[$main_menu], 2))) {
            return true;
        } else {
            return false;
        }
    }

    public function addSettingLinkOnPluginsPage($links)
    {
        $url = esc_url(add_query_arg(
            'page',
            $this->getSettingsSlug(),
            get_admin_url() . 'admin.php'
        ));
        $settings_link = "<a href='$url'>" . __('Settings') . '</a>';
        array_push(
            $links,
            $settings_link
        );
        return $links;
    }
}

