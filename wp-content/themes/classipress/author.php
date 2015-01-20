<?php
/**
 * Author profile page
 *
 */

//This sets the $curauth variable
$curauth = get_queried_object();

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$author_posts_count = count_user_posts( $curauth->ID );
?>

<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<div id="breadcrumb">
				<?php if ( function_exists('cp_breadcrumb') ) cp_breadcrumb(); ?>
			</div>

			<!-- left block -->
			<div class="content_left">

				<div class="shadowblock_out">

					<div class="shadowblock">

						<h1 class="single dotted"><?php printf( __( 'About %s', APP_TD ), $curauth->display_name ); ?></h1>

						<div class="post">

							<div id="user-photo"><?php appthemes_get_profile_pic($curauth->ID, $curauth->user_email, 96); ?></div>

							<div class="author-main">

								<ul class="author-info">
									<li><strong><?php _e( 'Member Since:', APP_TD ); ?></strong> <?php echo appthemes_display_date( $curauth->user_registered, 'date', true ); ?></li>
									<?php if ( !empty($curauth->user_url) ) { ?><li><strong><?php _e( 'Website:', APP_TD ); ?></strong> <a href="<?php echo esc_url($curauth->user_url); ?>"><?php echo strip_tags( $curauth->user_url ); ?></a></li><?php } ?>
									<?php if ( !empty($curauth->twitter_id) ) { ?><li><div class="twitterico"></div><a href="http://twitter.com/<?php echo urlencode($curauth->twitter_id); ?>" target="_blank"><?php _e( 'Twitter', APP_TD ); ?></a></li><?php } ?>
									<?php if ( !empty($curauth->facebook_id) ) { ?><li><div class="facebookico"></div><a href="<?php echo appthemes_make_fb_profile_url( $curauth->facebook_id ); ?>" target="_blank"><?php _e( 'Facebook', APP_TD ); ?></a></li><?php } ?>
								</ul>

								<?php cp_author_info( 'page' ); ?>

							</div>

							<h3 class="dotted"><?php _e( 'Description', APP_TD ); ?></h3>
							<p><?php echo nl2br( $curauth->user_description ); ?></p>

						</div><!--/post-->

					</div><!-- /shadowblock -->

				</div><!-- /shadowblock_out -->


				<div class="tabcontrol">

					<ul class="tabnavig">
						<li><a href="#block1"><span class="big"><?php _e( 'Ads', APP_TD ); ?></span></a></li>
						<?php if ( $author_posts_count ) { ?>
							<li><a href="#block2"><span class="big"><?php _e( 'Posts', APP_TD ); ?></span></a></li>
						<?php } ?>
					</ul>

					<!-- tab 1 -->
					<div id="block1">

						<div class="clr"></div>

						<div class="undertab"><span class="big"><?php _e( 'Ads', APP_TD ); ?> / <strong><span class="colour"><?php _e( 'Latest items listed', APP_TD ); ?></span></strong></span></div>

						<?php query_posts( array( 'post_type' => APP_POST_TYPE, 'author' => $curauth->ID, 'paged' => $paged ) ); ?>

						<?php get_template_part( 'loop', 'ad_listing' ); ?>

					</div><!-- /block1 -->


					<?php if ( $author_posts_count ) { ?>
					<!-- tab 2 -->
					<div id="block2">

						<div class="clr"></div>

						<div class="undertab"><span class="big"><?php _e( 'Posts', APP_TD ); ?> / <strong><span class="colour"><?php _e( 'Recent blog posts', APP_TD ); ?></span></strong></span></div>

						<?php query_posts( array( 'post_type' => 'post', 'author' => $curauth->ID, 'paged' => $paged ) ); ?>

						<?php get_template_part( 'loop' ); ?>

					</div><!-- /block2 -->
					<?php } ?>


				</div><!-- /tabcontrol -->

			</div><!-- /content_left -->

			<?php get_sidebar( 'author' ); ?>

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
