<?php
/*
 * Template Name: User Edit Item
 *
 * This template must be assigned to the edit-item page
 * in order for it to work correctly
 *
 */

global $wpdb, $current_user;

$getad = get_post( $_GET['aid'] );
$terms = wp_get_post_terms( $_GET['aid'], APP_TAX_CAT );
$category_id = ( ! empty( $terms ) ) ? $terms[0]->term_id : false;


// call tinymce init code if html is enabled and user not on mobile device
if ( $cp_options->allow_html && ! wp_is_mobile() )
	appthemes_tinymce( 490, 300 );
?>

<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<!-- left block -->
			<div class="content_left">

				<div class="shadowblock_out">

					<div class="shadowblock">

						<h1 class="single dotted"><?php _e( 'Edit Your Ad', APP_TD ); ?></h1>

						<?php do_action( 'appthemes_notices' ); ?>

						<p><?php _e( 'Edit the fields below and click save to update your ad. Your changes will be updated instantly on the site.', APP_TD ); ?></p>

						<form name="mainform" id="mainform" class="form_edit" action="" method="post" enctype="multipart/form-data">

						<?php wp_nonce_field( 'cp-edit-item' ); ?>

							<ol>

							<?php
								// we first need to see if this ad is using a custom form
								// so lets search for a catid match and return the id if found
								$fid = cp_get_form_id( $category_id );

								// if there's no form id it must mean the default form is being used so let's go grab those fields
								if ( ! $fid ) {

									// use this if there's no custom form being used and give us the default form
									$sql = "SELECT field_label, field_name, field_type, field_values, field_tooltip, field_req FROM $wpdb->cp_ad_fields WHERE field_core = '1' ORDER BY field_id asc";

								} else {

									// now we should have the formid so show the form layout based on the category selected
									$sql = $wpdb->prepare("SELECT f.field_label, f.field_name, f.field_type, f.field_values, f.field_perm, f.field_tooltip, m.meta_id, m.field_pos, m.field_req, m.form_id "
										. "FROM $wpdb->cp_ad_fields f "
										. "INNER JOIN $wpdb->cp_ad_meta m "
										. "ON f.field_id = m.field_id "
										. "WHERE m.form_id = %s "
										. "ORDER BY m.field_pos asc", $fid);

								}

								$results = $wpdb->get_results( $sql );

								if ( $results ) {
										// build the edit ad form
										cp_formbuilder( $results, $getad );
								}

								// check and make sure images are allowed
								if ( $cp_options->ad_images ) {

									if ( appthemes_plupload_is_enabled() ) {
										echo appthemes_plupload_form( $getad->ID );
									} else {
										$imagecount = cp_get_ad_images( $getad->ID );
										// print out image upload fields. pass in count of images allowed
										echo cp_ad_edit_image_input_fields( $imagecount );
									}

								} else { ?>

									<div class="pad10"></div>
									<li>
										<div class="labelwrapper">
											<label><?php _e( 'Images:', APP_TD ); ?></label><?php _e( 'Sorry, image editing is not supported for this ad.', APP_TD ); ?>
										</div>
									</li>
									<div class="pad25"></div>

								<?php } ?>


								<p class="submit center">
									<input type="button" class="btn_orange" onclick="window.location.href='<?php echo CP_DASHBOARD_URL; ?>'" value="<?php _e( 'Cancel', APP_TD ); ?>" />&nbsp;&nbsp;
									<input type="submit" class="btn_orange" value="<?php _e( 'Update Ad &raquo;', APP_TD ); ?>" name="submit" />
								</p>

							</ol>

							<input type="hidden" name="action" value="cp-edit-item" />
							<input type="hidden" name="ad_id" value="<?php echo $getad->ID; ?>" />

						</form>

					</div><!-- /shadowblock -->

				</div><!-- /shadowblock_out -->

			</div><!-- /content_left -->

			<?php get_sidebar( 'user' ); ?>

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
