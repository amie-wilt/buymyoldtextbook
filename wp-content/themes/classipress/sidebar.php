<?php global $cp_options; ?>

<div class="content_right">

<?php
	if ( is_page_template( 'tpl-ads-home.php' ) ) {

		$current_user = wp_get_current_user();
		$display_user_name = cp_get_user_name();
?>


	<?php } ?>


<?php
	if ( is_tax( APP_TAX_CAT ) ) {

		// go get the taxonomy category id so we can filter with it
		// have to use slug instead of name otherwise it'll break with multi-word cats
		if ( ! isset( $filter ) )
			$filter = '';

		$ad_cat_array = get_term_by( 'slug', get_query_var( APP_TAX_CAT ), APP_TAX_CAT, ARRAY_A, $filter );

		// build the advanced sidebar search
		cp_show_refine_search( $ad_cat_array['term_id'] );

		// show all subcategories if any
		$args = array(
			'hide_empty' => 0,
			'show_count' => 1,
			'title_li' => '',
			'echo' => 0,
			'show_option_none' => 0,
			'taxonomy' => APP_TAX_CAT,
			'depth' => 1,
			'child_of' => $ad_cat_array['term_id'],
		);
		$subcats = wp_list_categories( $args );

		if ( ! empty( $subcats ) ) :
		?>
			<div class="shadowblock_out">

				<div class="shadowblock">

					<h2 class="dotted"><?php _e( 'Sub Categories', APP_TD ); ?></h2>

					<ul>
						<?php print_r( $subcats ); ?>
					</ul>

					<div class="clr"></div>

				</div><!-- /shadowblock -->

			</div><!-- /shadowblock_out -->

		<?php endif; ?>

	<?php } // is_tax ?>


<?php
	if ( is_search() ) {

		// build the advanced sidebar search
		cp_show_refine_search( get_query_var( 'scat' ) );

	} // is_search
?>


	<?php appthemes_before_sidebar_widgets(); ?>

	<?php if ( ! dynamic_sidebar( 'sidebar_main' ) ) : ?>

		<!-- no dynamic sidebar setup -->



	<?php endif; ?>

	<?php appthemes_after_sidebar_widgets(); ?>

</div><!-- /content_right -->