<?php
/**
 * This is step 3 of 3 for the ad submission form
 *
 * @package ClassiPress
 * @subpackage New Ad
 * @author AppThemes
 *
 *
 */


global $current_user, $wpdb;

if ( ! isset( $errors ) )
	$errors = new WP_Error();

if ( cp_payments_is_enabled() )
	$errors = apply_filters( 'appthemes_validate_purchase_fields', $errors );

// now get all the ad values which we stored in an associative array in the db
// first we do a check to make sure this db session still exists and then we'll
// use this option array to create the new ad below
$advals = get_option( 'cp_'.$_POST['oid'] );
// check and make sure the form was submitted from step 2 and the hidden oid matches the oid in the db
// we don't want to create duplicate ad submissions if someone reloads their browser
if ( isset( $_POST['step2'] ) && isset( $advals['oid'] ) && ( $_POST['oid'] == $advals['oid'] ) && ! $errors->get_error_code() ) {
?>


	<div id="step3">

	<h2 class="dotted">
		<?php if ( cp_payments_is_enabled() && $advals['cp_sys_total_ad_cost'] > 0 ) _e( 'Final Step', APP_TD ); else _e( 'Ad Listing Received', APP_TD ); ?>
	</h2>

	<img src="<?php echo appthemes_locate_template_uri('images/step3.gif'); ?>" alt="" class="stepimg" />

	<div class="processlog">
		<?php
			// insert the ad and get back the post id
			$renew_id = ( isset( $_GET['renew'] ) ) ? $_GET['renew'] : false;
			$post_id = cp_add_new_listing( $advals, $renew_id );
		?>
	</div>
	<div class="thankyou">

		<?php

			// call in the selected payment gateway as long as the price isn't zero
			if ( cp_payments_is_enabled() && $advals['cp_sys_total_ad_cost'] > 0 ) {

				$order = appthemes_new_order();
				$order->add_item( CP_ITEM_LISTING, $advals['cp_sys_total_ad_cost'], $post_id );
				do_action( 'appthemes_create_order', $order );

				echo html( 'h3', __( 'Payment', APP_TD ) );
				echo html( 'p', __( 'Please wait while we redirect you to our payment page.', APP_TD ) );
				echo html( 'p', html( 'small', __( '(Click the button below if you are not automatically redirected within 5 seconds.)', APP_TD ) ) );
				cp_js_redirect( $order->get_return_url(), __( 'Continue to Payment', APP_TD ) );

			} else {

				// otherwise the ad was free and show the thank you page.
				// get the post status
				$the_post = get_post( $post_id ); 

				// check to see what the ad status is set to
				if ( $the_post->post_status == 'pending' ) {

					// send ad owner an email
					cp_owner_new_ad_email( $post_id );

					echo html( 'h3', __( 'Thank you! Your ad listing has been submitted for review.', APP_TD ) );
					echo html( 'p', __( 'You can check the status by viewing your dashboard.', APP_TD ) );

				} else {

					echo html( 'h3', __( 'Thank you! Your ad listing has been submitted and is now live.', APP_TD ) );
					echo html( 'p', __( 'Visit your dashboard to make any changes to your ad listing or profile.', APP_TD ) );
					echo html( 'a', array( 'href' => get_permalink( $post_id ) ), __( 'View your new ad listing.', APP_TD ) );

				}

			}

			// send new ad notification email to admin
			if ( $cp_options->new_ad_email )
				cp_new_ad_email( $post_id );

			// remove the temp session option from the database
			delete_option( 'cp_'.$_POST['oid'] );
		?>

	</div> <!-- /thankyou -->

	</div>

<?php } else { ?>

	<h2 class="dotted"><?php _e( 'An Error Has Occurred', APP_TD ); ?></h2>

	<div class="thankyou">

		<?php
			if ( $errors->get_error_code() ) {
				$error_html = '';
				foreach ( $errors->errors as $error ) {
					$error_html .= html( 'li', $error[0] );
				}
				echo html( 'ul', array( 'class' => 'errors' ), $error_html );
			?>

			<p><?php printf( __( 'Please <a href="#" %s>go back</a> and fix the error(s).', APP_TD ), "onclick='history.go(-1);return false;'" ); ?></p>

		<?php } else { ?>
			<p><?php _e( 'Your session has expired or you are trying to submit a duplicate ad. Please start over.', APP_TD ); ?></p>
		<?php } ?>

	</div>

<?php } ?>

	<div class="pad100"></div>

