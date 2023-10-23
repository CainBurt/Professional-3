<?php
/**
 * Template Name: Security Policy Template
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['navigation'] = new TimberMenu($post->get_field('sidebarnavigation'));
preg_match_all('/<(h\d*).*?(id="(.*?)").*?>(.*?)<\//',$post->content,$matches);
$levels = $matches[1];
$anchors = $matches[3];
$headings = $matches[4];
$context['anchors'] = array();
// $context['form'] = new TimberText($post->get_field('form_shortcode'));
foreach ($anchors as $key => $anchor) {
    $context['anchors'][] = array(
        'anchor' => $anchor,
        'heading' => $headings[$key]
    );
}
Timber::render( array( 'page-security-policy.twig', 'page.twig' ), $context );