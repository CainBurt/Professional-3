<?php

define( 'EDIT_STRINGS_DB_VERSION', 2 );

class Strings_List_Table extends WP_List_Table {
    // just the barebone implementation.
    public function get_columns() {
        $table_columns = array(		 
            'orig_string' => __( 'Original Text', $this->plugin_text_domain ),
            'replacement_string' => __( 'Replacement Text', $this->plugin_text_domain ),
        );
        return $table_columns;
    }
    public function prepare_items() {
        $this->_column_headers = [
            $this->get_columns(),
            [], // hidden columns
            $this->get_sortable_columns(),
            $this->get_primary_column_name(),
        ];
        $table_data = $this->fetch_table_data();
        $this->items = $table_data;
    }

    public function display_tablenav($which) {
        ?>
        <form action="" method="GET">
            <?php 
            $this->search_box( __( 'Search' ), 'search-box-id' ); 
            ?>
            <input type="hidden" name="page" value="<?= esc_attr($_REQUEST['page']) ?>"/>
        </form>
        <?php
    }

    public function fetch_table_data() {
        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        global $wpdb;
        $table_name = $wpdb->prefix . 'crowd_edit_strings';
        if ($search) {
            $strings = $wpdb->get_results("SELECT * FROM $table_name WHERE `orig_string` LIKE '%$search%' OR `replacement_string` LIKE '%$search%';", 'ARRAY_A');
        } else {
            $strings = $wpdb->get_results("SELECT * FROM $table_name;", 'ARRAY_A');
        }
        return $strings;
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'replacement_string':
                $html = '<form class="string-edit-form"><input type="hidden" name="string" value="' . $item['orig_string'] . '" /><textarea name="replacement" cols="40">' . $item[$column_name] . '</textarea><button class="button">Save</button></form>';
                return $html;
            default:
                return $item[$column_name];
        }
    }
    
}

function update_edit_strings() {
    $string = $_GET['string'];
    $replacement = $_GET['replacement'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'crowd_edit_strings';
    $sql = "SELECT string_id FROM $table_name WHERE `orig_string` = '$string';";
    $instance = $wpdb->get_row($sql, 'ARRAY_A');
    if (gettype($instance) == 'array') {
        $update = $wpdb->update( 
            $table_name, 
            array( 
                'replacement_string' => $replacement,   // string
            ), 
            array( 'string_id' => $instance['string_id'] ),
        );
        if ($update === false) {
            wp_send_json_error( array(
                'error' => 'That text could not be updated',
                'string' => $string,
                'replacement' => $replacement
            ), 500 );
        } else {
            wp_send_json_success( array(
                'string' => $string,
                'replacement' => $replacement
            ), 200 );
        }
    } else {
        wp_send_json_error( array(
            'error' => 'That text doesn\'t exist',
            'string' => $string,
            'replacement' => $replacement
        ), 500 );
    }
}
add_action( 'wp_ajax_update_edit_strings', 'update_edit_strings' );

function init_edit_strings() {
    global $wpdb;
	$table_name = $wpdb->prefix . 'crowd_edit_strings';
	$sql = "CREATE TABLE $table_name (
						 string_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						 orig_string text NOT NULL,
						 replacement_string text
						 ) DEFAULT CHARACTER SET utf8";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	update_option( 'crowd_edit_strings_version', EDIT_STRINGS_DB_VERSION );
}

add_action('after_setup_theme', 'init_edit_strings');

function register_edit_strings_scripts() {

    // wp_register_style( 'my-plugin', get_template_directory_uri( ) . 'ddd/css/plugin.css' );

    wp_register_script( 'edit-strings', get_template_directory_uri( ) . '/includes/edit-strings/strings-admin.js' );

}



add_action( 'admin_enqueue_scripts', 'register_edit_strings_scripts' );



function load_edit_strings_scripts( $hook ) {

// Load only on ?page=sample-page

    if( $hook != 'toplevel_page_edit-theme-text' ) {

    return;

    }

    // Load style & scripts.

    // wp_enqueue_style( 'my-plugin' );

    wp_enqueue_script( 'edit-strings' );

}



add_action( 'admin_enqueue_scripts', 'load_edit_strings_scripts' );


function edit_strings_settings_init() {
    
    add_settings_section(
        'edit_strings_setting_section',
        __( 'Strings', 'my-textdomain' ),
        'edit_strings_section_callback_function',
        'edit-strings'
    );

}

function edit_strings_section_callback_function() {
    // echo '<p>Edit strings to save their </p>';
    
    

}
add_action( 'admin_init', 'edit_strings_settings_init' );

function edit_strings_admin_page_contents() {
    ?>
        <h1>Edit Theme Text</h1>
        <p>Use <a href="https://developer.wordpress.org/themes/functionality/internationalization/" target="_blank">template strings</a> with the text domain <code>crowd</code> to register text for editing. Load the front end where they appear at least once for them to be registered here.</p>
        <p>Example: <code>__("Your text here", "crowd")</code>.<br/>(In <code>.twig</code> files, make sure this is within in the normal curly braces).</p>
        <?php
            $list_table = new Strings_List_Table( 'crowd-admin' );
            $list_table->prepare_items();
            $list_table->display();
        ?>
    <?php
}

function create_edit_strings_menu_page() {
    add_menu_page( __('Edit Theme Text', 'crowd-admin'), 
    __('Edit Theme Text', 'crowd-admin'), 'manage_options', 
    'edit-theme-text', 'edit_strings_admin_page_contents', 
    '', 40 );
}
add_action( 'admin_menu', 'create_edit_strings_menu_page' );

function change_string( $translated_text, $text, $domain ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'crowd_edit_strings';
    $sql = "SELECT orig_string,replacement_string FROM $table_name WHERE `orig_string` = '$text';";
    $instance = $wpdb->get_row($sql, 'ARRAY_A');
    if (gettype($instance) == "array") {
        $translated_text = str_replace($text, $instance['replacement_string'], $translated_text);
    } else {
        $wpdb->insert($table_name,array(
            'orig_string' => $text,
            'replacement_string' => $text
        ));
    }
    return $translated_text; 
} 
add_filter( 'gettext_crowd', 'change_string', 100, 3 );