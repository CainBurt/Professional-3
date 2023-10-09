<?php
// add new columns to users
function new_modify_user_table( $column ) {
    $column['handbook'] = 'Read Employee Handbook?';
    $column['security'] = 'Read Security Policy?';

     $pages = get_posts( array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ) );

    foreach ( $pages as $page ) {
        $resources_blocks = parse_blocks( $page->post_content );

        // Loop through all blocks on the page
        foreach ( $resources_blocks as $block ) {
            // get ones that are resource blocks
            if ( $block['blockName'] === 'acf/resource-block' ) {
                $resources_fields = $block['attrs']['data'];

                //loop through each block and find any that have track_clicks set to true, if yes add to users table
                foreach ( $resources_blocks as $block ) {
                    if ( $block['blockName'] === 'acf/resource-block' ) {
                        $resources_fields = $block['attrs']['data'];
                        $block_title = '';
                        foreach ( $resources_fields as $key => $value ) {
                            if ( strpos( $key, 'title' ) !== false && strpos($key, '_') !== 0 ) {
                                $block_title = $resources_fields[$key];    
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
// add_filter( 'manage_users_columns', 'new_modify_user_table' );