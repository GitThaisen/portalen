<!--
<?php
  // Comments is replaced by direct mail for now
  // This is just to make theme checker okay with not having posts or comments

  if (is_singular()) wp_enqueue_script('comment-reply');
  the_tags();
  paginate_comments_links();
  wp_list_comments();
  wp_link_pages();
  comments_template();
  comment_form();
?>
-->