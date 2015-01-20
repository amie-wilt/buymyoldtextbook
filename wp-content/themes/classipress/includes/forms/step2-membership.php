<?php
/**
 * This is step 2 of 3 for the membership purchase form
 * 
 * @package ClassiPress
 * @subpackage Membership
 * @author AppThemes
 *
 *
 */

?>

<div id="step2">

	<h2 class="dotted"><?php _e( 'Review Your Membership Purchase', APP_TD ); ?></h2>

	<img src="<?php echo appthemes_locate_template_uri('images/step2.gif'); ?>" alt="" class="stepimg" />

	<?php
		$extend = false;
		if ( ! isset( $active_membership->pack_id ) || empty( $active_membership->pack_id ) ) {
			$extend = false;
		} elseif ( $active_membership->pack_id == $membership->pack_id ) {
			$extend = true;
		} elseif ( appthemes_days_between_dates( $current_user->membership_expires ) > 0 ) {
			$extend = false;
	?>
			<div class="error" style="text-align:center;">
				<?php printf( __( 'Your Current Membership (%1$s)  will be canceled upon purchase. This membership still has %2$s days remaining.', APP_TD ), stripslashes( $active_membership->pack_name ), appthemes_days_between_dates( $current_user->membership_expires ) ); ?>
			</div>
		<?php } ?>

	<form name="mainform" id="mainform" class="form_step" action="" method="post" enctype="multipart/form-data">

		<ol>

			<li>
				<div class="labelwrapper"><label><strong><?php if ( $extend ) _e( 'Membership Renewal:', APP_TD ); else _e( 'Membership:', APP_TD ); ?></strong></label></div>
				<div id="active_membership_pack"><?php echo stripslashes( $membership->pack_name ); ?></div>
				<div class="clr"></div>
			</li>
			<li>
				<div class="labelwrapper"><label><strong><?php _e( 'Membership Benefit:', APP_TD ); ?></strong></label></div>
				<div id="active_membership_pack"><?php echo stripslashes( get_pack_benefit( $membership ) ); ?></div>
				<div class="clr"></div>
			</li>
			<li>
				<div class="labelwrapper"><label><strong><?php _e( 'Membership Length:', APP_TD ); ?></strong></label></div>
				<div id="active_membership_pack"><?php if ( $extend ) printf( __( '%s more days', APP_TD ), $membership->pack_duration ); else printf( __( '%s days', APP_TD ), $membership->pack_duration ); ?></div>
				<div class="clr"></div>
			</li>

			<?php if ( $extend ) { ?>
				<li>
					<div class="labelwrapper"><label><strong><?php _e( 'Previous Expiration:', APP_TD ); ?></strong></label></div>
					<div id="active_membership_pack"><?php echo appthemes_display_date( $current_user->membership_expires ); ?></div>
					<div class="clr"></div>
				</li>
				<li>
					<div class="labelwrapper"><label><strong><?php _e( 'New Expiration:', APP_TD ); ?></strong></label></div>
					<div id="active_membership_pack">
					<?php
						if ( $membership->pack_membership_price > 0 )
							echo appthemes_display_date( appthemes_mysql_date( $current_user->membership_expires, $membership->pack_duration ) );
						else
							echo appthemes_display_date( appthemes_mysql_date( current_time( 'mysql' ), $membership->pack_duration ) );
					?>
					</div>
					<div class="clr"></div>
				</li>
			<?php } ?>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Membership Purchase Fee:', APP_TD ); ?></label></div>
				<div id="review"><?php if ( $membership->pack_membership_price > 0 ) { appthemes_display_price( $membership->pack_membership_price ); } else { _e( 'FREE', APP_TD ); } ?></div>
				<div class="clr"></div>
			</li>

			<hr class="bevel-double" />
			<div class="clr"></div>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Total Amount Due:', APP_TD ); ?></label></div>
				<div id="review"><strong><?php if ( $membership->pack_membership_price > 0 ) appthemes_display_price( $membership->pack_membership_price ); else _e( '--', APP_TD ); ?></strong></div>
				<div class="clr"></div>
			</li>

			<?php
				if ( cp_payments_is_enabled() && $membership->pack_membership_price > 0 )
					do_action( 'appthemes_purchase_fields' );
			?>

		</ol>

		<div class="pad10"></div>

		<div class="license">
			<?php cp_display_message( 'terms_of_use' ); ?>
		</div>

		<div class="clr"></div>

		<p class="terms">
			<?php _e( 'By clicking the proceed button below, you agree to our terms and conditions.', APP_TD ); ?>
			<br />
			<?php _e( 'Your IP address has been logged for security purposes:', APP_TD ); ?> <?php echo appthemes_get_ip(); ?>
		</p>

		<p class="btn2">
			<input type="button" name="goback" class="btn_orange" value="<?php _e( 'Go back', APP_TD ); ?>" onclick="history.back()" />
			<input type="submit" name="step2" id="step2" class="btn_orange" value="<?php _e( 'Continue &rsaquo;&rsaquo;', APP_TD ); ?>" />
		</p>

		<input type="hidden" id="oid" name="oid" value="<?php echo $_POST['oid']; ?>" />
		<input type="hidden" id="pack" name="pack" value="<?php echo $pack_id; ?>" />
		<input type="hidden" id="total_cost" name="total_cost" value="<?php echo $membership->pack_membership_price; ?>" />
		<input type="hidden" id="cp_sys_userIP" name="cp_sys_userIP" value="<?php echo appthemes_get_ip(); ?>" />
		<input type="hidden" id="user_id" name="user_id" value="<?php echo $current_user->ID; ?>" />

	</form>

	<div class="clear"></div>

</div>
