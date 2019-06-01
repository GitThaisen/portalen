<?php
  $ajax = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
  $next = is_paged() ? get_the_posts_pagination() : get_posts_nav_link(array(
    'nxtlabel' => 'Flere<span aria-hidden="true"> &rarr;</span>'
  ));
  
  if (!$ajax) get_header();
  if (!$ajax) echo '<div class="search-hits">';

  $html = '<ul' . (have_posts() ? '' : ' data-missing="' . get_search_query() . '"') . '>';
  $html.= '<li><h1>' . intval($wp_query->found_posts) . ' treff i ' . get_bloginfo('name') . '</h1></li>';

  while (have_posts()) { the_post();
    $cont = $post->post_parent ? get_the_title($post->post_parent) : (get_the_excerpt() ?: 'Side');
    $html.= '<li><a class="search-hit" href="' . get_permalink() . '">';
    $html.= apply_filters('nrk_icon', '', $post->ID) ?: '<i aria-hidden="true" class="fa fa-question-circle"></i>';
    $html.= ' ' . get_the_title() . '<small>' . strip_tags($cont, '<strong>') . '</small></a></li>';
  }
  $html.= '<li>' . $next . '</li></ul>';

  echo apply_filters('nrk_search', $html);

  if (!$ajax) echo '</div>';
	if (!$ajax) get_footer();
