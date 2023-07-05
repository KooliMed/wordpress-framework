<?php
// DO NOT CHANGE THIS FILE NAME. IT WILL BE DONE AUTOMATICALLY. DO NOT CHANGE CLASSES OR TRAITS NAMES IT WILL BE DONE AUTOMATICALLY. 
if (!defined('ABSPATH')) {
    exit;
}
$thisFolder = dirname(__FILE__);
$plugin_dir_path = explode('pages', $thisFolder);

if (is_dir($plugin_dir_path[0])) {
    require_once($plugin_dir_path[0] . 'Database.php');
}

if ($x_table !== null) {
    return;
}

require_once($plugin_dir_path[0] . '/' . ucfirst($this->plugin_custom_name) . 'Plugin.class.php');
$this->db_plugin_custom_name = basename($plugin_dir_path[0]);
class WhateverDataBasePage extends WhateverPluginClass #DataBasePage class#
{
    public function __construct()
    {
        $this->TableMaxPageRows = 2;
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $plugin_dir_path = explode('pages', dirname(__FILE__));
        $this->db_plugin_custom_name = basename($plugin_dir_path[0]);
        // An array of Field names
        $table = $GLOBALS['wpdb']->prefix . $this->db_plugin_custom_name;
        $table_columns = $GLOBALS['wpdb']->get_col("DESC {$table}", 0);
        // Implode to a string suitable for inserting into the SQL query
        if (count($table_columns) > 0) {
            unset($table_columns[0]);
            $table_columns = $table_columns_original = array_values($table_columns);
        }
?>
        <h1>Automatic Data Table manify and display</h1>
        <hr>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <?php
                    $nbr_columns = count($table_columns);
                    $i = 0;
                    while ($i < $nbr_columns) {
                        $table_columns[$i] = str_replace('_', ' ', $table_columns[$i]);
                        $table_columns[$i] = str_replace('-', ' ', $table_columns[$i]);
                        echo '<td scope="col" class="manage-column column-author sortable desc"><b>' . ucfirst($table_columns[$i]) . '</b></td>';
                        $i++;
                    }
                    ?>

                </tr>
            </thead>
            <tbody id="the-list">
                <?php

                $paginator = $this->db_table_render($table_columns_original);
                if ($paginator == null) echo '<tr><td colspan="' . ($nbr_columns) . '" class="td-ctxt"></td></tr>';
                ?>

            </tbody>
        </table>
        <?php echo '<div class="subsubsub">';
        echo $paginator;
        ?>
        </div>

<?php
    }


    // Get db data and initiate pagination
    public function db_table_render($table_columns_original)
    {
        // Get the value of the plugin setting
        $table = $GLOBALS['wpdb']->prefix . $this->db_plugin_custom_name;

        // Count total records for pagination
        $total_records = $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM (SELECT * FROM $table LIMIT 0,1000) AS a ORDER BY updated_at DESC");
        $current_page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
        $offset_records = ($current_page * $this->TableMaxPageRows) - $this->TableMaxPageRows;

        // first check if data exists with select query
        $results = $GLOBALS['wpdb']->get_results("SELECT * FROM $table ORDER BY updated_at DESC LIMIT $this->TableMaxPageRows OFFSET $offset_records");
        if ($GLOBALS['wpdb']->last_error) {
            echo 'Error: ' . $GLOBALS['wpdb']->last_error;
        }

        // if record found process
        if ($GLOBALS['wpdb']->num_rows > 0) {
            echo $this->recordsTableRender($results, $table_columns_original);

            $paginator = paginate_links(array(
                'base' => add_query_arg('cpage', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => ceil($total_records / $this->TableMaxPageRows),
                'current' => $current_page,
                'type' => 'plain'
            ));
            return $paginator;
        }
    }

    /**
     * parses results and prepares the data table for rendering
     *
     * @param [void] $results
     * @param [int] $table_columns_original
     * @return void
     */
    function recordsTableRender($results, $table_columns_original)
    {
        $resultRecordsTableRender = '';
        $nbr_columns = count($table_columns_original);
        foreach ($results as $result) {
            $i = 0;
            $result = (array) $result;
            while ($i < $nbr_columns) {
                $resultRecordsTableRender .= '<td>' . $result[$table_columns_original[$i]] . '</td>';
                $i++;
            }
            $resultRecordsTableRender .= '</tr>';
        }
        return $resultRecordsTableRender;
    }
}

// this is just magic class
$DynamicName = ucfirst($this->db_plugin_custom_name) . 'DataBasePage';
$DataBasePageClass = new $DynamicName();
