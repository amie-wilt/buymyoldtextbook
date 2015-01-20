<?php
/**
 * Widgets available for the theme
 *
 */


/**
 * Widget to show all ad categories.
 * @since 3.3
 */
class CP_Widget_Ad_Categories extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __( 'Display the ad categories.', APP_TD ), 'classname' => 'widget-ad-categories' );
		parent::__construct( 'widget-ad-categories', __( 'CP Ad Categories', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Ad Categories', APP_TD ) : $instance['title'] );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = null;

		$cat_args = array(
			'orderby' => 'name',
			'order' => 'ASC',
			'hierarchical' => 1,
			'show_count' => 1,
			'use_desc_for_title' => 0,
			'hide_empty' => 0,
			'depth' => 1,
			'number' => null,
			'title_li' => '',
			'taxonomy' => APP_TAX_CAT,
			'cp_number' => $number,
		);

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;		

		echo '<div class="recordfromblog"><ul>';

		add_filter( 'get_terms', 'cp_filter_limit_number_of_categories', 10, 3 );
		wp_list_categories( $cat_args );
		remove_filter( 'get_terms', 'cp_filter_limit_number_of_categories', 10, 3 );

		echo '</ul></div>';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 0 ) );
		$title = esc_attr( $instance['title'] );
		$number = absint( $instance['number'] );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of categories to show (0 for all):', APP_TD ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>
	<?php
	}
}


// callback function to limit number of categories
function cp_filter_limit_number_of_categories( $terms, $taxonomies, $args ) {
	if ( ! isset( $args['cp_number'] ) || is_null( $args['cp_number'] ) )
		return $terms;

	$i = 0;
	$number = absint( $args['cp_number'] );
	foreach ( (array) $terms as $key => $term ) {
		if ( $i >= $number || $term->parent != 0 ) {
			unset( $terms[ $key ] );
			continue;
		}
		$i++;
	}
	return $terms;
}


// widget to show the search widget
function cp_ad_search_widget() {
	global $cp_options;

	$args = array(
		'show_option_all' => __( 'All Categories', APP_TD ),
		'hierarchical' => $cp_options->cat_hierarchy,
		'hide_empty' => $cp_options->cat_hide_empty,
		'depth' => $cp_options->search_depth,
		'show_count' => $cp_options->cat_count,
		'pad_counts' => $cp_options->cat_count,
		'orderby' => 'name',
		'title_li' => '',
		'use_desc_for_title' => 1,
		'name' => 'scat',
		'selected' => cp_get_search_catid(),
		'taxonomy' => APP_TAX_CAT,
	);
	$args = apply_filters( 'cp_dropdown_search_widget_args', $args );
?>

	<div class="recordfromblog">

		<form action="<?php echo home_url( '/' ); ?>" method="get" id="searchform" class="form_search">

			<input name="s" type="text" id="s" class="editbox_search" <?php if(get_search_query()) { echo 'value="'.trim(strip_tags(esc_attr(get_search_query()))).'"'; } else { ?> value="<?php _e( 'What are you looking for?', APP_TD ); ?>" onfocus="if (this.value == '<?php _e( 'What are you looking for?', APP_TD ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'What are you looking for?', APP_TD ); ?>';}" <?php } ?> />

			<?php wp_dropdown_categories( $args ); ?>
			<div class="pad5"></div>
			<input type="submit" class="btn_orange" value="<?php _e( 'Search', APP_TD ); ?>" title="<?php _e( 'Search', APP_TD ); ?>" id="go" name="sa" />
		</form>

	</div><!-- /recordfromblog -->        

<?php
}


// widget to show all categories excluding the blog cats
function cp_ad_region_widget() {
	global $wpdb;
?>
	<div class="shadowblock_out">

		<div class="shadowblock">

			<h2 class="dotted"><?php _e( 'Filter by City', APP_TD ); ?></h2>

			<div class="recordfromblog">

				<ul>
					<?php

						//$all_custom_fields = get_post_custom($post->ID);

						// get all the custom field labels so we can match the field_name up against the post_meta keys
						$sql = "SELECT field_values FROM $wpdb->cp_ad_fields f WHERE f.field_name = 'cp_city'";

						//$results = $wpdb->get_results($sql);
						$results = $wpdb->get_row( $sql );


						if ( $results ) {
					?>

							<a href="?region=all"><?php _e( 'All', APP_TD ); ?></a> /
							<?php
								$options = explode( ',', $results->field_values );

								foreach ( $options as $option ) {
							?>
									<a href="?region=<?php echo $option; ?>"><?php echo $option; ?></a> /
							<?php
								}

						} else {

							_e( 'No cities found.', APP_TD );

						}
					?>
				</ul>

			</div><!-- /recordfromblog -->

		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->

<?php
}

// not using this quite yet b/c query_posts isn't working with
// meta_key and meta_values (as of 2.9.2) even though it should be
// i.e. query_posts('meta_key=color&meta_value=blue');
//$widget_ops = array('classname' => 'cp_ad_region_widget', 'description' => "" );
//wp_register_sidebar_widget('cp_ad_region_widget_id', 'CP Ad Region Filter', 'cp_ad_region_widget', $widget_ops);



// custom sidebar 125x125 ads widget
class AppThemes_Widget_125_Ads extends WP_Widget {

	function AppThemes_Widget_125_Ads() {
		$widget_ops = array( 'description' => __( 'Places an ad space in the sidebar for 125x125 ads', APP_TD ) );
		$control_ops = array('width' => 500, 'height' => 350);
		$this->WP_Widget( 'cp_125_ads', __( 'CP 125x125 Ads', APP_TD ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Sponsored Ads', APP_TD ) : $instance['title'] );
		$newin = isset( $instance['newin'] ) ? $instance['newin'] : false;


		if (isset($instance['ads'])) :

			// separate the ad line items into an array
			$ads = explode("\n", $instance['ads']);

			if (sizeof($ads)>0) :

				echo $before_widget;

				if ($title) echo $before_title . $title . $after_title;
				if ($newin) $newin = 'target="_blank"';
			?>

				<ul class="ads">
			<?php
				$alt = 1;
				foreach ($ads as $ad) :
					if ($ad && strstr($ad, '|')) {
						$alt = $alt*-1;
						$this_ad = explode('|', $ad);
						echo '<li class="';
						if ($alt==1) echo 'alt';
						echo '"><a href="'.$this_ad[0].'" rel="'.$this_ad[3].'" '.$newin.'><img src="'.$this_ad[1].'" width="125" height="125" alt="'.$this_ad[2].'" /></a></li>';
					}
				endforeach;
			?>
				</ul>

				<?php
				echo $after_widget;

			endif;

		endif;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ads'] = strip_tags( $new_instance['ads'] );
		$instance['newin'] = $new_instance['newin'];

		return $instance;
	}

	function form( $instance ) {

		// load up the default values
		$default_ads = "http://www.appthemes.com|".get_template_directory_uri()."/images/ad125a.gif|Ad 1|nofollow\n"."http://www.appthemes.com|".get_template_directory_uri()."/images/ad125b.gif|Ad 2|follow\n"."http://www.appthemes.com|".get_template_directory_uri()."/images/ad125a.gif|Ad 3|nofollow\n"."http://www.appthemes.com|".get_template_directory_uri()."/images/ad125b.gif|Ad 4|follow";
		$defaults = array( 'title' => __( 'Sponsored Ads', APP_TD ), 'ads' => $default_ads, 'rel' => true, 'newin' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); 		
?>
		<p>
			<label><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label><?php _e( 'Ads:', APP_TD ); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('ads'); ?>" cols="5" rows="5"><?php echo $instance['ads']; ?></textarea>
			<?php _e( 'Enter one ad entry per line in the following format:', APP_TD ); ?><br />
			<code><?php _e( 'URL|Image URL|Image Alt Text|rel', APP_TD ); ?></code><br />
			<?php _e( '<strong>Note:</strong> You must hit your &quot;enter/return&quot; key after each ad entry otherwise the ads will not display properly.', APP_TD ); ?>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['newin'], 'on'); ?> id="<?php echo $this->get_field_id('newin'); ?>" name="<?php echo $this->get_field_name('newin'); ?>" />
			<label><?php _e( 'Open ads in a new window?', APP_TD ); ?></label>
		</p>
<?php
	}
}


// facebook like box sidebar widget
class AppThemes_Widget_Facebook extends WP_Widget {

	function AppThemes_Widget_Facebook() {
		$widget_ops = array( 'description' => __( 'This places a Facebook page Like Box in your sidebar to attract and gain Likes from visitors.', APP_TD ) );
		$this->WP_Widget( 'cp_facebook_like', __( 'CP Facebook Like Box', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$fid = $instance['fid'];
		$connections = $instance['connections'];
		$width = $instance['width'];
		$height = $instance['height'];

		echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;

	?>
		<div class="pad5"></div>
		<iframe src="http://www.facebook.com/plugins/likebox.php?id=<?php echo urlencode($fid); ?>&amp;connections=<?php echo urlencode($connections); ?>&amp;stream=false&amp;header=true&amp;width=<?php echo urlencode($width); ?>&amp;height=<?php echo $height; ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo esc_attr($width); ?>px; height:<?php echo esc_attr($height); ?>px;" allowTransparency="true"></iframe>
		<div class="pad5"></div>
	<?php

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['fid'] = strip_tags( $new_instance['fid'] );
		$instance['connections'] = strip_tags($new_instance['connections']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);

		return $instance;
	}

	function form($instance) {

		$defaults = array( 'title' => __( 'Facebook Friends', APP_TD ), 'fid' => '137589686255438', 'connections' => '10', 'width' => '305', 'height' => '290' );
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('fid'); ?>"><?php _e( 'Facebook ID:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('fid'); ?>" name="<?php echo $this->get_field_name('fid'); ?>" value="<?php echo $instance['fid']; ?>" />
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" value="<?php echo $instance['connections']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('connections'); ?>"><?php _e( 'Connections', APP_TD ); ?></label>			
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Width', APP_TD ); ?></label>
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e( 'Height', APP_TD ); ?></label>
		</p>

	<?php
	}
}


// custom sidebar blog posts widget
class AppThemes_Widget_Blog_Posts extends WP_Widget {

	function AppThemes_Widget_Blog_Posts() {
		$widget_ops = array( 'description' => __( 'Your most recent blog posts', APP_TD ) );
		$this->WP_Widget( 'cp_recent_posts', __( 'CP Recent Blog Posts', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		global $post;

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$count = $instance['count'];

		if ( !is_numeric($count) ) $count = 5;

		echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;

		// include the main blog loop
		appthemes_load_template( 'includes/sidebar-blog-posts.php', array( 'count' => $count ) );

		echo $after_widget;

	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = (trim(strip_tags($new_instance['count'])));

		return $instance;
	}

	function form( $instance ) {

		// load up the default values
		$defaults = array( 'title' => __( 'From the Blog', APP_TD ), 'count' => 5 );
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Posts Shown', APP_TD ); ?></label>
		</p>


<?php
	}
}


// classipress sidebar search widget
class CP_Widget_Search extends WP_Widget {

	function CP_Widget_Search() {
		$widget_ops = array( 'description' => __( 'Your sidebar ad search box', APP_TD ) );
		$this->WP_Widget('ad_search', __( 'CP Ad Search Box', APP_TD ), $widget_ops);
	}

	function widget( $args, $instance ) {

		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Search Classified Ads', APP_TD ) : $instance['title']);

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		//echo '<div>';
		cp_ad_search_widget();
		//echo "</div>\n";
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	function form($instance) {
?>
    <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ($instance['title'])) {echo esc_attr( $instance['title']);} ?>" />
		</p>
<?php
	}
}


// classipress sidebar top ads widget
class CP_Widget_Top_Ads_Today extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __( 'Display the top ads today.', APP_TD ), 'classname' => 'widget-top-ads-today' );
		parent::__construct( 'top_ads', __( 'CP Top Ads Today', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Popular Ads Today', APP_TD ) : $instance['title'] );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		cp_todays_count_widget( APP_POST_TYPE, $number );

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 10 ) );
		$title = esc_attr( $instance['title'] );
		$number = absint( $instance['number'] );
?>
    <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of ads to show:', APP_TD ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>
<?php
	}
}


// classipress sidebar top ads widget
class CP_Widget_Top_Ads_Overall extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __( 'Display the top ads overall.', APP_TD ), 'classname' => 'widget-top-ads-overall' );
		parent::__construct( 'top_ads_overall', __( 'CP Top Ads Overall', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Popular Ads Overall', APP_TD ) : $instance['title'] );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		cp_todays_overall_count_widget( APP_POST_TYPE, $number );

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 10 ) );
		$title = esc_attr( $instance['title'] );
		$number = absint( $instance['number'] );
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of ads to show:', APP_TD ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>
<?php
	}
}


// widget to show all categories excluding the blog cats
// deprecated since 3.0.5
function cp_ad_sponsors_widget() {
?>

	<div class="shadowblock_out">

		<div class="shadowblock">

			<h2 class="dotted"><?php _e( 'Site Sponsors', APP_TD ); ?></h2>
			<a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/ad320.gif" width="307" height="96" alt="ad" class="fineborder ad320" /></a>
			<a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/ad125a.gif" width="125" height="125" alt="ad" class="fineborder ad125l" /></a>
			<a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/ad125b.gif" width="125" height="125" alt="ad" class="fineborder ad125r" /></a>

			<div class="clr"></div>

		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->

<?php
}
//$widget_ops = array('classname' => 'cp_ad_sponsors_widget', 'description' => "" );
//wp_register_sidebar_widget('cp_ad_sponsors_widget_id', 'CP Ad Sponsors', 'cp_ad_sponsors_widget', $widget_ops);


// ad tags and categories cloud widget
class CP_Widget_Ads_Tag_Cloud extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __( 'Your most used ad tags in cloud format.', APP_TD ), 'classname' => 'widget_tag_cloud' );
		parent::__construct( 'ad_tag_cloud', __( 'CP Ads Tag Cloud', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 45;

		$current_taxonomy = ( empty( $instance['taxonomy'] ) || ! taxonomy_exists( $instance['taxonomy'] ) ) ? 'post_tag' : $instance['taxonomy'];
		if ( ! empty( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			if ( APP_TAX_TAG == $current_taxonomy ) {
				$title = __( 'Ad Tags', APP_TD );
			} else {
				$tax = get_taxonomy( $current_taxonomy );
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters( 'widget_title', $title );

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		echo '<div>';
		wp_tag_cloud( apply_filters( 'widget_tag_cloud_args', array( 'taxonomy' => $current_taxonomy, 'number' => $number ) ) );
		echo "</div>\n";

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 45, 'taxonomy' => APP_TAX_TAG ) );
		$title = esc_attr( $instance['title'] );
		$number = absint( $instance['number'] );
		$current_taxonomy = ( taxonomy_exists( $instance['taxonomy'] ) ) ? $instance['taxonomy'] : 'post_tag';
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e( 'Taxonomy:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
			<?php
				foreach ( get_object_taxonomies( APP_POST_TYPE ) as $taxonomy ) {
					$tax = get_taxonomy( $taxonomy );
					if ( ! $tax->show_tagcloud || empty( $tax->labels->name ) )
						continue;
			?>
					<option value="<?php echo esc_attr( $taxonomy ); ?>" <?php selected( $taxonomy, $current_taxonomy ); ?>><?php echo esc_attr( $tax->labels->name ); ?></option>
			<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of items to show:', APP_TD ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>
	<?php
	}

}


/**
 * Widget displays featured ads.
 * @since 3.3
 */
class CP_Widget_Featured_Ads extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __( 'Display the featured ads.', APP_TD ), 'classname' => 'widget-featured-ads' );
		parent::__construct( 'widget-featured-ads', __( 'CP Featured Ads', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Featured Ads', APP_TD ) : $instance['title'] );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		$featured_ads = new WP_Query( array( 'post__in' => get_option('sticky_posts'), 'post_type' => APP_POST_TYPE, 'posts_per_page' => $number, 'orderby' => 'rand' ) );
		$result = '';

		if ( $featured_ads->have_posts() ) {
			$result .= '<ul>';
			while ( $featured_ads->have_posts() ) {
				$featured_ads->the_post();
				$result .= '<li><a href="' . get_permalink( get_the_ID() ) . '">' . get_the_title() . '</a></li>';
			}
			$result .= '</ul>';
		}

		wp_reset_query();
		wp_reset_postdata();

		echo $result;

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 10 ) );
		$title = esc_attr( $instance['title'] );
		$number = absint( $instance['number'] );
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of ads to show:', APP_TD ); ?></label>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
		<?php
	}
}


/**
 * Widget displays ads marked as sold.
 * @since 3.3
 */
class CP_Widget_Sold_Ads extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __( 'Display the ads marked as sold.', APP_TD ), 'classname' => 'widget-sold-ads' );
		parent::__construct( 'widget-sold-ads', __( 'CP Sold Ads', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Sold Ads', APP_TD ) : $instance['title'] );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		$ads_args = array(
			'post_type' => APP_POST_TYPE,
			'posts_per_page' => $number,
			'meta_query' => array(
				array(
					'key' => 'cp_ad_sold',
					'value' => 'yes',
					'compare' => '=',
				),
			),
		);

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		$sold_ads = new WP_Query( $ads_args );
		$result = '';

		if ( $sold_ads->have_posts() ) {
			$result .= '<ul>';
			while ( $sold_ads->have_posts() ) {
				$sold_ads->the_post();
				$result .= '<li><a href="' . get_permalink( get_the_ID() ) . '">' . get_the_title() . '</a></li>';
			}
			$result .= '</ul>';
		}

		wp_reset_query();
		wp_reset_postdata();

		echo $result;

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 10 ) );
		$title = esc_attr( $instance['title'] );
		$number = absint( $instance['number'] );
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of ads to show:', APP_TD ); ?></label>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
		<?php
	}
}


// register the custom sidebar widgets
function cp_widgets_init() {

	register_widget('AppThemes_Widget_125_Ads');
	register_widget('AppThemes_Widget_Blog_Posts');
	register_widget('AppThemes_Widget_Facebook');
	register_widget('CP_Widget_Search');
	register_widget('CP_Widget_Top_Ads_Today');
	register_widget('CP_Widget_Top_Ads_Overall');
	register_widget('CP_Widget_Ads_Tag_Cloud');
	register_widget('CP_Widget_Ad_Categories');
	register_widget('CP_Widget_Featured_Ads');
	register_widget('CP_Widget_Sold_Ads');

}
add_action( 'widgets_init', 'cp_widgets_init' );


// remove some of the default sidebar widgets
function cp_unregister_widgets() {
	//unregister_widget('WP_Widget_Pages');
	//unregister_widget('WP_Widget_Calendar');
	//unregister_widget('WP_Widget_Archives');
	//unregister_widget('WP_Widget_Links');
	//unregister_widget('WP_Widget_Categories');
	//unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Search');
	//unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('P2P_Widget');
}
add_action( 'widgets_init', 'cp_unregister_widgets', 11 );

