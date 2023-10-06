<?php
// Function to export tracking data to CSV
function export_tracking_data_to_csv() {
    // Define the CSV filename and headers

    $filename = 'tracking_data.csv';

    // Create a file pointer and write CSV data
    $file = fopen('php://output', 'w');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Add the header row
    fputcsv($file, array('User', 'Read Employee Handbook', 'Read Security Policy', /* Add more columns here */));

    // Retrieve actual tracking data from your database
    $users = get_users(array('role' => 'subscriber')); // Adjust the query as needed

    foreach ($users as $user) {
        $user_data = array(
            $user->display_name,
            get_user_meta($user->ID, 'read_handbook', true),
            get_user_meta($user->ID, 'read_security', true),
            // Add more data retrieval here for additional columns
        );

        // Add the user data to the CSV
        fputcsv($file, $user_data);
    }

    fclose($file);
    exit;
}


// Hook to add the CSV export action
add_action('admin_init', 'register_csv_export_action');
function register_csv_export_action() {
    if (isset($_POST['export_csv'])) {
        export_tracking_data_to_csv();
    }
}


// Function to display the tracking information
function display_tracking_page() {
    ?>
    <div class="wrap">
        <h2>Tracking Page</h2>
        <a class="button" href="<?php echo admin_url('admin.php?page=export-csv'); ?>">Export to CSV</a>
        <table class="tracking-table fixed striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Read Employee Handbook?</th>
                    <th>Read Security Policy?</th>
                    <!-- Add more columns for additional tracking data here -->
                    <?php
                    $users = get_users();
                    $column_keys = array();
                    $added_columns = array(); // Array to track added columns
                    foreach ($users as $user) {
                        $user_meta = get_user_meta($user->ID);
                        
                        // Dynamically add column keys based on 'clicked_' keys
                        foreach ($user_meta as $meta_key => $meta_value) {
                            if (strpos($meta_key, 'clicked_') === 0) {
                                $column_name = str_replace('_', ' ', ucwords(str_replace('clicked_', 'Clicked ', $meta_key))); // Format the column name
                                $page = '';
                                // Check if the column name contains '(SOPs)' and modify it accordingly
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
                    echo '<td class="' . (get_the_author_meta('read_handbook', $user->ID) === 'Yes' ? 'yes-cell' : '') . '">' . (get_the_author_meta('read_handbook', $user->ID) === 'Yes' ? '&#9989;' : '' ). '</td>';
                    echo '<td class="' . (get_the_author_meta('read_security', $user->ID) === 'Yes' ? 'yes-cell' : '') . '">' . (get_the_author_meta('read_security', $user->ID) === 'Yes' ? '&#9989;' : '' ) . '</td>';
                     // Dynamically display columns for tracking data starting with 'clicked_'
                    foreach ($column_keys as $column_key => $value) {
                        if (isset($user_meta[$column_key])) {
                            $cell_content = esc_html($user_meta[$column_key][0]);
                            // Display "Yes" or an empty cell for other tracking columns
                            echo '<td class="' . ($cell_content === 'Yes' ? 'yes-cell' : '') . '">' . ($cell_content === 'Yes' ? '&#9989;' : '' ) . '</td>';
                        } else {
                            // Display an empty cell if the user doesn't have data for this column
                            echo '<td></td>';
                        }
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        
    </div>
    <?php
}

// Function to add the menu page
function add_tracking_menu_page() {
    add_menu_page('Tracking Page', 'Tracking Page', 'manage_options', 'tracking-page', 'display_tracking_page');
    add_submenu_page('tracking-page', 'Export to CSV', 'Export to CSV', 'manage_options', 'export-csv', 'export_tracking_data_to_csv');

}

// Hook to add the menu page
add_action('admin_menu', 'add_tracking_menu_page');