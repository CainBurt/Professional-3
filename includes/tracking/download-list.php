<?php
// add new columns to users
function download_add_user_table( $column ) {
    // $column['handbook'] = 'Read Employee Handbook?';
    // $column['security'] = 'Read Security Policy?';

     $pages = get_posts( array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ) );

    foreach ( $pages as $page ) {
        $download_list = parse_blocks( $page->post_content );

        // Loop through all blocks on the page
        foreach ( $download_list as $block ) {
            // get ones that are resource blocks
            if ( $block['blockName'] === 'acf/download-list' ) {
                $download_items = $block['attrs']['data'];

                //loop through each block and find any that have track_clicks set to true, if yes add to users table
                foreach ( $download_list as $block ) {
                    if ( $block['blockName'] === 'acf/download-list' ) {
                        $download_items = $block['attrs']['data'];
                        error_log(print_r($download_items, true));
                        $block_title = '';
                        foreach ( $download_items as $key => $value ) {
                            if ( strpos( $key, 'title' ) !== false && strpos($key, '_') !== 0 ) {
                                $block_title = $download_items[$key];    
                            }
                            elseif ( strpos( $key, 'track_clicks' ) !== false && $value == 1 ) {
                                $column[$block_title] = "Clicked ". $block_title;                                 
                            } 
                        }
                    }
                }
            }
        }
    }
    return $column;
}
// add_filter( 'manage_users_columns', 'download_add_user_table' );