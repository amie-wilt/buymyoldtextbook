<?php
/**
 * This is step 3 of 3 for the ad submission form
 * 
 * @package ClassiPress
 * @subpackage Membership
 * @author AppThemes
 *
 *
 */

//check to make sure the user has an order already setup, othrewise the page was refreshed or page hack was attempted
if ( $cp_user_recent_order ) {
?>

<div id="step3">

	<h2 class="dotted">
		<?php if ( cp_payments_is_enabled() && $order['total_cost'] > 0 ) { _e( 'Final Step', APP_TD ); } else { _e( 'Membership Updated', APP_TD ); } ?>
	</h2>

	<img src="<?php echo appthemes_locate_template_uri('images/step3.gif'); ?>" alt="" class="stepimg" />

	<div class="thankyou">

	<?php
		// call in the selected payment gateway as long as the price isn't zero
		if ( cp_payments_is_enabled() && $order['total_cost'] > 0 ) {

			$membership_order = appthemes_new_order();
			$membership_order->add_item( CP_ITEM_MEMBERSHIP, $order['total_cost'] );
			do_action( 'appthemes_create_order', $membership_order );

			echo html( 'h3', __( 'Payment', APP_TD ) );
			echo html( 'p', __( 'Please wait while we redirect you to our payment page.', APP_TD ) );
			echo html( 'p', html( 'small', __( '(Click the button below if you are not automatically redirected within 5 seconds.)', APP_TD ) ) );
			cp_js_redirect( $membership_order->get_return_url(), __( 'Continue to Payment', APP_TD ) );

		//process the "free" orders on this page
		} else { 
			$order_processed = appthemes_process_membership_order( $current_user, $order );
			//send email to user
			if ( $order_processed )
				cp_owner_activated_membership_email( $current_user, $order_processed );
	?>

				<h3><?php _e( 'Your order has been completed and your membership status should now be active.', APP_TD ); ?></h3>

				<p><?php _e( 'Visit your dashboard to review your membership status details.', APP_TD ); ?></p>

				<ul class="membership-pack">
					<li><strong><?php _e( 'Membership Pack:', APP_TD ); ?></strong> <?php echo stripslashes($order_processed['pack_name']); ?></li>
					<li><strong><?php _e( 'Membership Expires:', APP_TD ); ?></strong> <?php echo appthemes_display_date($order_processed['updated_expires_date']); ?></li>
				</ul>

				<div class="pad50"></div>

			<?php do_action('appthemes_after_membership_confirmation'); ?>

		<?php
			// remove the order option from the database because the free order was processed
			delete_option($cp_user_recent_order);
	
		}

	?>

	</div> <!-- /thankyou -->

</div>

<?php } else { ?>

	<h2 class="dotted"><?php _e( 'An Error Has Occurred', APP_TD ); ?></h2>

	<div class="thankyou">
		<p><?php _e( 'Your session or order has expired or we cannot cannot find your order in our systems. Please start over to create a valid membership order.', APP_TD ); ?></p>
	</div>

<?php } ?>

	<div class="pad100"></div>
