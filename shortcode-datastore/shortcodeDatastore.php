<?php
    /*
    Plugin Name: Shortcode Datastore
    Plugin URI: https://github.com/azumbro/ShortcodeDatastore
    Version: 1.0.0
    Description: Allows for flexible plain text or HTML to be bound to shortcodes that can be inserted into pages, posts, or templates. This allows for the same text/HTML to be inserted in multiple places and then managed from one dashboard.
    Author: azumbro
    */
    
    defined('ABSPATH') or die('No script kiddies please!');

    /* Start function install code. */
    // Register below code for when the plugin is activated.
    register_activation_hook( __FILE__, 'sds_install' );
    function sds_install() {
        global $wpdb;
        // Setup variables.
        $sdsDBSchemaVersion = '1.0.0'; // This value must be incremented whenever a table change is made.
        $installedSchemaVersion = get_option('sdsDBSchemaVersion');
        $sdsTable = $wpdb->base_prefix . 'sds_data';
        // Bring in the code for the dbDelta call.
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // Check and make sure that the new schema version is different from the last one.
        if($installedSchemaVersion !== $sdsDBSchemaVersion) {
            // Build the table creation SQL.
            $sql = "CREATE TABLE " . $sdsTable . " (
                sdsKey VARCHAR(255) NOT NULL, 
                sdsValue TEXT DEFAULT '' NOT NULL, 
                PRIMARY KEY  (sdsKey)
            ) ". $charset_collate .";";
            // Run the table creation SQL.
            dbDelta($sql);
            // Update the schema version in WP.
            update_option('sdsDBSchemaVersion', $sdsDBSchemaVersion);
        }
    }
    /* End function install code. */

    /* Start dashboard page code. */
    // This function tales a message and error bool, outputting a message string in the WP display box format.
    function sdsCreateMessage($message, $error) {
        $message = '<div id="message" class="' . ($error ?  "error" : "updated") . ' notice is-dismissible"><p>' . $message . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        return $message;
    }
    // Register the below code as a plugin page on the dashboard.
    add_action( 'admin_menu', 'sdsOptionsAddMenuPage' );
    function sdsOptionsAddMenuPage() {
        add_menu_page('Shortcode Datastore', 'Shortcode Datastore', 'manage_options', 'sdsoptions', 'sdsOptions');
    }
    function sdsOptions() {
        if(!current_user_can( 'manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        // Setup variables to use on the page.
        global $wpdb;
        $pluginPageURL = "admin.php?page=sdsoptions";
        $pluginPath = plugins_url("shortcode-datastore/");
        $postEndpoint = "admin-post.php";
        $sdsTable = $wpdb->base_prefix . 'sds_data';
        // Check if there is a valid nonce in the URL. This is the case for deletions.
        $validNonce = false;
        if(sanitize_key($_GET['_wpnonce'])) {
            $validNonce = wp_verify_nonce(sanitize_key($_GET['_wpnonce']));
        }
        // If action=create, set up the form page for adding a new shortcode.
        // This requires a valid nonce.
        if((sanitize_key($_GET['action']) == "create" || sanitize_key($_GET['action']) == "edit") && $validNonce) {
            // The form posts to admin-post, with the action specified as a hidden field routing it to the handler below.
            echo '<div class="wrap">';
            echo '<p align="center"><img src="' . $pluginPath . 'assets/ShortcodeDatastoreLogo.png" width="250px"></p>';
            echo '<hr>';
            // Output different wording for create and edits.
            $message = '<h2 style="float: left;">' . (sanitize_key($_GET['action']) == "create" ? 'Create a new shortcode' : 'Edit shortcode \'' . sanitize_key($_GET['key']) . '\'' ) . '.</h2>';
            echo $message;
            echo '<div style="float: right; margin-top: 12px;"><a href="' . $pluginPageURL . '" class="page-title-action">Existing Shortcodes</a></div>';
            echo "<p>&nbsp;</p>";
            echo "<p>&nbsp;</p>";
            echo '<form action="' . $postEndpoint . '" method="POST">';
            echo '<input type="hidden" name="action" value="sdsRequest">';
            echo '<input type="hidden" name="type" value="' . sanitize_key(sanitize_key($_GET['action'])) . '">';
            echo '<p style="font-weight:bold;">Shortcode Key:</p>';
            // Populate the key field if this is an edit.
            echo '<input type="text" name="key" style="width: 300px;"' . (sanitize_key($_GET['action']) == "edit" ? ' value="' . sanitize_key($_GET['key']) . '" readonly' : '') . ' required>';
            echo "<p>&nbsp;</p>";
            echo '<p style="font-weight:bold;">Shortcode Value:</p>';
            // If this is an edit, grab the value for the specified key and insert it into the editor.
            $existingValue = '';
            if(sanitize_key($_GET['action']) == "edit") {
                // Prepare and run the query for the specified key, returning the value.
                $query = $wpdb->prepare("SELECT sdsValue FROM " . $sdsTable . " WHERE sdsKey = '%s'", sanitize_key($_GET['key']));
                $rows = $wpdb->get_results($query);
                $existingValue = $rows[0]->sdsValue;
            }
            wp_editor($existingValue, 'value', array('theme_advanced_buttons1' => 'bold, italic, ul, pH, pH_min', "media_buttons" => true, "textarea_rows" => 8, "tabindex" => 4));
            echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="' . (sanitize_key($_GET['action']) == "create" ? "Create" : "Update") .' Shortcode"></p>';
            echo '</form>';
            echo "</div>";
        } 
        // For non-create actions, build the table page.
        else {
            // On action=delete, remove the shortcode from the database.
            // This requires a valid nonce.
            $isDelete = false;
            if(sanitize_key($_GET['action']) == "delete" && $validNonce) {
                $isDelete = true;
                if($wpdb->delete($sdsTable, array('sdsKey' => sanitize_key($_GET['key']))) > 0) {
                    $deleteSuccessful = true;
                }
                else {
                    $deleteSuccessful = false;
                }
            } 
            // Grab all the shortcodes for the database table.
            $rows = $wpdb->get_results("SELECT sdsKey, sdsValue FROM " . $sdsTable);
            echo '<div class="wrap">';
            echo '<p align="center"><img src="' . $pluginPath . 'assets/ShortcodeDatastoreLogo.png" width="250px"></p>';
            echo '<hr>';
            // Output messages for delete or create actions.
            if($isDelete) {
                echo sdsCreateMessage(($deleteSuccessful ?  "Shortcode deleted successfully." : "An error ocurred while deleting. Please try again."), !$deleteSuccessful);
            }
            if(sanitize_key($_GET["comingfrom"]) == "create" || sanitize_key($_GET["comingfrom"]) == "edit") {
                $successMessage = "Shortcode " . (sanitize_key($_GET["comingfrom"]) == "create" ? "added" : "updated") . " successfully.";
                $errorMessage = (sanitize_key($_GET["success"]) == 0 ? "An error occurred. Please try again." : "Cannot create shortcodes with duplicate keys. Please try again.");
                echo sdsCreateMessage((sanitize_key($_GET["success"]) == 1 ?  $successMessage : $errorMessage), sanitize_key($_GET["success"] != 1));
            }
            echo '<div style="float: left"><p>For usage instructions, see the plugin <a href="https://github.com/azumbro/ShortcodeDatastore" target="_blank">documentation</a>.</p></div>';
            $url = admin_url() . "admin.php?page=sdsoptions&action=create";
            echo '<div style="float: right; margin-top: 12px;"><a href="' . wp_nonce_url($url) . '" class="page-title-action">Add Shortcode</a></div>';
            // Build the shortcode table.
            echo '<table class="wp-list-table widefat fixed striped sites">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Shortcode Key</th>';
            echo '<th>Shortcode Value</th>';
            echo '<th>Shortcode</th>';
            echo '<th></th>';
            echo '</tr>';
            echo '<tbody>';
            foreach($rows as $row) {
                echo '<tr>';
                echo '<td>' . $row->sdsKey . '</td>';
                echo '<td>' . htmlspecialchars($row->sdsValue) . '</td>';
                echo '<td><input type="text" value="[sds key=\'' . $row->sdsKey . '\']" readonly></td>';
                echo '<td class="row-actions visible">';
                $url = admin_url() . "admin.php?page=sdsoptions&action=edit&key=" . $row->sdsKey;
                echo '<span class="edit"><a href="' . wp_nonce_url($url) .'">Edit</a></span>';
                echo ' | ';
                $url = admin_url() . "admin.php?page=sdsoptions&action=delete&key=" . $row->sdsKey;
                echo '<span class="delete"><a href="' . wp_nonce_url($url) .'" onclick="return confirm(\'Are you sure you would like to delete this shortcode?\')">Delete</a></span>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '<script>window.history.pushState({}, document.title, "' . admin_url($pluginPageURL). '");</script>';
            echo '</div>';
        }
    }
    /* End dashboard page code. */

    /* Start POST handlers. */
    // Register the below code as a handler.
    add_action('admin_post_sdsRequest', 'sdsHandler');
    // This handler takes the POST request from the create form and inserts to the database table.
    function sdsHandler() {        
        global $wpdb;
        $sdsTable = $wpdb->base_prefix . 'sds_data';
        $pluginPageURL = "admin.php?page=sdsoptions";
        // The 'comingfrom' field specifies create/edit and success specifies if the insert was successful.
        $params = "&comingfrom=" . sanitize_key($_POST["type"]) . "&success=";
        if($_POST["type"] == "create") {
            // On a create, insert to the database table. Keys are standardized to lower case here. Also, replace spaces with underscores.
            // On success, 1 is returned (the number of new rows).
            if($wpdb->insert($sdsTable, array("sdsKey" => sanitize_key(str_replace(" ", "_", sanitize_key($_POST["key"]))), "sdsValue" => wp_kses_post($_POST["value"]))) == 1) {
                $params .= "1";
            }
            else {
                // We want to let the user know if they try to insert a duplicate. So, check the error message here.
                if(strpos($wpdb->last_error, 'Duplicate') !== false) {
                    $params .= "2";
                }
                // This is the case of a non-duplicate error.
                else {
                    $params .= "0";
                }
            }
        }
        else {
            // On an update, update the database table for the specified key.
            // On success, 1 is returned (the number of new rows).
            if($wpdb->update($sdsTable, array("sdsValue" => wp_kses_post($_POST["value"])), array("sdsKey" => sanitize_key($_POST["key"]))) == 1) {
                $params .= "1";
            }
            else {
                $params .= "0";
            }
        }
        wp_redirect($pluginPageURL . $params);
    }
    /* End POST handlers. */

    /* Start shortcode code. */
    // Register the below code as a shortcode.
    add_shortcode('sds', 'sdsShortcode');
    // This code handle shortcodes inserted into WP content.
    function sdsShortcode($atts) {
        global $wpdb;
        $sdsTable = $wpdb->base_prefix . 'sds_data';
        // Extract the shortcode attributes. We need "key".
        extract(shortcode_atts(array(
            'key' => "",
        ), $atts));
        // Prepare and run the query for the specified key, returning the value.
        $query = $wpdb->prepare("SELECT sdsValue FROM " . $sdsTable . " WHERE sdsKey = '%s'", $key);
        $rows = $wpdb->get_results($query);
        // Out put the value for the specified key. If the key does not exist, this will output an empty string.
        return esc_html($rows[0]->sdsValue);
    }
    /* End shortcode code. */