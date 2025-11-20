<?php
add_action( 'wp_enqueue_scripts', 'glozin_child_enqueue_scripts', 20 );
function glozin_child_enqueue_scripts() {
	wp_enqueue_style( 'glozin-child-style', get_stylesheet_uri() );
}