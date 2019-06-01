<?php
	get_header();
  the_post();

  $home = get_post_type_archive_link(get_post_type());
  $side = '{"align":"none", "view": "list", "parentId":"' . ($post->post_parent ?: 0) . '"}';
  $path = $home ? '<a class="post-crumb" href="' . $home . '">' . esc_html(get_post_type_object(get_post_type())->label) . '</a>' : '';
  $path.= implode('', array_map(function ($id) {
    return '<a href="' . get_permalink($id) . '" class="post-crumb">' . get_the_title($id) . '</a>';
  }, array_reverse(get_post_ancestors($post->ID))));

  $hasLayout = array_reduce(parse_blocks($post->post_content), function ($has, $block) {
    $layouts = array('core/columns', 'core/cover', 'core/media-text', 'nrk/grid');
    return $has || in_array($block['blockName'], $layouts);
  });

  edit_post_link(__('Rediger', 'nrk-portalen'));
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-content'); ?>>
  <?php if (!$post->post_content) { ?>
      <h2 style="padding-top:3rem">
        <?php echo $path; ?>
        <span><?php the_title(); ?></span>
      </h2>
      <?php echo do_blocks('<!-- wp:nrk/grid /-->'); ?>
    <?php } else if ($hasLayout) { ?>
      <h1 class="screen-reader-text"><?php the_title(); ?></h1>
      <?php the_content(); ?>
    <?php } else { ?>
      <div class="wp-block-columns has-2-columns" style="padding-top:3rem">
        <div class="wp-block-column">
          <div class="hi-side">
            <h2>
              <?php echo $path ?: get_the_title(get_option('page_on_front')); ?>
            </h2>
            <?php echo do_blocks("<!-- wp:nrk/grid $side /-->"); ?>
          </div>
        </div>
        <div class="wp-block-column post-content">
          <h1>
            <?php echo apply_filters('nrk_icon', '', $post->ID); ?>
            <?php the_title(); ?>
          </h1>
          <?php the_content(); ?>
        </div>
      </div>
    <?php }
  ?>
</article>
<?php
  get_footer();
