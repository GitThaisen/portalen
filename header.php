<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=EDGE">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <?php function_exists('wp_body_open') ? call_user_func('wp_body_open') : do_action('wp_body_open'); ?>