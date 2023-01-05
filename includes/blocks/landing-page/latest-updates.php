<?php 

acf_register_block( array(
    'name'            => 'latest_updates',
    'title'           => __( 'Latest Updates', 'your-text-domain' ),
    'description'     => __( 'Landing page latest updates', 'your-text-domain' ),
    'render_callback' => 'latest_updates_render_callback',
    'category'        => 'formatting',
    'icon'            => 'admin-comments',
    'keywords'        => array( 'latest updates' ),
) );

function latest_updates_render_callback( $block, $content = '', $is_preview = false ) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // Render the block.
    Timber::render( 'components/blocks/landing-page/latest-updates.twig', $context );
}