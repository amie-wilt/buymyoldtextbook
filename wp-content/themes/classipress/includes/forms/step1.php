<?php
/**
 * This is step 1 of 3 for the ad submission form
 *
 * @package ClassiPress
 * @subpackage New Ad
 * @author AppThemes
 *
 *
 */

global $current_user, $wpdb;
$change_cat_url = add_query_arg( array( 'action' => 'change' ) );
?>

<div id="step1">

	<h2 class="dotted"><?php _e( 'Submit Your Listing', APP_TD ); ?></h2>

	<img src="<?php echo appthemes_locate_template_uri('images/step1.gif'); ?>" alt="" class="stepimg" />

	<?php
		do_action( 'appthemes_notices' );
		// display the custom message
		cp_display_message( 'ads_form_help' );
	?>

	<p class="dotted">&nbsp;</p>

	<?php
		// show the category dropdown when first arriving to this page.
		// Also show it if cat equals -1 which means the 'select one' option was submitted
		if ( !isset($_POST['cat']) || ($_POST['cat'] == '-1') ) {
	?>

	<form name="mainform" id="mainform" class="form_step" action="" method="post">

		<ol>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Cost Per Listing:', APP_TD ); ?></label></div>
				<?php cp_cost_per_listing(); ?>
				<div class="clr"></div>
			</li>

			<?php
				$category = ( isset( $_GET['renew'] ) ) ? true : false;
				if ( $category ) {
					$terms = wp_get_post_terms( $_GET['renew'], APP_TAX_CAT );
					$category = ( ! empty( $terms ) ) ? $terms[0] : false;
				}

				if ( ! isset( $_GET['renew'] ) || ! $category || ( isset( $_GET['action'] ) && $_GET['action'] == 'change' ) ) {
			?>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Select a Category:', APP_TD ); ?></label></div>
				<div id="ad-categories" style="display:block; margin-left:170px;">						
					<div id="catlvl0">
						<?php
							$args = array(
								'show_option_none' => __( 'Select one', APP_TD ),
								'class' => 'dropdownlist',
								'id' => 'ad_cat_id',
								'orderby' => 'name',
								'order' => 'ASC',
								'hide_empty' => 0,
								'hierarchical' => 1,
								'depth' => 1,
								'taxonomy' => APP_TAX_CAT,
							);

							if ( $cp_options->price_scheme == 'category' && cp_payments_is_enabled() && $cp_options->ad_parent_posting != 'no' ) {
								cp_dropdown_categories_prices( $args );
							} else {
 								wp_dropdown_categories( $args );
							}

						?>
						<div style="clear:both;"></div>
					</div>
				</div>
				<div id="ad-categories-footer" class="button-container">
					<input type="submit" name="getcat" id="getcat" class="btn_orange" value="<?php _e( 'Go &rsaquo;&rsaquo;', APP_TD ); ?>" />
					<div id="chosenCategory"><input id="ad_cat_id" name="cat" type="input" value="-1" /></div>
					<div style="clear:both;"></div>
				</div>
				<div style="clear:both;"></div>
			</li>

			<?php
				} else {
					if ( $cp_options->price_scheme == 'category' && cp_payments_is_enabled() ) {
						$prices = $cp_options->price_per_cat;
						$cat_fee = ( isset( $prices[ $category->term_id ] ) ) ? (float) $prices[ $category->term_id ] : 0;
						$cat_fee = ' - ' . appthemes_get_price( $cat_fee );
					} else {
						$cat_fee = '';
					}
			?>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Category:', APP_TD ); ?></label></div>
				<strong><?php echo $category->name; ?></strong><?php echo $cat_fee; ?>&nbsp;&nbsp;<small><a href="<?php echo $change_cat_url; ?>"><?php _e( '(change)', APP_TD ); ?></a></small>
				<div class="clr pad5"></div>
				<div class="button-container">
					<input id="ad_cat_id" name="cat" type="hidden" value="<?php echo $category->term_id; ?>" />
					<input type="submit" name="getcat" class="btn_orange" value="<?php _e( 'Go &rsaquo;&rsaquo;', APP_TD ); ?>" />
				</div>
			</li>

			<?php } ?>

		</ol>

	</form>


	<?php } else {
		// show the form based on the category selected
		// get the cat nice name and put it into a variable
		$category_id = appthemes_numbers_only( $_POST['cat'] );
		$category = get_term_by( 'id', $category_id, APP_TAX_CAT );
		//$_POST['catname'] = $ad_category->name;
	?>

	<form name="mainform" id="mainform" class="form_step" action="" method="post" enctype="multipart/form-data">

		<ol>

			<li>
				<div class="labelwrapper"><label><?php _e( 'Category:', APP_TD ); ?></label></div>
				<strong><?php echo $category->name; ?></strong>&nbsp;&nbsp;<small><a href="<?php echo $change_cat_url; ?>"><?php _e( '(change)', APP_TD ); ?></a></small>
			</li>

			<?php
				$renew_id = ( isset( $_GET['renew'] ) ) ? $_GET['renew'] : false;
				$renew = ( $renew_id ) ? get_post( $renew_id ) : false;
				echo cp_show_form( $category_id, $renew );
			?>

			<p class="btn1">
				<input type="submit" name="step1" id="step1" class="btn_orange" value="<?php _e( 'Continue &rsaquo;&rsaquo;', APP_TD ); ?>" />
			</p>

		</ol>

		<input type="hidden" id="ad_cat_id" name="cat" value="<?php echo esc_attr( $category_id ); ?>" />
		<input type="hidden" id="catname" name="catname" value="<?php echo esc_attr( $category->name ); ?>" />
		<input type="hidden" id="fid" name="fid" value="<?php if ( isset( $_POST['fid'] ) ) echo esc_attr( $_POST['fid'] ); ?>" />
		<input type="hidden" id="oid" name="oid" value="<?php echo esc_attr( $order_id ); ?>" />

	</form>

	<?php } ?>

</div>
