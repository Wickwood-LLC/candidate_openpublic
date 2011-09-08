<?php
// $Id$
/**
 * @file page.header.inc
 * Header template.
 * @ingroup page
 */
?>
<div id="page-wrapper" class="clearfix">
 
  <div id="header"> 
    <div id="header-inner" class="clearfix center-on-page">
      
      <div id="header-top-content" class="clearfix">
				<div class = 'title'>
					<?php if ($logo) {print $header_logo;} ?>
					<?php if ( $site_name || $page['header_top'] ) : ?>
						<div class = 'site-name'>
							<?php
							  if ( $page['header_top'] ) {
							    print render($page['header_top']);
							  } else {
							    print l($site_name, $front_page);
							  }
							?>
						</div>
					<?php endif; ?>
	
					<?php if ($site_slogan = variable_get('site_slogan')) : ?>
						<div class = 'site-slogan'><?php print $site_slogan; ?></div>
					<?php endif; ?>
				</div>
      </div><!--/header top content--> 
      
      <div id="header-bottom-content" class="clearfix">

        <?php if ( $page['header_bottom'] ) : ?>
        	<div id="header-search" class="clearfix">
          		<?php print render($page['header_bottom']); ?>
        	</div><!--/header search-->
        <?php endif; ?>
              
      </div><!--/header bottom content-->

    </div><!--/#header-inner-->
  </div> <!-- /.section, /#header -->
    
   <div id="wrap" class="center-on-page clearfix">
   <div id="wrap-inner" class="center-on-page clearfix">

    <?php if ($main_menu || $secondary_menu): ?>
    <div id="navigation"><div class="section">
      <?php print drupal_render($main_menu); ?>
      <?php //print theme('links__system_main_menu', array('links' => $main_menu, 'attributes' => array('id' => 'navlist', 'class' => array('links')))); ?>
          </div></div> <!-- /.section, /#navigation -->
  <?php endif; ?>
