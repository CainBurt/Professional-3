<?php 

acf_register_block( array(
    'name'            => 'start_block',
    'title'           => __( 'Before you start Block', 'your-text-domain' ),
    'description'     => __( 'Before you start block.', 'your-text-domain' ),
    'render_callback' => 'start_render_callback',
    'category'        => 'formatting',
    'icon'            => 'admin-comments',
    'keywords'        => array( 'example' ),
) );

function start_render_callback( $block, $content = '', $is_preview = false ) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // Render the block.
    Timber::render( 'components/blocks/start.twig', $context );
}