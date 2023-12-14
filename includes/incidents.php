<?php
add_action('admin_init', 'register_csv_incident_export');
function register_csv_incident_export()
{
    if (isset($_GET['incident'])) {
        export_incidents_to_csv();
    }
}

function add_custom_admin_page() {
    add_submenu_page(
        'edit.php?post_type=incident',
        'Export Incidents',            
        'Export Incidents',            
        'manage_options',              
        'export-incidents',            
        'display_custom_page'          
    );
}

function export_incidents_to_csv() 
{
    ob_start();
    $filename = date('Y-m-d') . '_incidents.csv';

    $file = fopen('php://output', 'w');

    $post_type = 'incident';
    $csv_header = array(
        'Incident ID',
        'Date of Incident',
        'Personal Information Affected?',
        'Asset Owner',
        'Details of the Incident',
        'Threat',
        'Vulnerability',
        'Screenshots',
        'Short Term Containment Action',
        'Action Responsibility',
        'Target Completion Date',
        'Date Affected Information Subjects Notified',
        'Method of Information Subject Notification',
    );

    fputcsv($file, $csv_header);

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
    );

    $posts = get_posts($args);

    foreach ($posts as $post) {
        $screenshots = get_field('screenshots', $post->ID);
        if (is_array($screenshots)) {
            $screenshots_urls = array();
            foreach ($screenshots as $screenshot) {
                if (is_array($screenshot) && isset($screenshot['screenshot']['url'])) {
                    $screenshots_urls[] = $screenshot['screenshot']['url'];
                }
            }
            $screenshots_string = implode(', ', $screenshots_urls);
        } else {
            $screenshots_string = $screenshots; 
        }
        $post_data = array(
            get_the_title($post),
            get_field('date_of_incident', $post->ID),
            get_field('personal_info_affected', $post->ID),
            get_field('asset_owner', $post->ID),
            get_field('details_of_the_incident', $post->ID),
            get_field('threat', $post->ID),
            get_field('vulnerability', $post->ID),
            $screenshots_string,
            get_field('short_term_containment_action', $post->ID),
            get_field('action_responsibility', $post->ID),
            get_field('target_completion_date', $post->ID),
            get_field('date_notified', $post->ID),
            get_field('method_of_notification', $post->ID),
        );
        fputcsv($file, $post_data);
    }

    fclose($file);

    $output = ob_get_clean();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    echo $output;
    exit;
}

// function add_export_to_csv_button_to_posts_archive() {
//     if (is_admin() && get_current_screen()->post_type == 'incident') {
//         echo '<a style="position: absolute; transform: -100px "class="button" href="' . admin_url("admin.php?incident=1") . '">Export to CSV</a>';
//     }
// }

// add_action('admin_notices', 'add_export_to_csv_button_to_posts_archive');



function enqueue_custom_admin_script() {
    if (isset($_GET['post_type']) && $_GET['post_type'] == 'incident') {
        wp_enqueue_script('incident-admin', get_template_directory_uri() . '/dist/js/incident-admin.js', array('jquery'), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_script');


