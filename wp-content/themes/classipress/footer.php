<?php global $cp_options; ?>

<div class="footer">

		<div class="footer_menu">

				<div class="footer_menu_res">

						<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'container' => false, 'menu_id' => 'footer-nav-menu', 'depth' => 1, 'fallback_cb' => false ) ); ?>

						<div class="clr"></div>

				</div><!-- /footer_menu_res -->

		</div><!-- /footer_menu -->

		<div class="footer_main">

				<div class="footer_main_res">

						<div class="dotted">

								<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_footer') ) : else : ?> <!-- no dynamic sidebar so don't do anything --> <?php endif; ?>

								<div class="clr"></div>

						</div><!-- /dotted -->

						<p>&copy; <?php echo date_i18n('Y'); ?> <?php bloginfo('name'); ?>. <?php _e( 'All Rights Reserved.', APP_TD ); ?></p>

						<?php if ( $cp_options->twitter_username ) : ?>
								<a href="http://twitter.com/<?php echo $cp_options->twitter_username; ?>" class="twit" target="_blank" title="<?php _e( 'Twitter', APP_TD ); ?>"><?php _e( 'Twitter', APP_TD ); ?></a>
						<?php endif; ?>

						<div class="right">
								<p><a target="_blank" href="http://www.appthemes.com/themes/classipress/" title="Classified Ads Software"><?php _e( 'Classified Ads Software', APP_TD ); ?></a> | <?php _e( 'Powered by', APP_TD ); ?> <a target="_blank" href="http://wordpress.org/" title="WordPress">WordPress</a></p>
						</div>

						<?php cp_website_current_time(); ?>

						<div class="clr"></div>

				</div><!-- /footer_main_res -->

		</div><!-- /footer_main -->

</div><!-- /footer -->