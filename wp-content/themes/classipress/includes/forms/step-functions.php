<?php

/**
 * This creates all the fields and assembles them
 * on the ad form page based on either custom forms
 * built by the admin or it just defaults to a
 * standard form which has been pre-defined.
 *
 * @global <type> $wpdb
 * @param <type> $results
 *
 * All custom options we want stored in WP and displayed on the ad detail page need to begin with cp_
 * All custom system options we want stored in WP and NOT displayed on the ad detail page need to begin with cp_sys_
 *
 */


// loops through the custom fields and builds the custom ad form
if ( !function_exists('cp_formbuilder') ) {
	function cp_formbuilder($results, $post = false) {
		global $wpdb, $cp_options;

		$custom_fields_array = array();

		foreach ( $results as $result ) {

			// external plugins can modify or disable field
			$result = apply_filters( 'cp_formbuilder_field', $result, $post );
			if ( ! $result )
				continue;

			if ( appthemes_str_starts_with( $result->field_name, 'cp_' ) )
				$custom_fields_array[] = $result->field_name;
			$post_meta_val = ( $post ) ? get_post_meta($post->ID, $result->field_name, true) : false;
	?>

			<li id="list_<?php echo esc_attr($result->field_name); ?>">
				<div class="labelwrapper">
					<label><?php if ( $result->field_tooltip ) { ?><a href="#" tip="<?php echo esc_attr( translate( $result->field_tooltip, APP_TD ) ); ?>" tabindex="999"><div class="helpico"></div></a><?php } ?><?php echo esc_html( translate( $result->field_label, APP_TD ) ); ?>: <?php if ( $result->field_req ) echo '<span class="colour">*</span>'; ?></label>
					<?php if ( ($result->field_type) == 'text area' && ( $cp_options->allow_html ) ) { // only show this for tinymce since it's hard to position the error otherwise ?>
						<br /><label class="invalid tinymce" for="<?php echo esc_attr($result->field_name); ?>"><?php _e( 'This field is required.', APP_TD ); ?></label>
					<?php } ?>
				</div>
				<?php

					switch ( $result->field_type ) {

						case 'text box':

							if ( isset($_POST[$result->field_name]) ) {
								$value = appthemes_clean( $_POST[$result->field_name] );
							} elseif ( $result->field_name == 'post_title' && $post ) {
								$value = $post->post_title;
							} elseif ( $result->field_name == 'tags_input' && $post ) {
								$value = rtrim(trim(cp_get_the_term_list($post->ID, APP_TAX_TAG)), ',');
							} else {
								$value = $post_meta_val;
							}

							$field_class = ( $result->field_req ) ? 'text required' : 'text';
							$field_minlength = ( empty( $result->field_min_length ) ) ? '2' : $result->field_min_length;
							$args = array( 'value' => $value, 'name' => $result->field_name, 'id' => $result->field_name, 'type' => 'text', 'class' => $field_class, 'minlength' => $field_minlength );
							$args = apply_filters( 'cp_formbuilder_' . $result->field_name, $args, $result, $post );

							echo html( 'input', $args );
							echo html( 'div', array( 'class' => 'clr' ) );

							break;

						case 'drop-down':

							$options = explode( ',', $result->field_values );
							$options = array_map( 'trim', $options );
							$html_options = '';

							$html_options .= html( 'option', array( 'value' => '' ), __( '-- Select --', APP_TD ) );
							foreach ( $options as $option ) {
								$args = array( 'value' => $option );
								if ( $option == $post_meta_val )
									$args['selected'] = 'selected';
								$args = apply_filters( 'cp_formbuilder_' . $result->field_name . '_option', $args, $result, $post );
								$html_options .= html( 'option', $args, $option );
							}

							$field_class = ( $result->field_req ) ? 'dropdownlist required' : 'dropdownlist';
							$args = array( 'name' => $result->field_name, 'id' => $result->field_name, 'class' => $field_class );
							$args = apply_filters( 'cp_formbuilder_' . $result->field_name, $args, $result, $post );

							echo html( 'select', $args, $html_options );
							echo html( 'div', array( 'class' => 'clr' ) );

							break;

						case 'text area':

							if ( isset($_POST[$result->field_name]) ) {
								$value = appthemes_clean( $_POST[$result->field_name] );
							} elseif ( $result->field_name == 'post_content' && $post ) {
								$value = $post->post_content;
							} else {
								$value = $post_meta_val;
							}

							$field_class = ( $result->field_req ) ? 'required' : '';
							$field_minlength = ( empty( $result->field_min_length ) ) ? '2' : $result->field_min_length;
							$args = array( 'value' => $value, 'name' => $result->field_name, 'id' => $result->field_name, 'rows' => '8', 'cols' => '40', 'class' => $field_class, 'minlength' => $field_minlength );
							$args = apply_filters( 'cp_formbuilder_' . $result->field_name, $args, $result, $post );
							$value = $args['value'];
							unset( $args['value'] );

							echo html( 'div', array( 'class' => 'clr' ) );
							echo html( 'textarea', $args, esc_textarea( $value ) );
							echo html( 'div', array( 'class' => 'clr' ) );
					?>

							<?php if ( $cp_options->allow_html && ! wp_is_mobile() ) { ?>
								<script type="text/javascript"> <!--
								tinyMCE.execCommand('mceAddControl', false, '<?php echo esc_attr($result->field_name); ?>');
								--></script>
							<?php } ?>

					<?php
							break;

						case 'radio':

							$options = explode( ',', $result->field_values );
							$options = array_map( 'trim', $options );

							$html_radio = '';
							$html_options = '';

							if ( ! $result->field_req ) {
								$args = array( 'value' => '', 'type' => 'radio', 'class' => 'radiolist', 'name' => $result->field_name, 'id' => $result->field_name );
								if ( empty( $post_meta_val ) )
									$args['checked'] = 'checked';
								$args = apply_filters( 'cp_formbuilder_' . $result->field_name, $args, $result, $post );
								$html_radio = html( 'input', $args ) . '&nbsp;&nbsp;' . __( 'None', APP_TD );
								$html_options .= html( 'li', array(), $html_radio );
							}

							foreach ( $options as $option ) {
								$field_class = ( $result->field_req ) ? 'radiolist required' : 'radiolist';
								$args = array( 'value' => $option, 'type' => 'radio', 'class' => $field_class, 'name' => $result->field_name, 'id' => $result->field_name );
								if ( $option == $post_meta_val )
									$args['checked'] = 'checked';
								$args = apply_filters( 'cp_formbuilder_' . $result->field_name, $args, $result, $post );
								$html_radio = html( 'input', $args ) . '&nbsp;&nbsp;' . $option;
								$html_options .= html( 'li', array(), $html_radio );
							}

							echo html( 'ol', array( 'class' => 'radios' ), $html_options );
							echo html( 'div', array( 'class' => 'clr' ) );

							break;

						case 'checkbox':

							$post_meta_val = ( $post ) ? get_post_meta($post->ID, $result->field_name, false) : array();
							$options = explode( ',', $result->field_values );
							$options = array_map( 'trim', $options );
							$optionCursor = 1;

							$html_checkbox = '';
							$html_options = '';

							foreach ( $options as $option ) {
								$field_class = ( $result->field_req ) ? 'checkboxlist required' : 'checkboxlist';
								$args = array( 'value' => $option, 'type' => 'checkbox', 'class' => $field_class, 'name' => $result->field_name . '[]', 'id' => $result->field_name . '_' . $optionCursor++ );
								if ( in_array($option, $post_meta_val) )
									$args['checked'] = 'checked';
								$args = apply_filters( 'cp_formbuilder_' . $result->field_name, $args, $result, $post );
								$html_checkbox = html( 'input', $args ) . '&nbsp;&nbsp;' . $option;
								$html_options .= html( 'li', array(), $html_checkbox );
							}

							echo html( 'ol', array( 'class' => 'checkboxes' ), $html_options );
							echo html( 'div', array( 'class' => 'clr' ) );

							break;

					}
					?>

			</li>

	<?php
		}

		// put all the custom field names into an hidden field so we can process them on save
		$custom_fields_vals = implode( ',', $custom_fields_array );
		echo html( 'input', array( 'type' => 'hidden', 'name' => 'custom_fields_vals', 'value' => $custom_fields_vals ) );

		cp_action_formbuilder( $results, $post );
	}
}



// loops through the custom fields and builds the step2 review page
function cp_formbuilder_review($results) {
	global $wpdb;
?>

	<li>
		<div class="labelwrapper">
			<label><strong><?php _e( 'Category:', APP_TD ); ?></strong></label>
		</div>
		<div id="review"><?php echo esc_html($_POST['catname']); ?></div>
		<div class="clr"></div>
	</li>

	<?php
		foreach ( $results as $result ) {

			// external plugins can modify or disable field
			$result = apply_filters( 'cp_formbuilder_review_field', $result );
			if ( ! $result )
				continue;
	?>
		<li>
			<div class="labelwrapper">
				<label><strong><?php echo esc_html( translate( $result->field_label, APP_TD ) ); ?>:</strong></label>
			</div>
			<div id="review">

			<?php 
				// text areas should display formatting
				// other fields should be stripped
				if ( $result->field_type == 'text area' ) {
					echo stripslashes( nl2br( $_POST[$result->field_name] ) );
				} else if ( $result->field_type == 'checkbox' ) {
					if ( isset($_POST[$result->field_name]) && is_array($_POST[$result->field_name]) )
						echo stripslashes( strip_tags( implode(", ", $_POST[$result->field_name]) ) );
				} else {
					echo stripslashes( strip_tags( $_POST[$result->field_name] ) ); 
				}
			?>

			</div>
			<div class="clr"></div>
		</li>

<?php
	}

}

// calculates total number of image input upload boxes on create ad page
function cp_image_input_fields() {
	global $cp_options;
	for ( $i=0; $i < $cp_options->num_images; $i++ ) {
?>
		<li>
			<div class="labelwrapper">
				<label><?php printf( __( 'Image %s', APP_TD ), $i+1 ); ?>:</label>
			</div>
			<input type="file" id="upload<?php echo $i+1; ?>" name="image[]" value="<?php if ( isset($_POST['image'.$i.'']) ) echo $_POST['image'.$i.'']; ?>" class="fileupload" onchange="enableNextImage(this, <?php echo $i+2; ?>);" <?php if ( $i > 0 ) echo 'disabled="disabled"'; ?> >
			<div class="clr"></div>
		</li>
<?php
	}
?>

	<p class="light"><?php printf( __( '%sKB max file size per image', APP_TD ), $cp_options->max_image_size ); ?></p>
	<div class="clr"></div>

<?php
}



// show the non-custom fields below the main form
function cp_other_fields($renew_id = false) {
	global $wpdb, $cp_options;

	// are images on ads allowed
	if ( $cp_options->ad_images ) {
		if ( appthemes_plupload_is_enabled() ) {

			echo appthemes_plupload_form($renew_id);

		} else {
			if ( $renew_id ) {
				$imagecount = cp_get_ad_images($renew_id);
				echo cp_ad_edit_image_input_fields($imagecount);
			} else {
				echo cp_image_input_fields();
			}
		}
	}

	// show the chargeable options if enabled
	if ( cp_payments_is_enabled() ) {

		// show the featured ad box if enabled
		if ( $cp_options->sys_feat_price ) {
		?>

			<li id="list_featured_ad" class="withborder">
				<div class="labelwrapper">
					<label><?php printf( __( 'Featured Listing %s', APP_TD ), appthemes_get_price( $cp_options->sys_feat_price ) ); ?></label>
				</div>
				<div class="clr"></div>
				<input name="featured_ad" value="1" type="checkbox" <?php checked( isset($_POST['featured_ad']) ); ?> />
				<?php _e( 'Your listing will appear in the featured slider section at the top of the front page.', APP_TD ); ?>
				<div class="clr"></div>
			</li>

		<?php
		}

		if ( $cp_options->price_scheme == 'single' ) {
		?>

			<li>
				<div class="labelwrapper">
					<label><?php _e( 'Ad Package:', APP_TD ); ?></label>
				</div>

				<?php
				// go get all the active ad packs and create a drop-down of options
				$results = $wpdb->get_results( "SELECT * FROM $wpdb->cp_ad_packs WHERE pack_status = 'active' ORDER BY pack_id asc" );

				if ( $results ) {
				?>

					<select name="ad_pack_id" class="dropdownlist required">

					<?php
						foreach ( $results as $result ) {
							// external plugins can modify or disable field
							$result = apply_filters( 'cp_package_field', $result, 'ad' );
							if ( ! $result )
								continue;
					?>
							<option value="<?php echo esc_attr($result->pack_id); ?>"><?php echo esc_attr(stripslashes($result->pack_name)); ?></option>
						<?php } ?>

					</select>

				<?php
				} else {
					_e( 'Error: no ad pack has been defined. Please contact the site administrator.', APP_TD );
				}
				?>

				<div class="clr"></div>
			</li>

		<?php } ?>

	<?php

	} // end charge for ads check

} // end cp_other_fields function




// queries the db for the custom ad form based on the cat id
if ( !function_exists('cp_show_form') ) {
	function cp_show_form($catid, $renew = false) {
		global $wpdb, $cp_options;

		$fid = '';

		// call tinymce init code if html is enabled and user not on mobile device
		if ( $cp_options->allow_html && ! wp_is_mobile() )
			appthemes_tinymce( 490, 300 );

		// get the category ids from all the form_cats fields.
		// they are stored in a serialized array which is why
		// we are doing a separate select. If the form is not
		// active, then don't return any cats.

		$sql = "SELECT ID, form_cats FROM $wpdb->cp_ad_forms WHERE form_status = 'active'";
		$results = $wpdb->get_results($sql);

		if ( $results ) {

			// now loop through the recordset
			foreach ( $results as $result ) {
				// put the form_cats into an array
				$catarray = unserialize( $result->form_cats );

				// now search the array for the $catid which was passed in via the cat drop-down
				if ( in_array($catid, $catarray) ) {
					// when there's a catid match, grab the form id
					$fid = $result->ID;

					// put the form id into the post array for step2
					$_POST['fid'] = $fid;
				}
			}

			// now we should have the formid so show the form layout based on the category selected
			$sql = $wpdb->prepare( "SELECT f.field_label, f.field_name, f.field_type, f.field_values, f.field_perm, f.field_tooltip, f.field_min_length, m.meta_id, m.field_pos, m.field_req, m.form_id "
				. "FROM $wpdb->cp_ad_fields f "
				. "INNER JOIN $wpdb->cp_ad_meta m "
				. "ON f.field_id = m.field_id "
				. "WHERE m.form_id = %s "
				. "ORDER BY m.field_pos asc",
				$fid);

			$results = $wpdb->get_results( $sql );

			if ( $results ) {

				// loop through the custom form fields and display them
				echo cp_formbuilder($results, $renew);

			} else {

				// display the default form since there isn't a custom form for this cat
				echo cp_show_default_form($renew);

			}

		} else {

			// display the default form since there isn't a custom form for this cat
			echo cp_show_default_form($renew);

		}

		// show the image, featured ad, payment type and other options
		$renew_id = ( $renew ) ? $renew->ID : false;
		echo cp_other_fields($renew_id);

	}
}



// if no custom forms exist, just call the default form fields
if ( !function_exists('cp_show_default_form') ) {
	function cp_show_default_form($renew = false) {
		global $wpdb;

		// now we should have the formid so show the form layout based on the category selected
		$sql = "SELECT field_label, field_name, field_type, field_values, field_tooltip, field_req, field_min_length FROM $wpdb->cp_ad_fields WHERE field_core = '1' ORDER BY field_id asc";

		$results = $wpdb->get_results( $sql );

		if ( $results ) {

			// loop through the custom form fields and display them
			echo cp_formbuilder($results, $renew);

		} else {

			echo __( 'ERROR: no results found for the default ad form.', APP_TD ) . '<br />';

		}

	}
}



// show the step 2 review page and query for the fields
// based on the cat they selected. This is an extra step
// but is much more secure and prevents fake forms from
// being submitted with malicious data
function cp_show_review( $postvals ) {
	global $wpdb;

	// if there's no form id it must mean the default form is being used so let's go grab those fields
	if ( empty( $postvals['fid'] ) ) {
		// use this if there's no custom form being used and give us the default form
		$sql = "SELECT field_label, field_name, field_type, field_values, field_req FROM $wpdb->cp_ad_fields WHERE field_core = '1' ORDER BY field_id asc";

	} else {
		// now we should have the formid so show the form layout based on the category selected
		$sql = $wpdb->prepare( "SELECT f.field_label,f.field_name,f.field_type,f.field_values,f.field_perm,m.meta_id,m.field_pos,m.field_req,m.form_id "
			. "FROM $wpdb->cp_ad_fields f "
			. "INNER JOIN $wpdb->cp_ad_meta m "
			. "ON f.field_id = m.field_id "
			. "WHERE m.form_id = %s "
			. "ORDER BY m.field_pos asc",
			$postvals['fid'] );

	}

	$results = $wpdb->get_results( $sql );

	if ( $results ) {

		// loop through the custom form fields and display them
		echo cp_formbuilder_review( $results );

	} else {

		printf( __( 'ERROR: The form template for form ID %s does not exist or the session variable is empty.', APP_TD ), $postvals['fid'] );

	}
?>

	<hr class="bevel" />
	<div class="clr"></div>


	<li>
		<div class="labelwrapper">
			<label><?php _e( 'Ad Listing Fee:', APP_TD ); ?></label>
		</div>
		<div id="review"><?php if ( cp_payments_is_enabled() ) { appthemes_display_price( $postvals['cp_sys_ad_listing_fee'] ); } else { _e( 'FREE', APP_TD ); } ?></div>
		<div class="clr"></div>
	</li>

	<?php if ( isset( $postvals['featured_ad'] ) ) { ?>
		<li>
			<div class="labelwrapper">
				<label><?php _e( 'Featured Listing Fee:', APP_TD ); ?></label>
			</div>
			<div id="review"><?php appthemes_display_price( $postvals['cp_sys_feat_price'] ); ?></div>
			<div class="clr"></div>
		</li>
	<?php } ?>
	
	<?php if ( isset( $postvals['cp_membership_pack'] ) ) { ?>
		<li>
			<div class="labelwrapper">
				<label><?php _e( 'Membership:', APP_TD ); ?></label>
			</div>
			<div id="review"><?php echo get_pack_benefit( $postvals['cp_membership_pack'] ); ?></div>
			<div class="clr"></div>
		</li>
	<?php } ?>

	<hr class="bevel-double" />
	<div class="clr"></div>

	<li>
		<div class="labelwrapper">
			<label><?php _e( 'Total Amount Due:', APP_TD ); ?></label>
		</div>
		<div id="review"><strong>
		<?php
			if ( cp_payments_is_enabled() ) appthemes_display_price( $postvals['cp_sys_total_ad_cost'] ); else _e( '--', APP_TD );
		?>
		</strong></div>
		<div class="clr"></div>
	</li>

	<?php
		if ( cp_payments_is_enabled() && $postvals['cp_sys_total_ad_cost'] > 0 )
			do_action( 'appthemes_purchase_fields' );
	?>

<?php
}


// display the total cost per listing on the 1st step page
function cp_cost_per_listing() {
	global $cp_options;
	// make sure we are charging for ads
	if ( cp_payments_is_enabled() ) {

		// now figure out which pricing scheme is set
		switch( $cp_options->price_scheme ) :

			case 'category':
				$cost_per_listing = __( 'Price depends on category', APP_TD );
				break;

			case 'single':
				$cost_per_listing = __( 'Price depends on ad package selected', APP_TD );
				break;

			case 'percentage':
				$cost_per_listing = sprintf( __( '%s of your ad listing price', APP_TD ), $cp_options->percent_per_ad . '%' );
				break;
		
			case 'featured':
				$cost_per_listing = __( 'Free listing unless featured.', APP_TD );
				break;

			default:
				// pricing structure must be free
				$cost_per_listing = __( 'Free', APP_TD );

		endswitch;

	} else {
		// if we aren't charging, then ads must be free
		$cost_per_listing = __( 'Free', APP_TD );
	}

	echo $cost_per_listing;

}


// give us just the ad listing fee
function cp_ad_listing_fee( $cat_id, $ad_pack_id, $cp_price, $price_curr ) {
	global $wpdb, $cp_options;

	// make sure we are charging for ads
	if ( ! cp_payments_is_enabled() )
		return 0;

	// now figure out which pricing scheme is set
	switch( $cp_options->price_scheme ) {

		case 'category':
			$prices = $cp_options->price_per_cat;
			$adlistingfee = ( isset( $prices[ $cat_id ] ) ) ? (float) $prices[ $cat_id ] : 0;
			break;

		case 'percentage':
			// grab the % and then put it into a workable number
			$ad_percentage = ( $cp_options->percent_per_ad * 0.01 );
			// calculate the ad cost. Ad listing price x percentage.
			$adlistingfee = ( appthemes_clean_price( $cp_price, 'float' ) * $ad_percentage );

			// can modify listing fee. example: apply currency conversion
			$adlistingfee = apply_filters( 'cp_percentage_listing_fee', $adlistingfee, $cp_price, $ad_percentage, $price_curr );
			break;

		case 'featured':
			// listing price is always free in this pricing schema
			$adlistingfee = 0;
			break;

		default: // pricing model must be single ad packs
			// make sure we have something if ad_pack_id is empty so no db error
			if ( empty( $ad_pack_id ) )
				$ad_pack_id = 1;

			// go get all the active ad packs and create a drop-down of options
			$sql = $wpdb->prepare( "SELECT pack_price, pack_duration FROM $wpdb->cp_ad_packs WHERE pack_id = %d LIMIT 1", $ad_pack_id );
			$results = $wpdb->get_row( $sql );

			// now return the price and put the duration variable into an array
			if ( $results ) {
				$adlistingfee = $results->pack_price;
			} else {
					sprintf( __( 'ERROR: no ad packs found for ID %s.', APP_TD ), $ad_pack_id );
			}
			break;

	}

	// return the ad listing fee
	return $adlistingfee;
}


function cp_get_ad_pack_length($ad_pack_id) {
	global $wpdb;

	// make sure we have something if ad_pack_id is empty so no db error
	if ( empty($ad_pack_id) )
		$ad_pack_id = 1;

	// go get all the active ad packs
	$sql = $wpdb->prepare( "SELECT pack_duration FROM $wpdb->cp_ad_packs WHERE pack_id = %d LIMIT 1", $ad_pack_id );
	$results = $wpdb->get_row($sql);

	// now return the length of ad pack
	if ( $results )
		$ad_pack_length = $results->pack_duration;

	return $ad_pack_length;
}



// figure out what the total ad cost will be
function cp_calc_ad_cost( $catid, $ad_pack_id, $featuredprice, $cp_price, $cp_coupon, $price_curr ) {

	if ( ! cp_payments_is_enabled() )
		return 0;

	// calculate the listing fee price
	$adlistingfee = cp_ad_listing_fee( $catid, $ad_pack_id, $cp_price, $price_curr );
	// calculate the total cost for the ad.
	$totalcost_out = $adlistingfee + $featuredprice;

	//set proper return format
	$totalcost_out = number_format($totalcost_out, 2, '.', '');	

	//if total cost is less then zero, then make the cost zero (free)
	if ( $totalcost_out < 0 )
		$totalcost_out = 0;

	return $totalcost_out;
}


// determine what the ad post status should be
if ( ! function_exists('cp_set_post_status') ) {
	function cp_set_post_status( $advals ) {
		global $wpdb, $cp_options;

		if ( $cp_options->post_status == 'pending' )
			return 'pending';

		if ( cp_payments_is_enabled() && $advals['cp_sys_total_ad_cost'] > 0 )
			return 'pending';

		return 'publish';
	}
}


// this is where the new ad gets created
function cp_add_new_listing($advals, $renew_id = false) {
	global $wpdb, $cp_options;

	$new_tags = '';
	$ad_length = '';
	$attach_id = '';
	$the_attachment = '';

	// check to see if html is allowed
	if ( ! $cp_options->allow_html )
		$post_content = appthemes_filter( $advals['post_content'] );
	else
		$post_content = $advals['post_content'];

	// tags are tricky and need to be put into an array before saving the ad
	if ( !empty( $advals['tags_input'] ) )
		$new_tags = explode( ',', $advals['tags_input'] );

	// put all the new ad elements into an array
	// these are the minimum required fields for WP (except tags)
	$new_ad = array();
	$new_ad['post_title'] = appthemes_filter( $advals['post_title'] );
	$new_ad['post_content'] = trim( $post_content );
	$new_ad['post_status'] = 'pending'; // no longer setting final status until after images are set
	$new_ad['post_author'] = $advals['user_id'];
	$new_ad['post_type'] = APP_POST_TYPE;

	if ( $renew_id ) {
		$new_ad['ID'] = $renew_id;
		$new_ad['post_date'] = current_time('mysql');
		$new_ad['post_date_gmt'] = current_time('mysql', 1);
		$post_id = wp_update_post( $new_ad );
	} else {
		// insert the new ad
		$post_id = wp_insert_post( $new_ad );
	}

	//set the custom post type categories
	wp_set_post_terms( $post_id, appthemes_filter( $advals['cat'] ), APP_TAX_CAT, false );

	//set the custom post type tags
	wp_set_post_terms( $post_id, $new_tags, APP_TAX_TAG, false );

	// the unique order ID we created becomes the ad confirmation ID
	// we will use this for payment systems and for activating the ad
	// later if need be. it needs to start with cp_ otherwise it won't
	// be loaded in with the ad so let's give it a new name
	$advals['cp_sys_ad_conf_id'] = $advals['oid'];

	// get the ad duration and first see if ad packs are being used
	// if so, get the length of time in days otherwise use the default
	// prune period defined on the CP settings page
	if ( isset( $advals['pack_duration'] ) )
		$ad_length = $advals['pack_duration'];
	else
		$ad_length = $cp_options->prun_period;

	// set the ad listing expiration date and put into a session
	$ad_expire_date = appthemes_mysql_date( current_time( 'mysql' ), $ad_length );
	$advals['cp_sys_expire_date'] = $ad_expire_date;
	$advals['cp_sys_ad_duration'] = $ad_length;

	// if renew ad - delete all old post meta and unmark ad as featured
	if ( $renew_id ) {
		unstick_post($renew_id);
		$custom_field_keys = get_post_custom_keys($renew_id);
		foreach ( $custom_field_keys as $custom_key )
			delete_post_meta($renew_id, $custom_key);
	}

	// now add all the custom fields into WP post meta fields
	foreach ( $advals as $meta_key => $meta_value ) {
		if ( appthemes_str_starts_with($meta_key, 'cp_') && !is_array($advals[$meta_key]) )
			add_post_meta( $post_id, $meta_key, $meta_value, true );

		if ( appthemes_str_starts_with($meta_key, 'cp_') && is_array($advals[$meta_key]) )
			foreach ( $advals[$meta_key] as $checkbox_value )
				add_post_meta( $post_id, $meta_key, $checkbox_value );

	}

	// if they checked the box for a featured ad, then make the post sticky
	if (isset($advals['featured_ad']))
		stick_post($post_id);

	if (isset($advals['attachment'])) {
		$the_attachment = $advals['attachment'];
		// associate the already uploaded images to the new ad and create multiple image sizes
		$attach_id = cp_associate_images( $post_id, $the_attachment, true );
	}

	if (isset($advals['app_attach_id'])) {
		$attachments = $advals['app_attach_id'];
		$titles = ( isset($advals['app_attach_title']) ) ? $advals['app_attach_title'] : array();
		// associate the already uploaded images to the new ad and update titles
		$attach_id = appthemes_plupload_associate_images( $post_id, $attachments, $titles, true );
	}
	// set the thumbnail pic on the WP post
	//cp_set_ad_thumbnail($post_id, $attach_id);

	//last step is to publish the ad when its appropriate to publish immediately
	$final_status = cp_set_post_status( $advals );

	if ( $final_status == 'publish' ) {
		$final_post = array();
		$final_post['ID'] = $post_id;
		$final_post['post_status'] = $final_status;
		$update_result = wp_update_post( $final_post );
	}

	cp_action_add_new_listing( $post_id );
	// kick back the post id in case we want to use it
	return $post_id;

}

//process membership order
function appthemes_process_membership_order( $user, $order ) {

	//if order ID matches pending membership id suffix, then process the order by extendning the date and setting the ID
	if ( isset( $user->active_membership_pack ) )
		$user_active_pack_id = get_pack_id( $user->active_membership_pack );
	else
		$user_active_pack_id = false;

	if ( isset( $user->membership_expires ) )
		$user_active_pack_expiration = $user->membership_expires;
	else
		$user_active_pack_expiration = current_time( 'mysql' );
	
	$membership = get_pack( $order['pack_id'] );
	if ( ! $membership )
		return false;

	//update the user profile to current order pack_id taking it off "pending" status and setup the membership object
	update_user_meta( $user->ID, 'active_membership_pack', $order['pack_id'] );

	//extend membership if its still active, so long as its not free (otherwise free extentions could be infinite)
	$expires_in_days = appthemes_seconds_to_days( strtotime( $user_active_pack_expiration ) - current_time( 'timestamp' ) );
	if ( $expires_in_days > 0 && $order['total_cost'] > 0 && $order['pack_id'] == $user_active_pack_id ) {
		$updated_expires_date = appthemes_mysql_date( $user_active_pack_expiration, $order['pack_duration'] );
	} else { 
		$updated_expires_date = appthemes_mysql_date( current_time( 'mysql' ), $order['pack_duration'] );
	}

	update_user_meta( $user->ID, 'membership_expires', $updated_expires_date );
	$order['updated_expires_date'] = $updated_expires_date;
	delete_option( $order['option_order_id'] );

	//return the order information in case its needed
	return $order;
}


