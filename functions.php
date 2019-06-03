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
  if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && constant('REST_REQUEST'))) return $text;
  return '<i class="post-status-icon" aria-label="' . esc_attr(get_post_status($post)) .'"></i>%s';
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
