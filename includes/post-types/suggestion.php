<?php

$labels = array(
    'name' => _x('Suggestions', 'plural'),
    'singular_name' => _x('Suggestion', 'singular'),
    'menu_name' => _x('Suggestions', 'admin menu'),
    'name_admin_bar' => _x('Suggestion', 'admin bar'),
    'add_new' => _x('Add New', 'add new'),
    'add_new_item' => __('Add New suggestion'),
    'new_item' => __('New suggestion'),
    'edit_item' => __('Edit suggestion'),
    'view_item' => __('View suggestion'),
    'all_items' => __('All suggestions'),
    'search_items' => __('Search suggestions'),
    'not_found' => __('No suggestions found.'),
  
);
$args = array(
	'label' => __( 'Suggestion'),
	'description'  => __( 'Suggestion'),
	'labels'  => $labels,
	'supports' => array('title','editor', 'author','revisions',),
	'public' => false,
	'publicly_queryable' => true,
	'show_ui' => true,
	'exclude_from_search' => true,
	'show_in_nav_menus' => false,
	'has_archive' => false,
	'rewrite' => false,
	'menu_icon' => 'dashicons-welcome-write-blog',
);
register_post_type( 'suggestion', $args );
