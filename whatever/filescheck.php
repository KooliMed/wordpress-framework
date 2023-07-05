<?php
if (!defined('ABSPATH')) {
    exit;
}
global $plugin_dir;
$plugin_dir = basename(__dir__);
$ffs = scandir(__dir__);

unset($ffs[array_search('.', $ffs, true)]);
unset($ffs[array_search('..', $ffs, true)]);
// prevent empty ordered elements
if (count($ffs) < 1)
    return;

foreach ($ffs as $ff) {
    if (strpos($ff, '.php') !== false && strpos($ff, 'filescheck.php') === false) {
        if (file_exists(__DIR__ . '/' . $ff)) {
            $lines = file(__DIR__ . '/' . $ff);
            foreach ($lines as $line) {
                if (strpos($line, 'Plugin Name: ') !== false) {
                    $pClass = explode('Plugin Name: ', $line);
                    $oldFilesClass = ucfirst(strtolower(trim($pClass[1])));
                    replaceInFile($oldFilesClass, __DIR__ . '/' . $ff);
                    rename(__DIR__ . '/' . $ff, __DIR__ . '/' . strtolower($plugin_dir . '.php'));
                    break;
                }
            }
        }
    }
}

function replaceInFile($oldFilesClass, $file)
{
    global $plugin_dir;
    $what = array('Main Menu: ' . $oldFilesClass,'Plugin Name: ' . $oldFilesClass, 'trait ' . $oldFilesClass . 'AdminTemplate ', 'class ' . $oldFilesClass . 'Chain extends ' . $oldFilesClass . 'Installer ', 'trait ' . $oldFilesClass . 'Functions ', 'class ' . $oldFilesClass . 'Installer extends ' . $oldFilesClass . 'Options ', 'class ' . $oldFilesClass . 'Init ', 'class ' . $oldFilesClass . 'Plugin extends ' . $oldFilesClass . 'Chain ', 'use ' . $oldFilesClass . 'Functions;', 'use ' . $oldFilesClass . 'AdminTemplate;', 'class ' . $oldFilesClass . 'Options ', 'class ' . $oldFilesClass . 'DataBasePage extends ' . $oldFilesClass . 'PluginClass ');
    $buffer = "";
    $lines = file($file);
    foreach ($lines as $line) {
        foreach ($what as $hit) {
            $with = str_replace($oldFilesClass, ucfirst($plugin_dir), $hit);
            if (strpos($line, $hit) !== false) {
                $line = str_replace($hit, $with, $line);
            }
        }
        $buffer .= $line;
    }
    file_put_contents($file, $buffer);
}

function changeFilesClasses($dir, $oldFilesClass)
{
    global $plugin_dir;
    $files_array = array($oldFilesClass . 'AdminTemplate.php', $oldFilesClass . 'Chain.class.php', $oldFilesClass . 'Functions.class.php', $oldFilesClass . 'Init.class.php', $oldFilesClass . 'Installer.class.php', $oldFilesClass . 'Options.class.php', $oldFilesClass . 'Plugin.class.php', $oldFilesClass . 'Database.php');
    $ffs = scandir($dir);

    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);

    // prevent empty ordered elements
    if (count($ffs) < 1)
        return;

    foreach ($ffs as $ff) {

        if (strpos($ff, '.php') !== false && in_array($ff, $files_array, true)) {
            $newff = str_replace($oldFilesClass . '', ucfirst(($plugin_dir)), $ff);
            replaceInFile($oldFilesClass, $dir . '/' . $ff);
            rename($dir . '/' . $ff, $dir . '/' . $newff);
        }
        // if subfolder loop the function
        if (is_dir($dir . '/' . $ff)) changeFilesClasses($dir . '/' . $ff, $oldFilesClass);
    }
}
// let files, traits and classes be
changeFilesClasses(__dir__, $oldFilesClass);
 // flash wp cache because it does not get that a new plugin is allready installed
wp_cache_flush();
// activate the plugin
$result = activate_plugin(dirname(__FILE__) . '/' . basename(dirname(__FILE__)) . '.php');
if (is_wp_error($result)) {
    var_dump($result);
}
//sit down, have a coffee.. you saved days programming
