<?php 

acf_register_block( array(
    'name'            => 'team_block',
    'title'           => __( 'Team Block', 'your-text-domain' ),
    'description'     => __( 'A custom tesm block.', 'your-text-domain' ),
    'render_callback' => 'team_render_callback',
    'category'        => 'formatting',
    'icon'            => 'admin-comments',
    'keywords'        => array( 'example' ),
) );

function team_render_callback( $block, $content = '', $is_preview = false ) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    // Render the block.
    Timber::render( 'components/blocks/team.twig', $context);
}