<?php 

acf_register_block( array(
    'name'            => 'nav_grid',
    'title'           => __( 'Navigation Grid', 'your-text-domain' ),
    'description'     => __( 'Landing page navigation grid', 'your-text-domain' ),
    'render_callback' => 'nav_grid_render_callback',
    'category'        => 'formatting',
    'icon'            => 'admin-comments',
    'keywords'        => array( 'navigation' ),
) );

function nav_grid_render_callback( $block, $content = '', $is_preview = false ) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // Render the block.
    Timber::render( 'components/blocks/landing-page/nav-grid.twig', $context );
}