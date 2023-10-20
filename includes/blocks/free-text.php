<?php 

acf_register_block( array(
    'name'            => 'free_text',
    'title'           => __( 'Free Text', 'free-text' ),
    'description'     => __( 'A free text blocks', 'free-text' ),
    'render_callback' => 'free_text_render_callback',
    'category'        => 'formatting',
    'icon'            => 'dashicons-welcome-write-blog',
    'keywords'        => array( 'free-text', 'text'),
) );

function free_text_render_callback( $block, $content = '', $is_preview = true ) {
    $context = Timber::context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    // Store $is_preview value.
    $context['is_preview'] = $is_preview;

    $current_page = Timber::get_post();
    $context['current_page_title'] = $current_page->title;
    // Render the block.
    Timber::render( 'components/blocks/free-text.twig', $context);
}