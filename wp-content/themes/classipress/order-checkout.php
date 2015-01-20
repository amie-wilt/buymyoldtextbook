
<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<div id="breadcrumb"><?php cp_breadcrumb(); ?></div>

			<div class="shadowblock_out">

				<div class="shadowblock">

					<div class="post">

						<h1 class="single dotted"><?php _e( 'Order Summary', APP_TD ); ?></h1>

						<div class="order-summary">

							<?php the_order_summary(); ?>

							<form action="<?php the_order_return_url(); ?>" method="POST">
								<p><?php _e( 'Please select a method for processing your payment:', APP_TD ); ?></p>
								<?php appthemes_list_gateway_dropdown(); ?>
								<button class="btn_orange" type="submit"><?php _e( 'Submit', APP_TD ); ?></button>
							</form>

						</div>

						<div class="clr"></div>

					</div><!--/post-->

				</div><!-- /shadowblock -->

			</div><!-- /shadowblock_out -->

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
