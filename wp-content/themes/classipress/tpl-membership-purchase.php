<?php
/*
Template Name: Membership Pack Purchases
*/


global $current_user;
$current_user = wp_get_current_user();

if ( ! isset( $errors ) )
	$errors = new WP_Error();

// get information about current membership
$active_membership = isset( $current_user->active_membership_pack ) ? get_pack( $current_user->active_membership_pack ) : false;

//get any existing orders
$cp_user_orders = get_user_orders( $current_user->ID );
$cp_user_recent_order = ( $cp_user_orders ) ? $cp_user_orders[0] : false;

if ( isset( $_POST['step1'] ) || isset( $_POST['step2'] ) ) {

	if ( isset( $_POST['pack'] ) ) {
		$pack_id = appthemes_numbers_only( $_POST['pack'] );
		$membership = get_pack( $pack_id );
		if ( ! $membership )
			$errors->add( 'invalid-pack-id', __( 'Choosen membership package does not exist.', APP_TD ) );
	} else {
		$errors->add( 'missed-pack', __( 'You need to choose membership package.', APP_TD ) );
	}

	if ( ! isset( $_POST['oid'] ) || $_POST['oid'] != appthemes_numbers_letters_only( $_POST['oid'] ) )
		$errors->add( 'invalid-order-id', __( 'Membership order ID is invalid.', APP_TD ) );

}
?>


<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<!-- full block -->
			<div class="shadowblock_out">

				<div class="shadowblock">

				<?php if ( $cp_options->enable_membership_packs ) { ?>

					<?php
						// check and make sure the form was submitted from step1 and the session value exists
						if ( isset( $_POST['step1'] ) && ! $errors->get_error_code() ) {

							include_once( get_template_directory() . '/includes/forms/step2-membership.php' );

						} elseif ( isset( $_POST['step2'] ) && ! $errors->get_error_code() ) {

							$errors = apply_filters( 'cp_membership_validate_fields', $errors );
							if ( cp_payments_is_enabled() )
								$errors = apply_filters( 'appthemes_validate_purchase_fields', $errors );

							if ( ! $errors->get_error_code() ) {
								//now put the array containing all the post values into the database
								$order = array();
								$order['user_id'] = $current_user->ID;
								$order['order_id'] = $_POST['oid'];
								$order['option_order_id'] = 'cp_order_' . $current_user->ID . '_' . $_POST['oid'];
								$order['pack_type'] = 'membership';
								$order['total_cost'] = $membership->pack_membership_price;

								//save the order for use when payment is completed
								$order = array_merge( $order, (array) $membership );
								update_option( $order['option_order_id'], $order );
								$cp_user_orders = get_user_orders( $current_user->ID );
								$cp_user_recent_order = ( $cp_user_orders ) ? $cp_user_orders[0] : false;
								include_once( get_template_directory() . '/includes/forms/step3-membership.php' );
							}

						} elseif ( ! $errors->get_error_code() ) {

							// create a unique ID for this new ad order
							if ( $cp_user_recent_order ) {
								$order_id = get_order_id( $cp_user_recent_order );
							} else {
								$order_id = cp_generate_id( 'membership' );
							}
							include_once( get_template_directory() . '/includes/forms/step1-membership.php' );

						} ?>

				<?php } else { ?>

					<h2 class="dotted"><?php _e( 'Membership Not Enabled', APP_TD ); ?></h2>

					<div class="info">
						<p><?php _e( 'Administrator currently has memberships disabled. Please try again later.', APP_TD ); ?></p>
					</div>

					<div class="pad100"></div>

				<?php } ?>

				<?php if ( $errors->get_error_code() ) { ?>

					<h2 class="dotted"><?php _e( 'An Error Has Occurred', APP_TD ); ?></h2>

					<div class="thankyou">
						<?php
							$error_html = '';
							foreach ( $errors->errors as $error ) {
								$error_html .= html( 'li', $error[0] );
							}
							echo html( 'ul', array( 'class' => 'errors' ), $error_html );
						?>

						<p><?php printf( __( 'Please <a href="#" %s>go back</a> and fix the error(s).', APP_TD ), "onclick='history.go(-1);return false;'" ); ?></p>
					</div>

				<?php } ?>

				</div><!-- /shadowblock -->

			</div><!-- /shadowblock_out -->

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
