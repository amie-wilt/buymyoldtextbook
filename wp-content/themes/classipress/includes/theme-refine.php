<?php
/**
 * This creates all the fields and assembles them
 * on the ad category sidebar based on either custom forms
 * built by the admin or it just defaults to a
 * standard form which has been pre-defined.
 *
 * @global <type> $wpdb
 * @param <type> $results
 *
 *
 */


// queries the db for the custom ad form based on the cat id
if ( ! function_exists( 'cp_show_refine_search' ) ) :
function cp_show_refine_search( $cat_id ) {
	global $wpdb;

	$form_id = false;

	// get the category ids from all the form_cats fields.
	// they are stored in a serialized array which is why
	// we are doing a separate select. If the form is not
	// active, then don't return any cats.
	$results = $wpdb->get_results( "SELECT ID, form_cats FROM $wpdb->cp_ad_forms WHERE form_status = 'active'" );

	if ( ! $results )
		return;

	// now loop through the recordset
	foreach ( $results as $result ) {

		// put the form_cats into an array
		$catarray = unserialize( $result->form_cats );

		// now search the array for the $catid which was passed in via the cat drop-down
		// when there's a catid match, grab the form id
		if ( in_array( $cat_id, $catarray ) )
			$form_id = $result->ID;
	}

	if ( ! $form_id )
		return;

	// now we should have the formid so show the form layout based on the category selected
	$sql = $wpdb->prepare( "SELECT f.field_label, f.field_name, f.field_type, f.field_values, f.field_perm, m.field_search, m.meta_id, m.field_pos, m.field_req, m.form_id "
		. "FROM $wpdb->cp_ad_fields f "
		. "INNER JOIN $wpdb->cp_ad_meta m ON f.field_id = m.field_id "
		. "WHERE m.form_id = %s AND m.field_search = '1' "
		. "ORDER BY m.field_pos ASC", $form_id );

	$results = $wpdb->get_results( $sql );

	if ( $results )
		echo cp_refine_search_builder( $results ); // loop through the custom form fields and display them

}
endif;


// loops through the custom fields and builds the custom refine search in the sidebar
if ( ! function_exists( 'cp_refine_search_builder' ) ) :
	function cp_refine_search_builder( $results ) {
		global $wpdb, $cp_options;

		$cp_min_price = str_replace( ',', '', $wpdb->get_var( "SELECT min( CAST( m.meta_value AS UNSIGNED ) ) FROM $wpdb->postmeta m INNER JOIN $wpdb->posts p ON m.post_id = p.ID WHERE m.meta_key = 'cp_price' AND p.post_status = 'publish'" ) );
		$cp_max_price = str_replace( ',', '', $wpdb->get_var( "SELECT max( CAST( m.meta_value AS UNSIGNED ) ) FROM $wpdb->postmeta m INNER JOIN $wpdb->posts p ON m.post_id = p.ID WHERE m.meta_key = 'cp_price' AND p.post_status = 'publish'" ) );
		$thousands = ( $cp_max_price > 1000 ) ? true : false;

		$locarray = array();
?>
	<script type="text/javascript">
	// <![CDATA[
	// toggles the refine search field values
	jQuery(document).ready(function() {
		jQuery('div.handle').click(function() {
			jQuery(this).next('div.element').animate({
				height: ['toggle', 'swing'],
				opacity: 'toggle' }, 200
			);

			jQuery(this).toggleClass('close', 'open');
			return false;
		});
		<?php foreach ( $_GET as $field => $val ) : ?>
			jQuery('.<?php echo esc_js($field); ?> div.handle').toggleClass('close', 'open');
			jQuery('.<?php echo esc_js($field); ?> div.element').show();
		<?php endforeach; ?>

	});
	// ]]>
	</script>

	<div class="shadowblock_out">

		<div class="shadowblock">

			<h2 class="dotted"><?php _e( 'Refine Results', APP_TD ); ?></h2>

			<ul class="refine">

				<form action="<?php echo home_url( '/' ); ?>" method="get" name="refine-search">
				<?php if ( ! is_tax( APP_TAX_CAT ) ) { ?>
					<input type="hidden" name="s" value="<?php echo esc_attr(cp_get_search_term()); ?>" />
					<input type="hidden" name="scat" value="<?php echo esc_attr(cp_get_search_catid()); ?>" />
				<?php } else { ?>
					<input type="hidden" name="<?php echo esc_attr( get_query_var( 'taxonomy' ) ); ?>" value="<?php echo esc_attr( get_query_var( 'term' ) ); ?>" />
				<?php } ?>

					<?php
					// grab the price and location fields first and put into a separate array
					// then remove them from the results array so they don't print out again
					foreach ( $results as $key => $value ) {

						switch ( $value->field_name ) :

							case 'cp_city':
								$locarray[0] = $results[$key];
								unset($results[$key]);
							break;

							case 'cp_zipcode':
								$locarray[1] = $results[$key];
								unset($results[$key]);
							break;

							case 'cp_price':
								$locarray[2] = $results[$key];
								unset($results[$key]);
							break;

						endswitch;
					}

					// sort array by key so we get the city/zip code first
					ksort( $locarray );

					// both zip code and city have been checked
					if ( array_key_exists(0, $locarray) && array_key_exists(1, $locarray) ) {
						$flabel = sprintf( __( '%1$s or %2$s', APP_TD ), $locarray[0]->field_label, $locarray[1]->field_label );
						$fname = 'cp_city_zipcode';
					} elseif ( array_key_exists(0, $locarray) ) { // must be the city only
						$flabel = $locarray[0]->field_label;
						$fname = 'cp_city_zipcode';
					} elseif ( array_key_exists(1, $locarray) ) { // must be the zip code only
						$flabel = $locarray[1]->field_label;
						$fname = 'cp_city_zipcode';
					}

					$distance_unit = ( 'mi' == $cp_options->distance_unit ) ? __( 'miles', APP_TD ) : __( 'kilometers', APP_TD );
					// show the city/zip code field and radius slider bar
					if ( array_key_exists(0, $locarray) || array_key_exists(1, $locarray) ) :
					?>
						<script type="text/javascript">
						// <![CDATA[
							jQuery(document).ready(function() {
								jQuery('#dist-slider').slider( {
									range: 'min',
									min: 0,
									max: 100,
									value: <?php echo esc_js( isset( $_GET['distance'] ) ? intval( $_GET['distance'] ) : '50' ); ?>,
									step: 5,
									slide: function(event, ui) {
										jQuery('#distance').val(ui.value + ' <?php echo esc_js( $distance_unit ); ?>');
									}
								});
								jQuery('#distance').val(jQuery('#dist-slider').slider('value') + ' <?php echo esc_js( $distance_unit ); ?>');
							});
						// ]]>
						</script>

						<li class="distance">
							<label class="title"><?php echo $flabel; ?></label>
							<input name="<?php echo esc_attr( $fname ); ?>" id="<?php echo esc_attr( $fname ); ?>" type="text" minlength="2" value="<?php if(isset($_GET[$fname])) echo esc_attr( $_GET[$fname] ); ?>" class="text" />
							<div class="clr"></div>
							<label for="distance" class="title"><?php _e( 'Radius', APP_TD ); ?>:</label>
							<input type="text" id="distance" name="distance" />
							<div id="dist-slider"></div>
						</li>

					<?php

					endif;

					// now loop through the other special fields
					foreach ( $locarray as $value ) :

						// show the price field range slider
						if ( $value->field_name == 'cp_price' ) {
							$curr_symbol = $cp_options->curr_symbol;
							if ( isset( $_GET['amount'] ) )
								$amount = explode( ' - ', $_GET['amount'] );
							$amount[0] = empty( $amount[0] ) ? $cp_min_price : $amount[0];
							$amount[1] = empty( $amount[1] ) ? $cp_max_price : $amount[1];
							$amount[0] = str_replace( array( ',', $curr_symbol, ' ' ), '', $amount[0] );
							$amount[1] = str_replace( array( ',', $curr_symbol, ' ' ), '', $amount[1] );
							?>

							<script type="text/javascript">
							// <![CDATA[
								jQuery(document).ready(function() {

									jQuery("#thousands").click(function() {
										thousands = ( jQuery(this).is(":checked") ) ? true : false;
										cp_show_price_slider(<?php echo esc_js( intval( $cp_min_price ) ); ?>, <?php echo esc_js( intval( $cp_max_price ) ); ?>, <?php echo esc_js( intval( $amount[0] ) ); ?>, <?php echo esc_js( intval( $amount[1] ) ); ?>, thousands);
									});
									thousands = ( jQuery("#thousands").is(":checked") ) ? true : false;
									cp_show_price_slider(<?php echo esc_js( intval( $cp_min_price ) ); ?>, <?php echo esc_js( intval( $cp_max_price ) ); ?>, <?php echo esc_js( intval( $amount[0] ) ); ?>, <?php echo esc_js( intval( $amount[1] ) ); ?>, thousands);

								});
							// ]]>
							</script>

							<li class="amount">
								<label class="title"><?php echo esc_html( translate( $value->field_label, APP_TD ) ); ?>:</label>
								<input type="text" id="amount" name="amount" />
								<div id="slider-range"></div>
								<?php if( $thousands ) { ?>
									<label class="title"><?php echo esc_html( __( 'Show Thousands', APP_TD ) ); ?>:</label><input type="checkbox" id="thousands" name="thousands" <?php checked( isset($_GET['thousands']) ); ?> />
								<?php } ?>
							</li>
							<?php

						}

					endforeach;


					foreach ( $results as $key => $result ) {
						if ( in_array( $result->field_type, array( 'radio', 'checkbox', 'drop-down', 'text box', 'text area' ) ) )
							echo cp_refine_fields( $result->field_label, $result->field_name, $result->field_values, $result->field_type );
					}
					?>
					<div class="pad10"></div>
					<button class="obtn btn_orange" type="submit" tabindex="1" id="go" value="Go" name="sa"><?php _e( 'Refine Results &rsaquo;&rsaquo;', APP_TD ); ?></button>
          
          <input type="hidden" name="refine_search" value="yes" />
          
				</form>

			</ul>

			<div class="clr"></div>

		</div>

	</div>

<?php
	}
endif;


// spit out the field names and values
function cp_refine_fields( $label, $name, $values, $type ) {
	if ( in_array( $type, array( 'radio', 'checkbox', 'drop-down' ) ) ) {
?>

	<li class="<?php echo esc_attr( $name ); ?>">
		<label class="title"><?php echo esc_html( translate( $label, APP_TD ) ); ?></label>

		<div class="handle close"></div>

		<div class="element">

			<?php
			$options = explode(',', $values);
			$options = array_map( 'trim', $options );
			$optionCursor = 1;
			$checked = '';
			?>

			<div class="scrollbox">

				<ol class="checkboxes">

					<?php
					$cur = ( isset( $_GET[ $name ] ) && is_array( $_GET[ $name ] ) ) ? array_map( 'stripslashes', $_GET[ $name ] ) : array();
					foreach ( $options as $option ) {
						if ( $cur )
							$checked = in_array( $option, $cur ) ? " checked='checked'" : ''; ?>
						<li>
							<input type="checkbox" name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo esc_attr( $option ); ?>" <?php echo $checked; ?> />&nbsp;<label for="<?php echo esc_attr( $name ); ?>[]"><?php echo esc_html( $option ); ?></label>
						</li> <!-- #checkbox -->
			  <?php } ?>

				</ol> <!-- #checkbox-wrap -->

			</div> <!-- #end scrollbox -->

		</div> <!-- #end element -->

		<div class="clr"></div>

	</li>
<?php
	} else {
?>
	<li class="<?php echo esc_attr( $name ); ?>">
		<label class="title"><?php echo esc_html( translate( $label, APP_TD ) ); ?></label>
		<input name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" type="text" minlength="2" value="<?php if ( isset( $_GET[ $name ] ) ) echo esc_attr( stripslashes( $_GET[ $name ] ) ); ?>" class="text" />
		<div class="clr"></div>
	</li>
<?php
	}
}

function cp_pre_get_posts( $query ) {
	global $wpdb, $cp_options;

	if ( $query->is_archive && isset( $query->query_vars['ad_cat'] ) ) {
		$meta_query = array();
		foreach ( $_GET as $key => $value ) {
			if ( $value )
			switch ( $key ) {
				case 'cp_city_zipcode' :
					$region = $cp_options->gmaps_region;
					$value = urlencode( $value );
					$geocode = json_decode( wp_remote_retrieve_body( wp_remote_get( "http://maps.googleapis.com/maps/api/geocode/json?address=$value&sensor=false&region=$region" ) ) );
					if ( 'OK' == $geocode->status ) {
						$query->set( 'app_geo_query', array(
							'lat' => $geocode->results[0]->geometry->location->lat,
							'lng' => $geocode->results[0]->geometry->location->lng,
							'rad' => intval( $_GET['distance'] ),
						) );
					} else {
						// Google Maps API error
					}
					break;

				case 'amount' :
					$value = str_replace( array( $cp_options->curr_symbol, ' ' ), '', $value );
					$value = str_replace( ' ', '', $value );
					$meta_query[] = array(
								'key' => 'cp_price',
								'value' => explode( '-', $value ),
								'compare' => 'BETWEEN',
								'type' => 'numeric',
					);
					break;

				default :
					if ( 'cp_' == substr( $key, 0, 3 ) ) {
						$field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->cp_ad_fields WHERE field_name = %s", $key ) );
						if ( $field === null )
							break;
						$compare = ( in_array( $field->field_type, array( 'radio', 'checkbox', 'drop-down' ) ) ) ? 'IN' : 'LIKE';
						$meta_query[] = array(
							'key'   => $key,
							'value' => $value,
							'compare' => $compare
						);
					}
					break;
			}
		}
		$query->set( 'meta_query', $meta_query );
	}

	return $query;
}
add_filter( 'pre_get_posts', 'cp_pre_get_posts' );


function cp_posts_clauses( $clauses, $wp_query ) {
	global $wpdb, $cp_options;

	$geo_query = $wp_query->get( 'app_geo_query' );
	if ( !$geo_query )
		return $clauses;

	extract( $geo_query, EXTR_SKIP );

	$R = ( 'mi' == $cp_options->distance_unit ) ? 3959 : 6371;
	$table = $wpdb->cp_ad_geocodes;

	$clauses['join'] .= " INNER JOIN $table ON ($wpdb->posts.ID = $table.post_id)";

	$clauses['where'] .= $wpdb->prepare( " AND ( %d * acos( cos( radians(%f) ) * cos( radians(lat) ) * cos( radians(lng) - radians(%f) ) + sin( radians(%f) ) * sin( radians(lat) ) ) ) < %d", $R, $lat, $lng, $lat, $rad );

	return $clauses;
}
add_filter( 'posts_clauses', 'cp_posts_clauses', 10, 2 );


// refine search on custom fields
function custom_search_refine_where( $where ) {
	global $wpdb, $wp_query, $refine_count, $cp_options;

	$refine_count = 0; // count how many post meta we query
	$old_where = $where; // intercept the old where statement

	if ( is_search() && isset( $_GET['s'] ) && isset( $_GET['refine_search'] ) ) {
		$query = '';

		foreach ( $_GET as $key => $value ) {
			if ( $value ) {
				switch ( $key ) {
					case 'cp_city_zipcode' :
						$region = $cp_options->gmaps_region;
						$value = urlencode( $value );
						$geocode = json_decode( wp_remote_retrieve_body( wp_remote_get( "http://maps.googleapis.com/maps/api/geocode/json?address=$value&sensor=false&region=$region" ) ) );
						if ( 'OK' == $geocode->status ) {
							$wp_query->set( 'search_geo_query', array(
								'lat' => $geocode->results[0]->geometry->location->lat,
								'lng' => $geocode->results[0]->geometry->location->lng,
								'rad' => intval( $_GET['distance'] ),
							) );
						} else {
							// Google Maps API error
						}
						break;

					case 'amount' :
						$refine_count++;
						$value = str_replace( array( $cp_options->curr_symbol, ' ' ), '', $value );
						$value = str_replace( ' ', '', $value );
						$value = explode( '-', $value );

						$query .= " AND (";
						$query .= "(mt" . $refine_count . ".meta_key = 'cp_price')";
						$query .= " AND (CAST(mt" . $refine_count . ".meta_value AS SIGNED) BETWEEN '$value[0]' AND '$value[1]')";
						$query .= ")";
						break;

					default :
						if ( 'cp_' == substr( $key, 0, 3 ) ) {
							$field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->cp_ad_fields WHERE field_name = %s", $key ) );
							if ( $field === null )
								break;
							$refine_count++;
							if ( is_array( $value ) )
								$value = implode( "','", $value );

							$compare = ( in_array( $field->field_type, array( 'radio', 'checkbox', 'drop-down' ) ) ) ? "IN ('$value')" : "LIKE '%$value%'";

							$query .= " AND (";
							$query .= "(mt" . $refine_count . ".meta_key = '$key')";
							$query .= " AND (CAST(mt" . $refine_count . ".meta_value AS CHAR) $compare)";
							$query .= ")";
						}
						break;
				}
			}
		}

		$geo_query = $wp_query->get( 'search_geo_query' );
		if ( $geo_query ) {
			extract( $geo_query, EXTR_SKIP );
			$R = ( 'mi' == $cp_options->distance_unit ) ? 3959 : 6371;
			$query .= $wpdb->prepare( " AND ( %d * acos( cos( radians(%f) ) * cos( radians(lat) ) * cos( radians(lng) - radians(%f) ) + sin( radians(%f) ) * sin( radians(lat) ) ) ) < %d", $R, $lat, $lng, $lat, $rad );
		}

		if ( ! empty( $query ) )
			$where .= $query;

		remove_filter( 'posts_where', 'custom_search_refine_where' );
	}

	return $where;
}


// refine search on custom fields
function custom_search_refine_join( $join ) {
	global $wpdb, $wp_query, $refine_count;

	if ( is_search() && isset( $_GET['s'] ) && isset( $_GET['refine_search'] ) ) {

		$geo_query = $wp_query->get( 'search_geo_query' );
		if ( $geo_query ) {
			$table = $wpdb->cp_ad_geocodes;
			$join .= " INNER JOIN $table ON ($wpdb->posts.ID = $table.post_id)";
		}

		if ( isset( $refine_count ) && is_numeric( $refine_count ) && $refine_count > 0 ) {
			for ( $i = 1; $i <= $refine_count; $i++ ) {
				$join .= " INNER JOIN $wpdb->postmeta AS mt".$i." ON ($wpdb->posts.ID = mt".$i.".post_id) ";
			}
		}

		remove_filter( 'posts_join', 'custom_search_refine_join' );
	}

	return $join;
}


// don't correct refine search urls in older WP, support for arrays introduced in WP 3.4
if( isset($_GET['refine_search']) && version_compare($wp_version, '3.4', '<') ) {
	remove_action( 'template_redirect', 'redirect_canonical' );
}

