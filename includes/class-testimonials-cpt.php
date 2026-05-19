<?php

if (! defined('ABSPATH')) {
    exit;
}

class Testimonials_CPT
{
    private static $instance = null;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init_hooks();
    }

    public function run()
    {
        // This method runs when plugin loads
        $this->load_textdomain();
    }

    private function load_textdomain()
    {
        load_plugin_textdomain('testimonials-cpt', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    private function init_hooks()
    {
        add_action('init', [$this, 'register_testimonial_cpt']);
        add_action('init', [$this, 'register_taxonomy']);
        add_action('add_meta_boxes', [$this, 'add_testimonial_metabox']);
        add_action('save_post_testimonial', [$this, 'save_testimonial_meta']);
        add_shortcode( 'testimonials', array( $this, 'testimonials_shortcode' ) );
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);
    }

    /**
     * Register Custom Post Type
     */
    public function register_testimonial_cpt()
    {
        $labels = array(
            'name'               => __('Testimonials', 'testimonials-cpt'),
            'singular_name'      => __('Testimonial', 'testimonials-cpt'),
            'menu_name'          => __('Testimonials', 'testimonials-cpt'),
            'add_new'            => __('Add New', 'testimonials-cpt'),
            'add_new_item'       => __('Add New Testimonial', 'testimonials-cpt'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'testimonial'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 25,
            'menu_icon'          => 'dashicons-testimonial',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        );

        register_post_type('testimonial', $args);
    }

    /**
     * Register Taxonomy
     */
    public function register_taxonomy()
    {
        $labels = array(
            'name'          => __('Testimonial Categories', 'testimonials-cpt'),
            'singular_name' => __('Category', 'testimonials-cpt'),
        );

        register_taxonomy('testimonial_category', 'testimonial', array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array('slug' => 'testimonial-category'),
        ));
    }

    /**
     * Add Metabox
     */
    public function add_testimonial_metabox() {
        add_meta_box(
            'testimonial_meta',
            __('Client Information', 'testimonial-cpt'),
            [$this, 'testimonial_metea_callback'],
            'testimonial',
            'normal',
            'high'
        );
    }

    public function testimonial_metea_callback($post) {
        wp_nonce_field('testimonial_meta_nonce', 'testimonial_meta_nonce');

        $client_name = get_post_meta($post->ID, '_client_name', true);
        $company = get_post_meta($post->ID, '_company', true);
        $rating = get_post_meta($post->ID, '_rating', true);

        ?>

        <p>
            <label for=""><strong>Client Name:</strong></label><br>
            <input type="text" name="client_name" value="<?php echo esc_attr($client_name); ?>" class="widefat">
        </p>

        <p>
            <label for=""><strong>Company:</strong></label><br>
            <input type="text" name="company" value="<?php echo esc_attr($company); ?>" class="widefat">
        </p>

         <p>
            <label for=""><strong>Rating (1-5):</strong></label><br>
            <input type="number" name="rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5" style="width:100px;">
        </p>

        <?php
    }

    public function save_testimonial_meta($post_id) {
        if( !isset($_POST['testimonial_meta_nonce']) || !wp_verify_nonce($_POST['testimonial_meta_nonce'], 'testimonial_meta_nonce')) {
            return;
        }

        if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        $fields = ['client_name', 'company', 'rating'];
        foreach( $fields as $field ){
            if( isset($_POST[$field]) ){
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[$field] ) );
            }
        }
    }

    /**
     * Shortcode: [testimonials limit="6" category="slug"]
     */
    public function testimonials_shortcode($atts) {
        $atts = shortcode_atts([
            'limit'     => 6,
            'category'  => ''
        ], $atts);

        $args = [
            'post_type'         => 'testimonial',
            'posts_per_page'    => absint($atts['limit']),
            'orderby'           => 'date',
            'order'             => 'DESC'
        ];

        if(!empty($atts['category'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'testimonial_category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ]
            ];
        }

        $query = new WP_Query($args);
        ob_start();
        ?>
        <div class="tcpt-testimonial">
            <?php if( $query->have_posts() ) while($query->have_posts()) : $query->the_post(); ?>
                <div class="testimonial-item">
                    <?php the_post_thumbnail('medimu', ['class' => 'testimonial-avatar']); ?>
                    <div class="testimonial-content">
                        <?php the_content(); ?>
                        <p class="client-info">
                            <strong><?php echo esc_html( get_post_meta( get_the_ID(), '_client_name', true ) ); ?></strong>
                            <?php if ( $company = get_post_meta( get_the_ID(), '_company', true ) ) : ?>
                                , <?php echo esc_html( $company ); ?>
                            <?php endif; ?>
                        </p>
                        <?php if ( $rating = get_post_meta( get_the_ID(), '_rating', true ) ) : ?>
                            <p class="testimonial-rating">
                                <strong><?php esc_html_e('Rating:', 'testimonials-cpt'); ?></strong>
                                <?php echo esc_html( $rating ); ?>/5
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php
        return ob_get_clean();
    }

    public function enqueue_public_assets(){
        wp_enqueue_style('tcpt-public-style', TCPT_PLUGIN_URL . 'assets/css/public.css', [], TCPT_VERSION);
    }
    
}
