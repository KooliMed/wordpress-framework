<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}
trait WhateverAdminTemplate #AdminTemplate trait#
{
    /**
     * Creates administration page
     * @return void
     */

    protected function addTemplateMenuPage($subMenuName)
    {
        $this->subMenuNamex = $subMenuName;
        add_submenu_page($this->getSettingsSlug(), $subMenuName, $subMenuName, 'manage_options', $this->getSettingsSlug() . $subMenuName, function () {
            $this->OutputAdminTemplate($this->subMenuNamex);
        });
    }

    protected function OutputAdminTemplate($subMenuName)
    {
        echo '<div class="wrap">';
        $pageName = $this->km_get_current_screen();
        $pageName = explode($this->getSettingsSlug(), $pageName);
        $pageNameStripped = str_replace(["\r", "\n", "\t", " "], "", $pageName[1]);
        include_once('pages/' . $pageNameStripped . '.php');
        echo '</div>';
    }
}