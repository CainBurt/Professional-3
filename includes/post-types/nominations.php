<?php

$labels = array(
    'name' => _x('Nomination', 'plural'),
    'singular_name' => _x('Nomination', 'singular'),
    'menu_name' => _x('Nominations', 'admin menu'),
    'name_admin_bar' => _x('Nomination', 'admin bar'),
    'add_new' => _x('Add New', 'add new'),
    'add_new_item' => __('Add New nomination'),
    'new_item' => __('New nomination'),
    'edit_item' => __('Edit nomination'),
    'view_item' => __('View nomination'),
    'all_items' => __('All nominations'),
    'search_items' => __('Search nominations'),
    'not_found' => __('No nominations found.'),
  
);
$args = array(
	'label' => __( 'Nomination'),
	'description'  => __( 'Nomination'),
	'labels'  => $labels,
	'supports' => array(
		'title',
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
	'menu_icon' => 'dashicons-welcome-write-blog',
);
register_post_type( 'nominations', $args );
