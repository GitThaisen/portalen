<?php
// Compontents
require(__DIR__ . '/components/faq.php');
require(__DIR__ . '/components/icon.php');
require(__DIR__ . '/components/grid.php');
require(__DIR__ . '/components/owner.php');

// Theme
if(!isset($content_width)) $content_width = 1190; // Set default content width
remove_action('wp_head', 'wp_generator'); // Hide WP version for security
add_action('wp_enqueue_scripts', function () { // Load theme JS/CSS
  wp_enqueue_script('theme_script', get_theme_file_uri('script.js'), null, null, true);
  wp_enqueue_style('theme_style', get_theme_file_uri('style.css'));
});

// Allow post content to be URL
add_filter('post_type_link', 'nrk_allow_url_posts', 10, 2);
add_filter('page_link', 'nrk_allow_url_posts', 10, 2);
function nrk_allow_url_posts ($permalink, $id) {
  $text = trim(strip_tags(preg_replace('/<!--(.|\s)*?-->/', '', get_post($id)->post_content)));
  return filter_var($text, FILTER_VALIDATE_URL) ?: $permalink;
}

// Add tags to pages
add_action('init', function () {
  register_taxonomy_for_object_type('post_tag', 'page');
  add_post_type_support('page', 'excerpt');
});

// Setup theme options
add_action('after_setup_theme', function () {
  set_post_thumbnail_size(1900, 1900);
  add_editor_style(get_theme_file_uri('/style.css'));
  add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));
  add_theme_support('align-wide');
  add_theme_support('title-tag');
  add_theme_support('editor-styles');
  add_theme_support('post-thumbnails');
  add_theme_support('responsive-embeds');
  add_theme_support('automatic-feed-links');
});

// Make sure wp_title is never empty
add_filter('wp_title', function ($title) {
  return $title ?: get_bloginfo('name');
}, 99);

// Prettier "Private" indication on post titles
add_filter('private_title_format', 'nrk_title_status', 10, 2);
add_filter('protected_title_format', 'nrk_title_status', 10, 2);
function nrk_title_status ($text, $post) {
  return '<svg style="width:1rem;height:1rem;color:#252627;margin-right:5px" aria-label="' . esc_attr(get_post_status($post)) .'" viewBox="0 0 20 20"><path d="M10.02 5.4c1.26 0 2.4.53 3.2 1.37l-2.15 1.3A2.1 2.1 0 0 1 10.74 7a3 3 0 0 0-3.67 3.48l-1.32.8a4.5 4.5 0 0 1 4.26-5.87zm-1.93 6.8l-1.33.8h.01l-2.25 1.36-3 1.8a1 1 0 0 1-1.37-.34 1 1 0 0 1 .33-1.38l2.24-1.34C1.18 11.86.25 10.67.25 10.67a1.38 1.38 0 0 1 0-1.6S4.3 3.9 9.68 3.9c2.02 0 3.94.75 5.55 1.67l3.2-1.93a1 1 0 1 1 1.03 1.72l-2.37 1.42.01.01-2.82 1.7v-.01l-1.35.8v.02L8.1 12.2zm1.91.7c-.3 0-.6-.06-.88-.14l3.82-2.3A3 3 0 0 1 10 12.9zm9.72-3.78c.37.41.37 1.1 0 1.51 0 0-4.68 5.27-10.05 5.27-1.5 0-2.88-.41-4.11-1l2.03-1.22a4.5 4.5 0 0 0 6.93-3.78c0-.13-.03-.25-.04-.37l3.46-2.08c1.11.92 1.78 1.67 1.78 1.67z" fill-rule="evenodd"/></svg> %s';
}

// Show all pages and allow private parents
add_filter('edit_page_per_page', function () { return 999; });
add_filter('quick_edit_dropdown_pages_args', 'admin_allow_private_parent');
add_filter('page_attributes_dropdown_pages_args', 'admin_allow_private_parent');
function admin_allow_private_parent ($args){
  return array_merge($args, array('post_status' => array('publish', 'private')));
}

// Add chevron button to wysiwyg
add_action('enqueue_block_editor_assets', function () {
  wp_enqueue_script('nrk/chevron', get_theme_file_uri('components/chevron.js'), array('wp-rich-text'));
});

// Redirect 404 to search
add_action('get_header', function () {
  if (!is_admin() && is_404()) wp_redirect(home_url('?s=' . basename($_SERVER['REQUEST_URI'])));
});

// Remove front page from hits and limit to 6 results
add_action('pre_get_posts', function ($query) {
  if (!is_admin() && $query->is_main_query() && $query->is_search) {
    $query->set('post__not_in',  array(get_option('page_on_front')));
    $query->set('posts_per_page', 6);
  }
});

// Enhance the_title on search page with highlighting search query
add_filter('the_title', function ($title, $id) {
  $fn_relevanssi = 'relevanssi_get_the_title';
  $is_relevanssi = !is_admin() && is_search() && function_exists($fn_relevanssi);
  return $is_relevanssi ? call_user_func($fn_relevanssi, $id) : $title;
}, 1, 2);

// Enhance get_the_excerpt on search page with fallback to post_content
add_filter('relevanssi_pre_excerpt_content', function ($content, $post) {
  return trim($post->post_excerpt) ?: do_blocks($content);
}, 10, 2);

// Boost page weight in search
add_filter('relevanssi_match', function ($match) {
	if (relevanssi_get_post_type($match->doc) === 'page') $match->weight*= 1.5;
	return $match;
});

// Expand gutenberg blocks to become visible for search
add_filter('relevanssi_post_content', 'do_blocks');

// Add notes
add_action('add_meta_boxes', function() {
  add_meta_box('post_notes', 'Notater', function ($post) {
    echo '<textarea name="post_notes" class="widefat">';
    echo esc_html(get_post_meta($post->ID, 'post_notes', true)) . '</textarea>';
  }, null, 'side');
});

// Save notes
add_action('save_post', function ($id) {
  if (isset($_POST[$key = 'post_notes'])) update_post_meta($id, $key, sanitize_text_field($_POST[$key]));
});
