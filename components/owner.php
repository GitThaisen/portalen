<?php
// Register owner taxonomy (Run after registering post types)
add_action('init', function () {
  $post_types = get_post_types(array('public' => true));
  register_taxonomy('owner', $post_types, 'show_admin_column=1&hierarchical=1&show_in_rest=1&label=Eier');
}, 99);

// Hide unneded admin-interface
add_action('admin_head', function () { ?>
  <style>
    #owner-adder,
    #toplevel_page_nrk-owner,
    .taxonomy-owner .column-slug,
    .taxonomy-owner .term-parent-wrap,
    .taxonomy-owner .term-slug-wrap { display:none }
  </style>
<?php });

// Edit
add_action('owner_add_form_fields', 'nrk_owner_term_edit');
add_action('owner_edit_form_fields', 'nrk_owner_term_edit');
function nrk_owner_term_edit ($owner) {
  $is_new = empty($owner->term_id); // Are we editing existing or new owner?
  $fields = array(
    'email' => 'E-post',
    'frequency' => array(
      'Revisjonshyppighet',
      'week' => 'Ukentlig',
      'month' => 'M&aring;nedlig',
      'quarter' => 'Kvartalsvis',
      'year' => '&angst;rlig'
    )
  );
  
  foreach ($fields as $name=>$label) {
    $value = $is_new? '' : get_term_meta($owner->term_id, $name, true);
    $single = is_string($label);
    
    echo $is_new? '<div class="form-field">' : '<tr class="form-field"><th scope="row">';
    echo '<label>' . ($single ? $label : reset($label)) . '</label>' . ($is_new? '' : '</th><td>');
    if ($single) echo '	<input name="owner-' . $name . '" type="text" value="' . esc_attr($value) . '">';
    else foreach ($label as $k=>$v) if (!is_numeric($k)) {
      echo '<label style="display:inline-block"><input name="owner-' . $name . '" type="radio" value="';
      echo $k . '"' . checked($value, $k, false) . '>' . $v . '</label> &nbsp; ';
    } 
    echo $is_new? '</div>' : '</td></tr>';
  }
}

// Save
add_action('created_owner', 'nrk_owner_save');
add_action('edited_owner', 'nrk_owner_save');
function nrk_owner_save($term_id){
  foreach(array('email', 'frequency') as $name) {
    if (isset($_POST[$k="owner-$name"])) update_term_meta($term_id, $name, sanitize_text_field($_POST[$k]));
  }
}

// Get posts by owner
function nrk_owner_get_posts_by_term ($owner_id, $args = array()) {
  return array_filter(get_posts(array_merge(array(
    'post_type' => get_taxonomy('owner')->object_type,
    'post_status' => array('publish', 'pending', 'draft', 'private', 'future'),
    'posts_per_page' => -1,
    'order' => 'ASC',
    'orderby' => 'modified',
    'tax_query' => array(array(
      'taxonomy' => 'owner',
      'terms' => $owner_id
    ))
  ), wp_parse_args($args))), function($post){
    return strpos(get_permalink($post), home_url('/')) === 0;  // Only render inbound links
  });
}

// Show all count of all posts (public, private, etc)
add_filter('get_terms', function ($terms) {
  if (function_exists('get_current_screen') && get_current_screen()->taxonomy === 'owner') {
    foreach ($terms as $term) if (isset($term->taxonomy) && $term->taxonomy === 'owner') {
      $term->count = count(nrk_owner_get_posts_by_term($term->term_id, 'fields=ids'));
    }
  }
  return $terms;
});

// Verify page GUI
add_action('wp_loaded', function(){
  if (empty($_GET['owner']) || !is_user_logged_in()) return;
  if(!($owner = get_term_by('slug', $_GET['owner'], 'owner'))) return; // Validate owner
  $action = admin_url('admin-ajax.php?action=owner');
  $posts = nrk_owner_get_posts_by_term($owner->term_id);
  $time = current_time('timestamp');
  ?>
  <!doctype html>
  <html>
  <head>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?php wp_title(); ?></title>
    <style>
      html, body { margin: 0; font: 15px/1.35 sans-serif; background: #f3f5f7 }
      body { margin-left: calc(100vw - 300px); padding: 20px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif }

      .owner-frame { position: fixed; background: #fff; top: 0; left: 0; width: calc(100% - 300px); height: 100%; box-shadow: 0 5px 20px rgba(0,0,0,.2) }
      .owner-title { margin-top: 0; font-weight: 300; font-size: 24px }
      .owner-item { position: fixed; left: -99px; opacity: 0 }
      .owner-item + div { display: block; color: #252627; background: #fff; border-radius: 3px; margin-bottom: 2px; box-shadow: 0 2px 7px rgba(0,0,0,.1); transition: .2s }
      .owner-item + div > label { display: block; cursor: pointer; padding: 11px; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none }
      .owner-item + div > label small { display: block; margin-top: 3px; opacity: .7 }
      .owner-item + div > form { display: none; overflow: hidden }

      .owner-item:checked + div { background: #f3f5f7; box-shadow: 0 0 0 2px #1769ff, -2px 0 0 2px #1769ff }
      .owner-item:checked + div > form { display: block }

      .owner-button { -webkit-appearance: none; box-sizing: border-box; float: left; vertical-align: top; text-align: left; cursor: pointer; color: inherit; font: inherit; width: 50%; height: 40px; margin: 0; border: 0; border-radius: 0; box-shadow: inset -1px 1px rgba(0,0,0,.1); padding: 9px 9px 9px 40px; transition: .2s }
      .owner-button { background: #f3f5f7 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 15 15'%3E%3Cpath d='M6.21 11.35L3.44 8.5c-.44-.52.4-1.5.88-1.08.5.42 1.84 1.48 1.84 1.48l4.35-4.42c.6-.53 1.64.45 1.03 1.1l-5.32 5.77z'/%3E%3C/svg%3E") 0 50%/40px 20px no-repeat }
      .owner-button:active { transform: scale(.97) }
      .owner-button:hover { filter: brightness(.9) }
      .owner-button--fail { width: 98%; margin: 1%; padding: 0; text-align: center; box-shadow: none; border-radius: 3px; background: #1769ff; color: #fff }
      
      .owner-fail { position: fixed; left: -99px; opacity: 0 }
      .owner-fail ~ div { clear: both; overflow: hidden; visibility: hidden; height: 0; background: #fff }
      .owner-fail ~ div textarea { vertical-align: top; box-sizing: border-box; width: 100%; border: 0; margin: 0; padding: 9px; background: none; font: inherit; color: inherit; resize: none; outline-offset: -5px }
      .owner-fail ~ label { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 15 15'%3E%3Cpath d='M8.81 2.31l4.66 8.47A1.5 1.5 0 0 1 12.15 13h-9.3a1.5 1.5 0 0 1-1.32-2.22L6.2 2.32a1.5 1.5 0 0 1 2.62 0zm-.87.49a.5.5 0 0 0-.88 0l-4.65 8.46a.5.5 0 0 0 .44.74h9.3a.5.5 0 0 0 .44-.74L7.94 2.8zm-.44 8.45a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5zm0-2a.66.66 0 0 1-.66-.66V5.9a.66.66 0 0 1 1.32 0V8.6a.66.66 0 0 1-.66.66z'/%3E%3C/svg%3E"); }
      .owner-fail:checked ~ div { visibility: visible; height: auto }
      .owner-fail:checked ~ label { background-color: #fff }

      .owner-done { opacity: .4; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 15 15'%3E%3Cpath d='M6.21 11.35L3.44 8.5c-.44-.52.4-1.5.88-1.08.5.42 1.84 1.48 1.84 1.48l4.35-4.42c.6-.53 1.64.45 1.03 1.1l-5.32 5.77z'/%3E%3C/svg%3E") 100% 50%/40px 20px no-repeat }
      .owner-exit { position: absolute; top: 0; right: 0; padding: 15px; color: inherit; text-decoration: none }
    </style>
  </head>
  <body>
    <h1 class="owner-title">
      <?php echo count($posts); ?> l&oslash;sninger eid av<br>
      <?php echo esc_html($owner->name, 'nrk'); ?>:
    </h1>
    <a class="owner-exit" href="<?php echo home_url('/'); ?>" aria-label="Avslutt kontroll">
      <svg aria-hidden="true" width="20" height="20" viewBox="0 0 15 15"><path stroke="currentColor" stroke-linecap="round" d="M3,3 L12,12 M3,12 L12,3"></path></svg>
    </a>
    <?php foreach($posts as $i=>$post) { ?>
      <input id="owner-item-<?php echo $i; ?>" type="radio" class="owner-item" name="item" value="<?php echo get_permalink($post); ?>">
      <div>
        <label for="owner-item-<?php echo $i; ?>">
          <?php echo get_the_title($post); ?>
          <small><?php echo human_time_diff(get_the_modified_time('U', $post), $time); ?></small>
        </label>
        <form action="<?php echo $action; ?>">
          <button class="owner-button" type="submit">Ser bra ut</button>
          <input type="hidden" name="id" value="<?php echo esc_attr($post->ID); ?>">
          <input id="owner-fail-<?php echo $i; ?>" type="checkbox" class="owner-fail" name="fail">
          <label for="owner-fail-<?php echo $i; ?>" class="owner-button">Kan rettes</label>
          <div>
            <textarea id="owner-text-<?php echo $i; ?>" name="text" placeholder="Hva kan rettes? Endres? Bli bedre?"></textarea>
            <button type="submit" class="owner-button owner-button--fail"><?php _e('Send inn', 'nrk-portalen'); ?></button>
          </div>
        </form>
      </div>
    <?php } ?>
    <iframe class="owner-frame" frameborder="0" width="100%" height="100%" src="<?php echo home_url(); ?>"></iframe>
    <script>
      (function(){
        // Navigate to URL of active input
        document.addEventListener('change', function(event){
          if(event.target.name === 'item') document.querySelector('.owner-frame').src = event.target.value
          if(event.target.name === 'fail') document.getElementById(event.target.id.replace('-fail', '-text')).focus()
        })
        
        // Autosize textarea
        document.addEventListener('input', function (event) {
          if (event.target.name === 'text') {
            event.target.style.height = 'auto';
            event.target.style.height = event.target.scrollHeight + 'px';
          }
        })
        
        // Submit feedback
        document.addEventListener('submit', function (event) {
          event.preventDefault();

          var form = event.target
          var ajax = new window.XMLHttpRequest()
          var data = [].map.call(form.elements, function (el) {
            var isInput = el.name && ((el.type === 'checkbox' || el.type === 'radio')? el.checked : el.value)
            return isInput && encodeURIComponent(el.name) + '=' + encodeURIComponent(el.value)
          }).filter(Boolean).join('&')
          console.log(data)

          ajax.open('POST', form.action, true)
          ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
          ajax.send(data)
          form.previousElementSibling.classList.add('owner-done')
          form.parentNode.removeChild(form)

          var next = document.querySelector('label[for^="item"]:not(.owner-done)') || form
          
          next.focus()
          next.click()
        })
      })();
    </script>
  </body>
  <?php die();
});

// Verify page AJAX
add_action('wp_ajax_owner', function () {
  $time = current_time('mysql');
  $post = get_post($_POST['id']);
  $user = wp_get_current_user();
  $fine = empty($_POST['fail']) || empty($_POST['text']);
  
  if (!$post) wp_die('post not found');
  else if ($fine) {
    wp_update_post(array(
      'ID' => $post->ID,
      'post_date ' => $time,
      'post_date_gmt' => get_gmt_from_date($time)
    ));
    wp_die('update');
  } else {
    $href = get_permalink($post);
    $body = 'Hei!<br>' . esc_html($user->user_email) . ' har tilbakemelding p&aring; ';
    $body.= '<a href="' . $href .'">' . $href . '</a>:<br><br>' . esc_html($_POST['text']);

    wp_new_comment(array(
      'user_id' => $user->ID,
      'comment_post_ID' => $post->ID,
      'comment_content' => $_POST['text']
    ));
    wp_mail(get_option('admin_email'), "Tilbakemelding p&aring; $href", $body, 'Content-Type: text/html; charset=UTF-8');
    wp_die('fail');
  }
});

// Setup cron job
add_filter('cron_schedules', function($schedules) {
  $day = 60 * 60 * 24;
	return array_merge($schedules, array(
    'weekly'    => array('interval' => $day * 7,   'display' => 'Weekly'),
    'monthly'   => array('interval' => $day * 30,  'display' => 'Monthly'),
    'quarterly' => array('interval' => $day * 91,  'display' => 'Quarterly'),
    'yearly'    => array('interval' => $day * 365, 'display' => 'Yearly') 	  
	));
});

// add_action('port_owners_test', 'nrk_owners_mail');
add_action('port_owners_week', 'nrk_owners_mail');
add_action('port_owners_month', 'nrk_owners_mail');
add_action('port_owners_quarter', 'nrk_owners_mail');
add_action('port_owners_year', 'nrk_owners_mail');
add_action('init', function(){
  if(!wp_next_scheduled($k = 'port_owners_week', $a = array('week'))) wp_schedule_event(time(), 'weekly', $k, $a);
  if(!wp_next_scheduled($k = 'port_owners_month', $a = array('month'))) wp_schedule_event(time(), 'monthly', $k, $a);
  if(!wp_next_scheduled($k = 'port_owners_quarter', $a = array('quarter'))) wp_schedule_event(time(), 'quarterly', $k, $a);
  if(!wp_next_scheduled($k = 'port_owners_year', $a = array('year'))) wp_schedule_event(time(), 'yearly', $k, $a);
  // if(isset($_GET['mail'])) do_action('port_owners_test');
});

// Send mail
function nrk_owners_mail ($frequency) {
  $owners = get_terms(array(
    'taxonomy' => 'owner',
    'meta_key' => 'frequency',
    'meta_value' => $frequency
  ));

  foreach($owners as $owner) {
    $mail = get_term_meta($owner->term_id, 'email', true);
    $href = home_url('/?owner=' . urlencode($owner->slug));
    $name = get_bloginfo('name');
    $from = get_option('admin_email');
    $head = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $name . ' <' . $from . '>');

    $body = '<strong>Hei, ' . esc_html($owner->name) . '!</strong>';
    $body.= '<br><br><strong>Du st&aring;r oppf&oslash;rt som eier av innhold p&aring; ' . home_url('/'). '</strong>';
    $body.= '<br>' . $name . ' brukes mye, og det er viktig at innhold er korrekt og oppdatert.';
    $body.= '<br><br><strong>Kvalitetssikre ditt innhold</strong>';
    $body.= '<br>G&aring; til <a href="' . $href . '">' . $href . '</a> og trykk "Ser bra ut" eller "Noe kan rettes"';
    $body.= '<br>OBS: Det er ogs&aring; viktig &aring; klikke "Ser bra ut", s&aring; vi vet at innholdet er korrekt og kontrollert.';
  
    $body.= '<br><br><strong>Vi hjeper deg</strong>';
    $body.= '<br>Dersom du har sp&oslash;rsm&aring;l eller synes disse mailene kommer for ofte, ';
    $body.= ' kontakt oss p&aring; <a href="mailto:' . $from . '">' . $from . '</a>';
    $body.= '<br><br>Takk for hjelpen, og ha en fin dag!';
  
    if ($mail) wp_mail($mail, 'Oppdatering av innhold', $body, $head);
  }
}
