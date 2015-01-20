<?php
/**
 * The featured slider on the home page
 *
 */
global $cp_options;
?>

<?php if ( $cp_options->enable_featured ) : ?>

	<script type="text/javascript">
		// <![CDATA[
		/* featured listings slider */
		jQuery(document).ready(function($) {
			$('.slider').jCarouselLite({
				btnNext: '.next',
				btnPrev: '.prev',
				visible: ( $(window).width() < 940 ) ? 4 : 5,
				pause: true,
				auto: true,
				timeout: 2800,
				speed: 1100,
				easing: 'easeOutQuint' // for different types of easing, see easing.js
			});
		});
		// ]]>
	</script>

	<?php appthemes_before_loop('featured'); ?>

	<?php if ( $featured = cp_get_featured_slider_ads() ) : ?>

		<!-- featured listings -->
		<div class="shadowblock_out slider_top">

			<div class="shadowblockdir">

				<h2 class="dotted"><?php _e( 'Featured Listings', APP_TD ); ?></h2>

				<div class="sliderblockdir">

					<div class="prev"></div>

					<div id="sliderlist">

						<div class="slider">

							<ul>

								<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>

									<?php appthemes_before_post('featured'); ?>

									<li>

										<span class="feat_left">

											<?php if ( $cp_options->ad_images ) cp_ad_featured_thumbnail(); ?>
											
											<span class="price_sm"><?php cp_get_price($post->ID, 'cp_price'); ?></span>

										</span>

										<?php appthemes_before_post_title('featured'); ?>

										<p><a href="<?php the_permalink(); ?>"><?php if ( mb_strlen( get_the_title() ) >= $cp_options->featured_trim ) echo mb_substr( get_the_title(), 0, $cp_options->featured_trim ).'...'; else the_title(); ?></a></p>

										<?php appthemes_after_post_title('featured'); ?>

									</li>

									<?php appthemes_after_post('featured'); ?>

								<?php endwhile; ?>

								<?php appthemes_after_endwhile('featured'); ?>

							</ul>

						</div>

					</div><!-- /slider -->

					<div class="next"></div>

					<div class="clr"></div>

				</div><!-- /sliderblock -->

			</div><!-- /shadowblock -->

		</div><!-- /shadowblock_out -->

	<?php endif; ?>

	<?php appthemes_after_loop('featured'); ?>

	<?php wp_reset_postdata(); ?>

<?php endif; // end feature ad slider check ?>

