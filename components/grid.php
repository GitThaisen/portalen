<?php
add_action('init', function () {
  wp_register_script('nrk/grid', get_theme_file_uri('components/grid.js'), array('wp-blocks', 'wp-element', 'wp-editor'));
  register_block_type('nrk/grid', array(
    'editor_script' => 'nrk/grid',
    'render_callback' => 'nrk_grid_render',
    'attributes' => array(
      'backgroundColor' => array( 'type' => 'string', 'default' => '' ),
      'parentId' => array( 'type' => 'string', 'default' => '-1' ),
      'view' => array( 'type' => 'string', 'default' => 'icons' )
    )
  ));
});

function nrk_grid_render ($atts) {
  $back = $atts['backgroundColor'] ? 'alignfull has-background has-' . $atts['backgroundColor'] . '-background-color' : 'not-background';
  $space = str_repeat('<li aria-hidden="true"></li>', 5); // Helps flex equaly size items in last row
  $class = 'wp-block-nrk-grid ' . $back . ' has-' . $atts['view'];

  return '<ul class="' . esc_attr($class) . '">' . strtr(wp_list_pages(array(
    'echo' => false,
    'title_li' => false,
    'child_of' => $atts['parentId'] === '-1' ? get_the_ID() : intval($atts['parentId']),
    'walker' => new NRK_Grid_Block_Walker($atts),
    'post_type' => get_post_type(),
    'post_status' => current_user_can('edit_posts') ? 'publish,private' : 'publish',
    'depth' => 2
  )), array('</ul>' => "$space</ul>")) . "$space</ul>";
}

class NRK_Grid_Block_Walker extends Walker {
  public $db_fields = array('parent' => 'post_parent', 'id' => 'ID');
	
	public function __construct ($atts) {
  	$this->view = $atts['view'];
	}

	public function start_lvl(&$html, $depth = 0, $args = array()) {
  	$html.= '<ul class="wp-block-nrk-grid has-popup" hidden>';
  }
	public function end_lvl(&$html, $depth = 0, $args = array()) {
  	$html .= '</ul>';
  }
	public function start_el(&$html, $page, $depth = 0, $args = array(), $current_page = 0) {
  	$pops = $depth === 0 && isset($args['pages_with_children'][$page->ID]) && empty($page->post_content);
  	$icon = ($depth === 0 && $this->view === 'images') ? '<div class="wp-block-nrk-grid__img">' . get_the_post_thumbnail($page, array(250, 120)) . '</div>' : apply_filters('nrk_icon', '', $page->ID);
    $html.= '<li class="' . ($icon ? 'has' : 'no') . '-icon has-level-' . $depth . '">';

    if ($pops) $html.= '<button class="wp-block-nrk-grid__item" data-grid-popup>';
    else $html.= '<a class="wp-block-nrk-grid__item" href="' . get_permalink($page) . '">';

    $html.= $icon . get_the_title($page) . '<small>' . strip_tags($page->post_excerpt ?: '') . '</small>';
    $html.= $pops ? '</button>' : '</a>';
	}
	public function end_el(&$html, $page, $depth = 0, $args = array()) {
  	$html .= '</li>';
  }
}
