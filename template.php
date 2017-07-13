<?php
function bb2html($text) {
  $bbcode = array(
    "[strong]", "[/strong]",
    "[b]", "[/b]",
    "[u]", "[/u]",
    "[i]", "[/i]",
    "[em]", "[/em]",
    "[amp]", "[theta]", "[degree]", "[prime]", "[doubleprime]", "[squareroot]",
    "[br]"
  );
  $htmlcode = array(
    "<strong>", "</strong>",
    "<strong>", "</strong>",
    "<u>", "</u>",
    "<em>", "</em>",
    "<em>", "</em>",
    "&amp;", "&theta;", "&#176;", "&prime;", "&Prime;", "&radic;",
    "<br>"
  );
  return str_replace($bbcode, $htmlcode, $text);
}

function bb_strip($text) {
  $bbcode = array(
    "[strong]", "[/strong]",
    "[b]", "[/b]",
    "[u]", "[/u]",
    "[i]", "[/i]",
    "[em]", "[/em]",
    "&amp;", "&theta;", "&#176;", "&prime;", "&Prime;", "&radic;",
    "[br]"
  );
  return str_replace($bbcode, '', $text);
}

function bb2cleantitle($text) {
  $bbcode = array(
    "[strong]", "[/strong]",
    "[b]", "[/b]",
    "[u]", "[/u]",
    "[i]", "[/i]",
    "[em]", "[/em]",
    "[amp]", "[theta]", "[degree]", "[prime]", "[doubleprime]", "[squareroot]",
    "[br]"
  );
  $stripcode = array(
    "", "",
    "", "",
    "", "",
    "", "",
    "", "",
    "&amp;", "&theta;", "&#176;", "&prime;", "&Prime;", "&radic;",
    " "
  );
  return str_replace($bbcode, $stripcode, $text);
}

function candidate_openpublic_preprocess_html(&$variables) {
  // Add conditional stylesheets for IE
  //drupal_add_css(path_to_theme() . '/css/ie8.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 8', '!IE' => FALSE), 'preprocess' => FALSE));
  //drupal_add_css(path_to_theme() . '/css/ie7.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'preprocess' => FALSE));
  //drupal_add_css(path_to_theme() . '/css/ie6.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 6', '!IE' => FALSE), 'preprocess' => FALSE));
}

/*
 * implement hook_preprocess_node
 *
 * we are turning off number of reads
 */
function candidate_openpublic_preprocess_node(&$variables) {
  unset($variables['content']['links']['statistics']);
}

function candidate_openpublic_preprocess_page(&$variables) {

  // we have two cache for the utility menu one if logged in one if logged out.
  // also we change the name of the login if we are already logged in.
  global $user;
  $menu_utility_cache = $user->uid ? cache_get("menu_utility") : cache_get("menu_utility_anon") ;
  if($menu_utility_cache) {
    $menu_utility = $menu_utility_cache->data;
  }
  else {
    $menu_utility = menu_navigation_links('menu-utility');
    if ($user->uid) {
      foreach($menu_utility as $key => $item) {
        if ($item['href'] == 'user') {
        $menu_utility[$key]['title'] ='My Account';
        }
      }
    }
    $menu_utility = theme(
      'links',
      array(
        'links' => $menu_utility,
        'attributes' => array(
          'id' => 'user-menu',
          'class' => array('links', 'clearfix'),
        ),
        'heading' => array(
          'text' => t('User menu'),
          'level' => 'h2',
          'class' => array('element-invisible'),
        ),
      )
    );
    cache_set( $user->uid ? "menu_utility" : "menu_utility_anon" , $menu_utility);
  }
  $variables['menu_utility'] = $menu_utility;

  $footer_utility_cache = cache_get("footer_utility") ;
  if($footer_utility_cache) {
    $footer_utility = $footer_utility_cache->data;
  }
  else {
    $footer_utility = menu_navigation_links('menu-footer-utility');
    $footer_utility = theme(
      'links',
      array(
        'links' => $footer_utility,
        'attributes' => array(
          'id' => 'footer-utility',
          'class' => array('links', 'clearfix'),
        ),
        'heading' => array(
          'text' => t('Utility Links'),
          'level' => 'h2',
          'class' => array('element-invisible'),
        ),
      )
    );
    cache_set("footer_utility", $footer_utility);
  }
  $variables['footer_utility'] = $footer_utility;

  // We are caching the footer_menu render array for performance
  $footer_menu_cache = cache_get("footer_menu_data") ;
  if ($footer_menu_cache) {
    $footer_menu = $footer_menu_cache->data;
  }
  else {
    $footer_menu = menu_tree_output(menu_build_tree('main-menu', array('max_depth'=>2)));
    cache_set("footer_menu_data", $footer_menu);
  }
  //set the active trail
  $active_trail = menu_get_active_trail();
  foreach($active_trail as $trail) {
    if (isset($trail['mlid']) && isset($footer_menu[$trail['mlid'] ] )) {
      $footer_menu[$trail['mlid']]['#attributes']['class'][] = 'active-trail';
    }
  }
  $variables['footer_menu'] = $footer_menu;
  $variables['main_menu'] = $footer_menu;


  $frontpage = variable_get('site_frontpage', 'node');

  $logo = $variables['logo'];
  $site_name = $variables['site_name'];
  if (preg_match("|^.*/files/(.*)|", $logo, $m)) {
    $file = "public://" . $m[1];
    $header_logo = l(theme('image_style', array('style_name'=>'logo', 'path'=>$file, 'alt'=>"$site_name logo")), '', array("html"=>TRUE, 'attributes'=>array('class'=>'logo')));
    $footer_logo = l(theme('image_style', array('style_name'=>'logo-small', 'path'=>$file, 'alt'=>"$site_name logo")), '', array("html"=>TRUE, 'attributes'=>array('class'=>'logo')));
  }
  elseif ($logo == url(drupal_get_path('theme', 'candidate_openpublic') . "/logo.png", array('absolute'=>TRUE))) {
    $header_logo = l(theme('image', array('path'=>$logo, 'alt'=>"$site_name logo")), '', array("html"=>TRUE, 'attributes'=>array('class'=>'logo')));
    $footer_logo = l(theme('image', array('path'=>drupal_get_path('theme', 'candidate_openpublic') . "/logo-sm.png", 'alt'=>"$site_name logo")), '', array("html"=>TRUE, 'attributes'=>array('class'=>'logo')));
  }
  else {
    $header_logo = l(theme('image', array('path'=>$logo, 'alt'=>"$site_name logo")), '', array("html"=>TRUE, 'attributes'=>array('class'=>'logo')));
    $footer_logo = l(theme('image', array('path'=>$logo, 'alt'=>"$site_name logo")), '', array("html"=>TRUE, 'attributes'=>array('class'=>'logo')));
  }

  $variables['footer_logo'] = $footer_logo;
  $variables['header_logo'] = $header_logo;
  $variables['front_page'] = $frontpage;
  if(function_exists('defaultcontent_get_node') &&
     ($node = defaultcontent_get_node("email_update")) ) {
    $node = node_view($node);
    $variables['subscribe_form'] = $node['webform'];
  }
}

/**
 *  Preprocess function for home page feature rotator
 */
function candidate_openpublic_preprocess_views_view_fields(&$vars) {
  if ($vars['view']->name == 'home_page_feature_rotator' && $vars['view']->current_display == 'block_1') {
    drupal_add_css(drupal_get_path("theme", 'candidate_openpublic') . "/css/home-page-rotator.css", 'file');
    drupal_add_js(drupal_get_path("theme", 'candidate_openpublic') . "/js/jquery.cycle.min.js", 'file');
    $result_count = sizeof($vars['view']->result);
    if($result_count > 1) {
      drupal_add_js('
        function homepage_feature_rotator_rotate_slide(slide_no) {
          jQuery("#home-rotator").cycle("pause");
          jQuery("#home-rotator").cycle(slide_no);
        }
      ', 'inline');

      drupal_add_js(
        'jQuery("#home-rotator").cycle({
          fx:     "fade",
          speed:   600,
          timeout: 4000,
          cleartypeNoBg: 1,
          height: "auto",
          width: "auto",
          slideResize: 0,
          pause:   true,
          pauseOnPagerHover: 1
        });',
        array('type' => 'inline', 'scope' => 'footer')
      );
    }
    else {
      drupal_add_css(
        '.home-rotator-slide {
          display: block;
          margin: 0;
        }
        ',
        array('type' => 'inline', 'group' => CSS_THEME, 'weight' => 30)
      );
    }

    $nav = '';
    $counter = 0;
    $row = $vars['row'];

    $vars['title'] = filter_xss($row->node_title);

    $vars['main_image'] = filter_xss(str_ireplace('alt=""', 'alt="' . check_plain($vars['title']) . ' ' . t('feature image') . '"', $vars['fields']['entity_id_1']->content), array('a', 'img'));

    foreach ($vars['view']->result as $id => $node) {
      $active_slide = '';
      if ($node->nid == $row->nid) {
        $active_slide = 'class="activeSlide"';
      }
      $nav .= '<li><a href="#" onclick="homepage_feature_rotator_rotate_slide(' . $counter . '); return false;" ' . $active_slide . '>' . ($counter+1) . '</a></li>';
      $counter++;
    }

    $vars['summary'] = filter_xss($vars['fields']['entity_id']->content, array('div'));
    $vars['read_more'] = filter_xss($vars['fields']['entity_id_3']->content);

    if ($vars['read_more']) {
      $vars['main_image'] = l($vars['main_image'], $vars['read_more'], array('html' => TRUE));
    }

    if($result_count < 2) {
      $nav = '';
    }
    $vars['rotator_nav'] = $nav;
  }
}

/**
 *  Preprocess function for home page breaking new view.
 */
function candidate_openpublic_preprocess_views_view(&$vars) {
  if ($vars['view']->name == 'breaking_news' && $vars['view']->current_display == 'block_1') {
    drupal_add_css(drupal_get_path("theme", 'candidate_openpublic') . "/css/breaking_news.css", 'file');
  }
}
