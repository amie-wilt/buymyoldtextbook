<?php
/**
 * This is step 1 of 2 for the membership purchase submission form
 *
 * @package ClassiPress
 * @subpackage Purchase Membership
 * @author AppThemes
 *
 *
 */

?>


<div id="step1">

	<h2 class="dotted"><?php _e( 'Purchase a Membership Pack', APP_TD ); ?></h2>

	<img src="<?php echo appthemes_locate_template_uri('images/step1.gif'); ?>" alt="" class="stepimg" />

	<?php
		// display the custom message
		cp_display_message( 'membership_form_help' );

		if ( isset( $_GET['membership'] ) && $_GET['membership'] == 'required' ) {
	?>

			<p class="info">
			<?php
				if ( ! empty( $_GET['cat'] ) && $_GET['cat'] != 'all' ) {
					$category_id = appthemes_numbers_only( $_GET['cat'] );
					$category = get_term_by( 'term_id ', $category_id, APP_TAX_CAT );
					if ( $category ) {
						$term_link = html( 'a', array( 'href' => get_term_link( $category, APP_TAX_CAT ), 'title' => $category->name ), $category->name );
						printf( __( 'Membership is currently required in order to post to category %s.', APP_TD ), $term_link );
					}
				} else {
					_e( 'Membership is currently required.', APP_TD );
				}
			?>
			</p>

		<?php } ?>
 
	<p class="dotted">&nbsp;</p>

	<form name="mainform" id="mainform" class="form_membership_step" action="" method="post" enctype="multipart/form-data">

		<?php
			$sql = "SELECT * FROM $wpdb->cp_ad_packs WHERE pack_status = 'active_membership' ORDER BY pack_id asc";
			$results = $wpdb->get_results( $sql );
		?>

		<div id="membership-packs" class="wrap">

			<table id="memberships" class="widefat fixed footable">

				<thead style="text-align:left;">
					<tr>
						<th scope="col" data-class="expand"><?php _e( 'Name', APP_TD ); ?></th>
						<th scope="col" data-hide="phone"><?php _e( 'Membership Benefit', APP_TD ); ?></th>
						<th scope="col" data-hide="phone"><?php _e( 'Subscription', APP_TD ); ?></th>
						<th scope="col" style="width:75px;" data-hide="phone"></th>
					</tr>
				</thead>

			<?php
				if ( $results ) {
			?>

					<tbody id="list">

					<?php
						foreach ( $results as $result ) {
							// external plugins can modify or disable field
							$result = apply_filters( 'cp_package_field', $result, 'membership' );
							if ( ! $result )
								continue;

							$rowclass = 'even';
							$requiredClass = '';
							$benefit = get_pack_benefit( $result );
							if ( stristr( $result->pack_type, 'required' ) )
								$requiredClass = 'required';
					?>

							<tr class="<?php echo $rowclass . ' ' . $requiredClass; ?>">
								<td><strong><?php echo stripslashes( $result->pack_name ); ?></strong><a class="tip" tip="<?php echo $result->pack_desc; ?>" tabindex="99"><div class="helpico"></div></a></td>
								<td><?php echo $benefit; ?></td>
								<td><?php printf( __( '%1$s / %2$s days', APP_TD ), appthemes_get_price( $result->pack_membership_price ), $result->pack_duration ); ?></td>
								<td><input type="submit" name="step1" id="step1" class="btn_orange" onclick="document.getElementById('pack').value=<?php echo $result->pack_id; ?>;" value="<?php _e( 'Buy Now &rsaquo;&rsaquo;', APP_TD ); ?>" style="margin-left: 5px; margin-bottom: 5px;" /></td>
							</tr>

					<?php
						} // end for each
					?>

					</tbody>

			<?php
				} else {
			?>

					<tr>
						<td colspan="7"><?php _e( 'No membership packs found.', APP_TD ); ?></td>
					</tr>

			<?php
				} // end $results
			?>

			</table>

		</div><!-- end wrap for membership packs-->

		<input type="hidden" id="oid" name="oid" value="<?php echo $order_id; ?>" />
		<input type="hidden" id="pack" name="pack" value="<?php if ( isset( $_POST['pack'] ) ) echo $_POST['pack']; ?>" />

	</form>

</div>
