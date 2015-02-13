<?php

/**
 * These are Administration Panel additions
 * within the WordPress admin pages
 * http://codex.wordpress.org/Administration_Panels
 * 
 * 
 */


// column sorting ajax 
function cp_ajax_sortable_js() {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {

	// Return a helper with preserved width of cells
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			jQuery(this).width(jQuery(this).width());
			//ui.placeholder.html('<!--[if IE]><td>&nbsp;</td><![endif]-->');
		});
		return ui;
	};

	jQuery("tbody.sortable").sortable({
		helper: fixHelper,
		opacity: 0.7,
		cursor: 'move',
		// connectWith: 'table.widefat tbody',
		placeholder: 'ui-placeholder',
		forcePlaceholderSize: true,
		items: 'tr',
		update: function() {
			var results = jQuery("tbody.sortable").sortable("toArray"); // pass in the array of row ids based off each tr css id

			var data = { // pass in the action
			action: 'cp_ajax_update',
			rowarray: results
			};

			jQuery("span#loading").html('<img src="<?php echo get_template_directory_uri(); ?>/images/ajax-loading.gif" />');
			jQuery.post(ajaxurl, data, function(theResponse){
				jQuery("span#loading").html(theResponse);
			});
		}
	}).disableSelection();


});

</script>
<?php
}
add_action( 'admin_head', 'cp_ajax_sortable_js' );


// db update function for the column sort ajax feature
function cp_ajax_sort_callback() {
	global $wpdb;

	$counter = 1;
	foreach ( $_POST['rowarray'] as $value ) {
		$wpdb->update( $wpdb->cp_ad_meta, array( 'field_pos' => $counter ), array( 'meta_id' => $value ) );
		$counter = $counter + 1;
	}
	die();
}
add_action( 'wp_ajax_cp_ajax_update', 'cp_ajax_sort_callback' );


// adds the thumbnail column to the WP Posts Edit SubPanel
function cp_thumbnail_column( $cols ) {
	$cols['thumbnail'] = __( 'Image', APP_TD );
	return $cols;
}
add_filter( 'manage_post_posts_columns', 'cp_thumbnail_column', 11 );
add_filter( 'manage_' . APP_POST_TYPE . '_posts_columns', 'cp_thumbnail_column', 11 );


function cp_thumbnail_value( $column_name, $post_id ) {
	$thumb = false;
	$width = 50;
	$height = 50;

	if ( 'thumbnail' == $column_name ) {
		// thumbnail of WP 2.9
		$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
		// image from gallery
		$attachments = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );

		if ( $thumbnail_id ) {
			$thumb = wp_get_attachment_image( $thumbnail_id, array( $width, $height ), true );
		} elseif ( $attachments ) {
			foreach ( $attachments as $attachment_id => $attachment ) {
				$thumb = wp_get_attachment_image( $attachment_id, array( $width, $height ), true );
			}
		}

		if ( $thumb )
			echo $thumb;

	}
}
add_action( 'manage_post_posts_custom_column', 'cp_thumbnail_value', 11, 2 );
add_action( 'manage_' . APP_POST_TYPE . '_posts_custom_column', 'cp_thumbnail_value', 11, 2 );


