<?php
if (!defined('ABSPATH')) {
  exit;
}
/*
  $table is the default database name inherited from the plugin Display Name
  If you want to change this name, you will have to rewrite all the installDatabaseTables() function and not just the $table variable.
*/
$x_table = null;
if (isset($table)) {
  $x_table = 'CREATE TABLE `' . $table . '` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `element` varchar(64) NOT NULL,
                            `active` TINYINT(1) DEFAULT 0 NOT NULL ,
                            `custom_element` varchar(1024) NOT NULL,
                            `custom_end_element` varchar(1024) NOT NULL,
                            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

INSERT INTO `' . $table . '` (`id`, `element`, `active`, `custom_element`, `custom_end_element`, `updated_at`) VALUES (NULL, "element 1","1","custom element 1","custom end element 1", CURRENT_TIMESTAMP);
INSERT INTO `' . $table . '` (`id`, `element`, `active`, `custom_element`, `custom_end_element`, `updated_at`) VALUES (NULL, "element 2","1","custom element 2","custom end element 2", CURRENT_TIMESTAMP);
INSERT INTO `' . $table . '` (`id`, `element`, `active`, `custom_element`, `custom_end_element`, `updated_at`) VALUES (NULL, "element 3","1","custom element 3","custom end element 3", CURRENT_TIMESTAMP);
INSERT INTO `' . $table . '` (`id`, `element`, `active`, `custom_element`, `custom_end_element`, `updated_at`) VALUES (NULL, "element 4","1","custom element 4","custom end element 4", CURRENT_TIMESTAMP);
INSERT INTO `' . $table . '` (`id`, `element`, `active`, `custom_element`, `custom_end_element`, `updated_at`) VALUES (NULL, "element 5","1","custom element 5","custom end element 5", CURRENT_TIMESTAMP);
INSERT INTO `' . $table . '` (`id`, `element`, `active`, `custom_element`, `custom_end_element`, `updated_at`) VALUES (NULL, "element 6","1","custom element 6","custom end element 6", CURRENT_TIMESTAMP);';
}
// Do not delete the content of this file. Just uncomment the below line to bypass creating a database table and page
// $x_table = null;