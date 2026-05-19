<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete all testimonials (optional - be careful)
$testimonials = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => -1 ) );
foreach ( $testimonials as $post ) {
    wp_delete_post( $post->ID, true );
}
delete_option( 'tcpt_options' ); // if any