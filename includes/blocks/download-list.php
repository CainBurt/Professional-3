<?php 

acf_register_block( array(
    'name'            => 'download_list',
    'title'           => __( 'Download List', 'download-list' ),
    'description'     => __( 'A list of downloads', 'download-list' ),
    'render_callback' => 'download_list_render_callback',
    'category'        => 'formatting',
    'icon'            => 'download',
    'keywords'        => array( 'download-list', 'download', 'list' ),
) );

function download_list_render_callback( $block, $content = '', $is_preview = true ) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // Render the block.
    Timber::render( 'components/blocks/download-list.twig', $context);
}