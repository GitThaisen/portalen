<?php
// Register post type and editing
add_action('init', function () {
  wp_register_style('nrk/faq', get_theme_file_uri('components/faq.css'));
  wp_register_script('nrk/faq', get_theme_file_uri('components/faq.js'), array('wp-blocks', 'wp-element', 'wp-editor'));
  register_block_type('nrk/faq', array(
    'editor_style' => 'nrk/faq',
    'editor_script' => 'nrk/faq',
    'render_callback' => 'nrk_faq_render',
    'attributes' => array('faq' => array('type' => 'string', 'default' => '0'))
  ));
  
  register_post_type('faq', array(
    'label'     => 'L&oslash;sninger',
    'menu_icon' => 'dashicons-lightbulb',
    'taxonomies' => array('post_tag'),
    'supports'  => array('title', 'editor', 'excerpt', 'revisions', 'comments'),
		'public'    => true,
		'show_in_rest' => true
  ));
});

function nrk_faq_render ($atts) {
  $post = get_post($atts['faq']);
  $show =
    get_post_status($post) === 'publish'
    || current_user_can('create_users')
    || in_array(strtolower(wp_get_current_user()->user_email), array_map(function ($owner) {
      return strtolower(get_term_meta($owner->term_id, 'email', true));
    }, get_the_terms($post->ID, 'owner') ?: array()));

  if (!intval($atts['faq']) || !$post || !$show) return '';
  setup_postdata($GLOBALS['post'] =& $post);

  $edit = get_edit_post_link();
  $open = home_url(trailingslashit($GLOBALS['wp']->request)) === get_permalink($post);
  $html = '<a rel="bookmark" role="button" href="' . get_permalink() . '" aria-expanded="' . json_encode($open) . '">';
  $html.= $edit ? '<span class="post-edit-link" onclick="window.open(\'' . $edit . '\')">' . __('Rediger', 'nrk-portalen') . '</span>' : '';
  $html.= get_the_title() . '</a><article' . ($open ? '' : ' hidden') . '>' . get_the_content();
  $html.= '<footer><i aria-hidden="true" class="fa fa-check"></i> Oppdatert ' . get_the_modified_date('d.m.Y');
  if (comments_open()) {
    $br = '%0d%0a%0d%0a';
    $body = "Hei!{$br}Jeg har tilbakemelding p&aring; " . get_permalink() . ":{$br}{$br}Takk for hjelpen og ha en fin dag!";
    $href = 'mailto:' . get_option('admin_email') . '?subject=Tilbakemelding: "' . $post->post_title . '"&body=' . $body;
    $html.= ' &nbsp; &nbsp; <a href="' . esc_url($href) . '" style="color:inherit;text-decoration:none"><i aria-hidden="true" class="fa fa-comment"></i> Gi tilbakemelding</a>';
  }
  $html.= '</footer></article>';

  wp_reset_postdata();
  return '<div class="wp-block-nrk-faq">' . $html . '</div>';
}

// Redirect faq urls to the page they are found on
add_action('pre_get_posts', function ($query) {
  global $wpdb;
  if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'faq') {
    $faq_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'faq' AND post_name = %s", $query->get('name')));
    $page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' AND post_content LIKE '%wp:nrk/faq {\"faq\":\"$faq_id\"%'");
    if ($page_id) {
      $query->set('post_type', 'page');
      $query->set('page_id', $page_id);
    }
  }
  return $query;
});

// Allow searching for private posts in admin
add_action('pre_get_posts', function ($query){
  if ($query->is_search && current_user_can('edit_posts')) {
    $query->set('post_status', array('private', 'publish'));
  }
});

// Some "smart-searches" for edtiors to debug their site
add_action('init', function ($query) {
  global $wpdb, $wp_query;
  if (empty($_GET['s']) || !current_user_can('edit_posts')) return;
  if ($_GET['s'] === '#solo') { // Shows FAQs without an owner
    $faqs = wp_list_pluck($wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'faq'"), 'ID');
    $cont = wp_list_pluck($wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE post_type = 'page'"), 'post_content');
    $solo = array_diff($faqs, preg_match_all('/\wp:nrk\/faq \{\"faq\":\"(\d+)/', implode('', $cont), $m) ? $m[1] : array());
    query_posts(array('post_type' => 'faq', 'posts_per_page' => -1, 'post__in' => $solo));
  }
  if ($_GET['s'] === '#empty') { // Shows empty FAQs
    $faq_ids = wp_list_pluck($wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'faq' AND post_content = ''"), 'ID');
    query_posts(array('post_type' => 'faq', 'posts_per_page' => -1, 'post__in' => $faq_ids));
  }
  if ($_GET['s'] === '#nested') { // Shows FAQ with FAQs inside
    $faq_ids = wp_list_pluck($wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'faq' AND post_content LIKE '%wp:nrk/faq%'"), 'ID');
    query_posts(array('post_type' => 'faq', 'posts_per_page' => -1, 'post__in' => $faq_ids ?: array(0)));
  }
  if ($_GET['s'] === '#feilmelding') { // Shows posts with a blockquote/warning
    $faq_ids = wp_list_pluck($wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_content LIKE '%<blockquote%'"), 'ID');
    query_posts(array('post_type' => 'any', 'posts_per_page' => -1, 'post__in' => $faq_ids));
  }
  $wp_query->is_search = true; // Ensure search template
});

