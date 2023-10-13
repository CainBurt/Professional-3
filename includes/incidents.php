<?php
// Hook to add the CSV export action
add_action('admin_init', 'register_csv_incident_export');
function register_csv_incident_export()
{
    if (isset($_GET['incident'])) {
        export_incidents_to_csv();
    }
}

function add_custom_admin_page() {
    add_submenu_page(
        'edit.php?post_type=incident', // Parent menu (post type archive page)
        'Export Incidents',            // Page title
        'Export Incidents',            // Menu title
        'manage_options',              // Capability required to access
        'export-incidents',            // Menu slug
        'display_custom_page'          // Callback function to display the page
    );
}

function export_incidents_to_csv() 
{
    ob_start(); // Start output buffering
    $filename = date('Y-m-d') . '_incidents.csv';


    // Define the post type and ACF field keys
    $file = fopen('php://output', 'w');

    $post_type = 'incident';
    $csv_header = array(
        'Incident ID',
        'Date of Incident',
        'Personal Information Affected?',
        'Information Assets/Personal Data Sets Affected',
        'Asset Owner',
        'Details of the Incident',
        'Threat',
        'Vulnerability',
        'Short Term Containment Action',
        'Action Responsibility',
        'Target Completion Date',
        'Date Affected Information Subjects Notified',
        'Method of Information Subject Notification',
    );

    // Create an empty CSV file
    // $csv_file = fopen(__DIR__ . '/exported_data.csv', 'w');

    // Write the CSV header
    fputcsv($file, $csv_header);

    // Query the posts
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
    );

    $posts = get_posts($args);
    // error_log($posts);


    // Loop through the posts and export ACF data
    foreach ($posts as $post) {
        $post_data = array(
            get_the_title($post),
            get_field('date_of_incident', $post->ID),
            get_field('personal_info_affected', $post->ID),
            get_field('asset_affected', $post->ID),
            get_field('asset_owner', $post->ID),
            get_field('details_of_the_incident', $post->ID),
            get_field('threat', $post->ID),
            get_field('vulnerability', $post->ID),
            get_field('short_term_containment_action', $post->ID),
            get_field('action_responsibility', $post->ID),
            get_field('target_completion_date', $post->ID),
            get_field('date_notified', $post->ID),
            get_field('method_of_notification', $post->ID)
        );
        fputcsv($file, $post_data);
    }

    // Close the CSV file
    fclose($file);
    // Get the output buffer and clean it
    $output = ob_get_clean();

    // Set headers to force download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the CSV
    echo $output;
    exit;
}

function display_custom_page() {
    ?>
    <div class="wrap">
        <h2>Export Incidents</h2>
        <a class="button" href="<?php echo admin_url('admin.php?incident=1'); ?>">Export to CSV</a>
    </div>
    <?php
}
add_action('admin_menu', 'add_custom_admin_page', 11);

