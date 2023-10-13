<?php

$labels = array(
    'name' => _x('Incidents', 'plural'),
    'singular_name' => _x('Incident', 'singular'),
    'menu_name' => _x('Incidents', 'admin menu'),
    'name_admin_bar' => _x('Incident', 'admin bar'),
    'add_new' => _x('Add New', 'add new'),
    'add_new_item' => __('Log New Incident'),
    'new_item' => __('New Incident'),
    'edit_item' => __('Edit Incident'),
    'view_item' => __('View Incident'),
    'all_items' => __('All Incidents'),
    'search_items' => __('Search Incidents'),
    'not_found' => __('No Incidents Found.'),
  
);
$args = array(
	'label' => __( 'Incident'),
	'description'  => __( 'Incident'),
	'labels'  => $labels,
	'supports' => array(
		// 'title',
		// 'editor', 
		// 'author',
		'revisions',),
	'public' => false,
	'publicly_queryable' => true,
	'show_ui' => true,
	'exclude_from_search' => true,
	'show_in_nav_menus' => false,
	'has_archive' => false,
	'rewrite' => false,
	'menu_icon' => 'dashicons-archive',
);
register_post_type( 'Incident', $args );
