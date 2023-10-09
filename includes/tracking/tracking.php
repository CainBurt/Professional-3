<?php
// add tracking to user table in db
function new_modify_user_table_row($val, $column_name, $user_id)
{
    $new_col_name = "clicked_" . $column_name;
    switch ($column_name) {
        case 'handbook':
            return get_the_author_meta('read_handbook', $user_id);
        case 'security':
            return get_the_author_meta('read_security', $user_id);
        case $column_name:
            return get_the_author_meta($new_col_name, $user_id);
        default:
            return $val;
    }
    return $val;
}
add_filter('manage_users_custom_column', 'new_modify_user_table_row', 10, 3);

function process_contact_form_data()
{
    $wpcf = WPCF7_ContactForm::get_current();
    $form_id = $wpcf->id;
    if ($form_id == 932 || $form_id == 733) {
        update_user_meta(get_current_user_id(), 'read_handbook', 'Yes');
    }
}
add_action('wpcf7_before_send_mail', 'process_contact_form_data');

function process_security_contact_form_data()
{
    $wpcf = WPCF7_ContactForm::get_current();
    $form_id = $wpcf->id;
    if ($form_id == 927 || $form_id == 1499) {
        update_user_meta(get_current_user_id(), 'read_security', 'Yes');
    }
}
add_action('wpcf7_before_send_mail', 'process_security_contact_form_data');

function update_user_field()
{
    $user_id = intval($_POST['user_id']);
    $field_name = sanitize_text_field($_POST['field_key']);
    $field_value = sanitize_text_field($_POST['field_value']);
    $send_request = sanitize_text_field($_POST['send_request']);

    if (current_user_can('edit_user', $user_id) && !empty($send_request)) {
        update_user_meta($user_id, 'clicked_' . $field_name, $field_value);
        wp_send_json(array('status' => 'success'));
    } else {
        wp_send_json(array('status' => 'error'));
    }

    wp_die();
}
add_action('wp_ajax_update_user_field', 'update_user_field');
add_action('wp_ajax_nopriv_update_user_field', 'update_user_field');


function export_tracking_data_to_csv()
{
    $filename = date('Y-m-d') . '_tracking_data.csv';

    $file = fopen('php://output', 'w');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $users = get_users();
    $allUserMetaKeys = array();
    $addedColumns = array(); // Array to track added columns

    foreach ($users as $user) {
        $user_meta = get_user_meta($user->ID);

        foreach ($user_meta as $meta_key => $meta_value) {
            if (strpos($meta_key, 'clicked_') === 0) {
                if (!in_array($meta_key, $allUserMetaKeys)) {
                    $allUserMetaKeys[] = $meta_key;
                    $column_name = str_replace('_', ' ', ucwords(str_replace('clicked_', 'Clicked ', $meta_key)));
                    if (strpos($column_name, '(SOPs)') !== false) {
                        $column_name = str_replace('Clicked ', '', $column_name);
                    }
                    $addedColumns[] = $column_name;
                }
            }
        }
    }

    // Header
    $header = array('Name', 'Email', 'Read Employee Handbook', 'Read Security Policy');
    foreach ($allUserMetaKeys as $meta_key) {
        $column_name = str_replace('_', ' ', ucwords(str_replace('clicked_', 'Clicked ', $meta_key)));
        if (strpos($column_name, '(SOPs)') !== false) {
            $column_name = str_replace('Clicked ', '', $column_name);
        }
        if (in_array($column_name, $addedColumns)) {
            $header[] = $column_name;
        }
    }

    fputcsv($file, $header);

    // User Data
    foreach ($users as $user) {
        $user_data = array(
            $user->display_name,
            $user->user_email,
            (get_user_meta($user->ID, 'read_handbook', true) === 'Yes' ? 'Yes' : ''),
            (get_user_meta($user->ID, 'read_security', true) === 'Yes' ? 'Yes' : ''),
        );

        foreach ($allUserMetaKeys as $meta_key) {
            $data = get_user_meta($user->ID, $meta_key, true);
            if ($data === 'No') {
                $data = '';
            }

            if (in_array($column_name, $addedColumns)) {
                $user_data[] = $data;
            }
        }

        fputcsv($file, $user_data);
    }

    fclose($file);
    exit;
}



// Hook to add the CSV export action
add_action('admin_init', 'register_csv_export_action');
function register_csv_export_action()
{
    if (isset($_GET['report'])) {
        export_tracking_data_to_csv();
    }
}


// Function to display the tracking information
function display_tracking_page()
{
?>
    <div class="wrap">
        <h2 style="display: inline; padding-right: 25px">Tracking Page</h2>
        <a class="button" href="<?php echo admin_url('admin.php?page=tracking-page&report=1'); ?>">Export to CSV</a>
        <div class="tracking-wrap">
            <table class="tracking-table fixed striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Read Employee Handbook?</th>
                        <th>Read Security Policy?</th>
                        <?php
                        $users = get_users();
                        $column_keys = array();
                        $added_columns = array();
                        foreach ($users as $user) {
                            $user_meta = get_user_meta($user->ID);

                            // Dynamically add column keys based on 'clicked_' keys
                            foreach ($user_meta as $meta_key => $meta_value) {
                                if (strpos($meta_key, 'clicked_') === 0) {
                                    $column_name = str_replace('_', ' ', ucwords(str_replace('clicked_', 'Clicked ', $meta_key))); // Format the column name
                                    $page = '';
                                    if (strpos($column_name, '(SOPs)') !== false) {
                                        $page .= '<span style="color: var(--orange)">(SOP)</span>';
                                        $column_name = str_replace('Clicked ', '', $column_name);
                                        $column_name = str_replace('(SOPs)', '', $column_name);
                                    }

                                    if (!in_array($column_name, $added_columns)) {
                                        $column_keys[$meta_key] = true;
                                        $added_columns[] = $column_name;
                                        echo '<th>' . $page . esc_html($column_name) . '</th>';
                                    }
                                }
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = get_users();
                    foreach ($users as $user) {
                        $user_meta = get_user_meta($user->ID);
                        echo '<tr>';
                        echo '<td>' . esc_html($user->display_name) . '</td>';
                        echo '<td class="' . (get_the_author_meta('read_handbook', $user->ID) === 'Yes' ? 'yes-cell' : '') . '">' . (get_the_author_meta('read_handbook', $user->ID) === 'Yes' ? '&#9989;' : '') . '</td>';
                        echo '<td class="' . (get_the_author_meta('read_security', $user->ID) === 'Yes' ? 'yes-cell' : '') . '">' . (get_the_author_meta('read_security', $user->ID) === 'Yes' ? '&#9989;' : '') . '</td>';
                        // Dynamically display columns for tracking data starting with 'clicked_'
                        foreach ($column_keys as $column_key => $value) {
                            if (isset($user_meta[$column_key])) {
                                $cell_content = esc_html($user_meta[$column_key][0]);
                                echo '<td class="' . ($cell_content === 'Yes' ? 'yes-cell' : '') . '">' . ($cell_content === 'Yes' ? '&#9989;' : '') . '</td>';
                            } else {
                                echo '<td></td>';
                            }
                        }
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}

function add_tracking_menu_page()
{
    add_menu_page('Tracking Page', 'Tracking Page', 'manage_options', 'tracking-page', 'display_tracking_page');
}
add_action('admin_menu', 'add_tracking_menu_page');




// Function to display the "Clicked_" fields as dropdowns on the user profile page
function display_clicked_fields_dropdowns_on_profile_page($profileuser)
{
    $user_meta = get_user_meta($profileuser->ID);

    $clicked_fields = array();

    foreach ($user_meta as $meta_key => $meta_value) {
        if (strpos($meta_key, 'clicked_') === 0) {
            $field_name = esc_html($meta_key);
            $field_value = esc_html($meta_value[0]);

            $clicked_fields[] = array(
                'name' => $field_name,
                'value' => $field_value,
            );
        }
    }

    if (!empty($clicked_fields)) {
        echo '<h2>Clicked Fields</h2>';
        echo '<table class="form-table">';

        foreach ($clicked_fields as $field) {
            echo '<tr>';
            //    $field['name'] = str_replace('_', ' ', ucwords(str_replace('clicked_', 'Clicked ', $field['name']))); // Format the column name

            echo '<th>' . $field['name'] . '</th>';
            echo '<td>';

            $options = array(
                'Yes' => 'Yes',
                '' => ' ',
            );

            echo '<select class="regular-text" name="user_meta[' . $field['name'] . ']">';

            foreach ($options as $option_value => $option_label) {
                $selected = ($field['value'] === $option_value) ? 'selected="selected"' : '';
                echo '<option value="' . esc_attr($option_value) . '" ' . $selected . '>' . esc_html($option_label) . '</option>';
            }

            echo '</select>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
    }
}
add_action('show_user_profile', 'display_clicked_fields_dropdowns_on_profile_page');
add_action('edit_user_profile', 'display_clicked_fields_dropdowns_on_profile_page');

function save_clicked_fields_user_meta($user_id)
{
    if (current_user_can('edit_user', $user_id)) {
        if (isset($_POST['user_meta']) && is_array($_POST['user_meta'])) {
            foreach ($_POST['user_meta'] as $meta_key => $meta_value) {
                update_user_meta($user_id, $meta_key, $meta_value);
            }
        }
    }
}
add_action('personal_options_update', 'save_clicked_fields_user_meta');
add_action('edit_user_profile_update', 'save_clicked_fields_user_meta');
