<?php
/**
 *
 * Keeps track of ad views for daily and total
 * @author AppThemes
 *
 *
 */

// sidebar widget showing overall popular ads
function cp_todays_overall_count_widget( $post_type, $limit ) {

	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $limit,
		'paged' => 1,
	);

	$popular = new CP_Popular_Posts_Query( $args );

	echo '<ul class="pop">';

	// must be overall views
	if ( $popular->have_posts() ) {

		while ( $popular->have_posts() ) {
			$popular->the_post();
			echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . appthemes_get_stats_by( get_the_ID(), 'total' ) . '&nbsp;' . __( 'views', APP_TD ) . ')</li>';
		}

	} else {

		echo '<li>' . __( 'No ads viewed yet.', APP_TD ) . '</li>';

	}

	echo '</ul>';

	wp_reset_postdata();
}

// sidebar widget showing today's popular ads
function cp_todays_count_widget( $post_type, $limit ) {

	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $limit,
		'paged' => 1,
	);

	$popular = new CP_Popular_Posts_Query( $args, 'today' );

	echo '<ul class="pop">';

	// must be views today
	if ( $popular->have_posts() ) {

		while ( $popular->have_posts() ) {
			$popular->the_post();
			echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . appthemes_get_stats_by( get_the_ID(), 'today' ) . '&nbsp;' . __( 'views', APP_TD ) . ')</li>';
		}

	} else {

		echo '<li>' . __( 'No ads viewed yet.', APP_TD ) . '</li>';
	}

	echo '</ul>';

	wp_reset_postdata();
}


