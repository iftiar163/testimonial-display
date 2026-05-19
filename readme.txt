=== Testimonials CPT ===
Contributors: Iftiar Hossain
Donate link: https://example.com/
Tags: testimonials, custom post type, shortcode
Requires at least: 5.0
Tested up to: 6.5
Stable tag: trunk
Requires PHP: 7.4

== Description ==
A lightweight custom post type plugin for managing testimonials and displaying them with a shortcode.

== Installation ==
1. Upload the `testimonials-cpt` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. Add testimonials from `Testimonials` in the WordPress admin menu.
4. Create or edit a page/post and insert the shortcode: `[testimonials]`.

== Usage ==
* Use `[testimonials]` to show the latest testimonials.
* Optional attributes:
  * `limit="6"` — number of testimonials to display (default: 6)
  * `category="slug"` — filter by testimonial category slug

Example:

    [testimonials limit="4" category="clients"]

== Frequently Asked Questions ==
= How do I include the rating in outputs? =
The plugin saves a rating value from the testimonial metabox and displays it automatically in the shortcode output.

== Changelog ==
= 1.0.0 =
* Initial release with testimonial CPT, taxonomy, metaboxes, and shortcode.
