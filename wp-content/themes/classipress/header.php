<?php global $cp_options; ?>

<div class="header">

		<div class="header_top">

				<div class="header_top_res">

						<p>
								<?php echo cp_login_head(); ?>

								<a href="<?php echo appthemes_get_feed_url(); ?>" class="srvicon rss-icon" target="_blank" title="<?php _e( 'RSS Feed', APP_TD ); ?>"><?php _e( 'RSS Feed', APP_TD ); ?></a>

								<?php if ( $cp_options->facebook_id ) { ?>
									&nbsp;|&nbsp;<a href="<?php echo appthemes_make_fb_profile_url( $cp_options->facebook_id ); ?>" class="srvicon facebook-icon" target="_blank" title="<?php _e( 'Facebook', APP_TD ); ?>"><?php _e( 'Facebook', APP_TD ); ?></a>
								<?php } ?>

								<?php if ( $cp_options->twitter_username ) { ?>
									&nbsp;|&nbsp;<a href="http://twitter.com/<?php echo $cp_options->twitter_username; ?>" class="srvicon twitter-icon" target="_blank" title="<?php _e( 'Twitter', APP_TD ); ?>"><?php _e( 'Twitter', APP_TD ); ?></a>
								<?php } ?>
						</p>

				</div><!-- /header_top_res -->

		</div><!-- /header_top -->


		<div class="header_main">

				<div class="header_main_bg">

						<div class="header_main_res">

								<div id="logo">

										<?php if ( $cp_options->use_logo ) { ?>

												<?php if ( $cp_options->logo ) { ?>
														<a href="<?php echo home_url('/'); ?>"><img src="<?php echo $cp_options->logo; ?>" alt="<?php bloginfo('name'); ?>" class="header-logo" /></a>
												<?php } else { ?>
														<a href="<?php echo home_url('/'); ?>"><div class="cp_logo"></div></a>
												<?php } ?>

										<?php } else { ?>

												<h1><a href="<?php echo home_url('/'); ?>"><?php bloginfo('name'); ?></a></h1>
												<div class="description"><?php bloginfo('description'); ?></div>

										<?php } ?>

								</div><!-- /logo -->

								<div class="adblock">
									<?php appthemes_advertise_header(); ?>
								</div><!-- /adblock -->

								<div class="clr"></div>

						</div><!-- /header_main_res -->

				</div><!-- /header_main_bg -->

		</div><!-- /header_main -->


		<div class="header_menu">

				<div class="header_menu_res">

                <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'menu-header', 'fallback_cb' => false, 'container' => false ) ); ?>

                <a href="<?php echo CP_ADD_NEW_URL; ?>" class="obtn btn_orange"><?php _e( 'Sell Your Book', APP_TD ); ?></a>

                <div class="clr"></div>

    
				</div><!-- /header_menu_res -->

		</div><!-- /header_menu -->

</div><!-- /header -->