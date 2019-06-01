<?php
define('NRK_ICON_JS', '/components/icon/core-icons.min.js');
define('NRK_ICON_CSS', '/components/icon/font-awesome.min.css');
define('NRK_ICON_KEY', 'nrk-icon');

add_action('wp_enqueue_scripts', 'nrk_icon_assets');
add_action('admin_enqueue_scripts', 'nrk_icon_assets');
add_action('add_meta_boxes', function() { add_meta_box(NRK_ICON_KEY, 'Ikon', 'nrk_icon_edit', null, 'side'); });

add_filter('nrk_icon', function ($html = '', $id) {
  return nrk_icon_html(get_post_meta($id, 'icon', true) ?: $html);
}, 10, 2);

add_action('save_post', function ($id) {
  if (isset($_POST[NRK_ICON_KEY])) update_post_meta($id, 'icon', sanitize_text_field($_POST[NRK_ICON_KEY]));
});

function nrk_icon_assets () {
  wp_enqueue_script('theme-icon', get_theme_file_uri(NRK_ICON_JS));
  wp_enqueue_style('theme-icon', get_theme_file_uri(NRK_ICON_CSS));
  add_editor_style(get_theme_file_uri(NRK_ICON_CSS));
}

function nrk_icon_html ($icon) {
  $is_core_icon = strpos($icon = esc_attr($icon), 'nrk-') === 0;
  $svg = $is_core_icon ? '<svg style="width:1em;height:1em;vertical-align:middle"><use xlink:href="#' . $icon . '" /></svg>' : '';
  return $icon ? '<i aria-hidden="true" class="fa fa-' . $icon . '">' . $svg . '</i>' : '';
}

function nrk_icon_edit ($post) {
  global $wp_filesystem;
  require_once(ABSPATH . '/wp-admin/includes/file.php');
  WP_Filesystem();

  preg_match_all('/\.fa-([^:.]+):+before/', $wp_filesystem->get_contents(get_template_directory() . NRK_ICON_CSS), $icons);
  preg_match_all('/\"(nrk-[^"]+)/', $wp_filesystem->get_contents(get_template_directory() . NRK_ICON_JS), $core);
  $icons = array_map('esc_attr', array_merge(array(''), $icons[1], $core[1]));
  $value = get_post_meta($post->ID, 'icon', true);
  ?>
  <input type="search" name="icon-search" class="widefat" placeholder="S&oslash;k i ikoner">
  <div style="font-size:0;overflow:auto;margin:7px 0;max-height:200px">
    <?php foreach ($icons as $icon) {
      $id = NRK_ICON_KEY . '_' . $icon;      
      echo '<input class="theme-icon" type="radio" id="' . $id . '" name="' . NRK_ICON_KEY . '" value="' . $icon . '" ' . checked($value, $icon, false) . '>';
      echo '<label for="' . $id . '" title="' . $icon . '">' . nrk_icon_html($icon) . '</label>';
    } ?>
  </div>
  <style>
    .theme-icon { position: absolute; visibility: hidden }
    .theme-icon + label{ display: inline-block; vertical-align: top; box-sizing: border-box; width: 14.25%; border: 1px solid #fff; font-size: 14px; line-height: 2em; height: 2em; text-align: center; background: #eee }
    .theme-icon:checked + label { background: #0073aa; color: #fff }
  </style>
  <script>
    document.addEventListener('input', function (event) {
      if (event.target.name === 'icon-search') [].forEach.call(event.target.nextElementSibling.children, function (el) {
        el.style.display = el.title.indexOf(event.target.value) < 0 ? 'none' : ''
      })
    })
  </script>
<?php }
