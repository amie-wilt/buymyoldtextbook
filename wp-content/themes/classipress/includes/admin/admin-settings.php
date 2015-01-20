<?php

class CP_Theme_Settings_General extends APP_Tabs_Page {

	function setup() {
		$this->textdomain = APP_TD;

		$this->args = array(
			'page_title' => __( 'ClassiPress Settings', APP_TD ),
			'menu_title' => __( 'Settings', APP_TD ),
			'page_slug' => 'app-settings',
			'parent' => 'app-dashboard',
			'screen_icon' => 'options-general',
			'admin_action_priority' => 10,
		);

		add_action( 'admin_notices', array( $this, 'admin_tools' ) );

	}


	public function admin_tools() {

		if ( isset( $_GET['pruneads'] ) && $_GET['pruneads'] == 1 ) {
			cp_check_expired_cron();
			echo scb_admin_notice( __( 'Expired ads have been pruned.', APP_TD ) );
		}

		if ( isset( $_GET['resetstats'] ) && $_GET['resetstats'] == 1 ) {
			appthemes_reset_stats();
			echo scb_admin_notice( __( 'Statistics have been reseted.', APP_TD ) );
		}

		// flush out the cache so changes can be visible
		cp_flush_all_cache();

	}


	protected function init_tabs() {
		$this->tabs->add( 'general', __( 'General', APP_TD ) );
		$this->tabs->add( 'listings', __( 'Listings', APP_TD ) );
		$this->tabs->add( 'security', __( 'Security', APP_TD ) );
		$this->tabs->add( 'advertise', __( 'Advertising', APP_TD ) );
		$this->tabs->add( 'advanced', __( 'Advanced', APP_TD ) );

		$this->tab_sections['general']['configuration'] = array(
			'title' => __( 'Site Configuration', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Home Page Layout', APP_TD ),
					'type' => 'select',
					'name' => 'home_layout',
					'values' => array(
						'standard' => __( 'Standard Style', APP_TD ),
						'directory' => __( 'Directory Style', APP_TD ),
					),
					'tip' => __( 'Select the layout you prefer for your home page. The directory style is a more traditional classified ads layout. The standard style is more like a blog layout.', APP_TD ),
				),
				array(
					'title' => __( 'Color Scheme', APP_TD ),
					'type' => 'select',
					'name' => 'stylesheet',
					'values' => array(
						'aqua.css' => __( 'Aqua Theme', APP_TD ),
						'blue.css' => __( 'Blue Theme', APP_TD ),
						'green.css' => __( 'Green Theme', APP_TD ),
						'red.css' => __( 'Red Theme', APP_TD ),
						'teal.css' => __( 'Teal Theme', APP_TD ),
						'aqua-black.css' => __( 'Aqua Theme - Black Header', APP_TD ),
						'blue-black.css' => __( 'Blue Theme - Black Header', APP_TD ),
						'green-black.css' => __( 'Green Theme - Black Header', APP_TD ),
						'red-black.css' => __( 'Red Theme - Black Header', APP_TD ),
						'teal-black.css' => __( 'Teal Theme - Black Header', APP_TD ),
					),
					'tip' => __( 'Select the color scheme you would like your classified ads site to use.', APP_TD ),
				),
				array(
					'title' => __( 'User Set Password', APP_TD ),
					'name' => 'allow_registration_password',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Turning this off will send the user a password instead of letting them set it on the new user registration page.', APP_TD ),
				),
				array(
					'title' => __( 'Enable Logo', APP_TD ),
					'name' => 'use_logo',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'If you do not have a logo to use, uncheck the box and this will display the title and description of your web site instead.', APP_TD ),
				),
				array(
					'title' => __( 'Web Site Logo', APP_TD ),
					'desc' => $this->wrap_upload( 'logo', '<br />' . __( 'Upload a logo or paste an image URL directly.', APP_TD ) ),
					'type' => 'text',
					'name' => 'logo',
					'tip' => __( 'Paste the URL of your web site logo image here. It will replace the default header logo. (i.e. http://www.yoursite.com/logo.jpg)', APP_TD ),
				),
				array(
					'title' => __( 'Feedburner URL', APP_TD ),
					'desc' => '<br />' . sprintf( '%s' . __( 'Sign up for a free <a target="_new" href="%s">Feedburner account</a>.', APP_TD ), '<div class="feedburnerico"></div>', 'http://feedburner.google.com' ),
					'type' => 'text',
					'name' => 'feedburner_url',
					'tip' => __( 'Paste your Feedburner address here. It will automatically redirect your default RSS feed to Feedburner. You must have a Google Feedburner account setup first.', APP_TD ),
				),
				array(
					'title' => __( 'Twitter Username', APP_TD ),
					'desc' => '<br />' . sprintf( '%s' . __( 'Sign up for a free <a target="_new" href="%s">Twitter account</a>.', APP_TD ), '<div class="twitterico"></div>', 'http://twitter.com' ),
					'type' => 'text',
					'name' => 'twitter_username',
					'tip' => __( 'Paste your Twitter username here. It will automatically redirect people who click on your Twitter link to your Twitter page. You must have a Twitter account setup first.', APP_TD ),
				),
				array(
					'title' => __( 'Facebook Page ID', APP_TD ),
					'desc' => '<br />' . sprintf( '%s' . __( 'Sign up for a free <a target="_new" href="%s">Facebook account</a>.', APP_TD ), '<div class="facebookico"></div>', 'http://www.facebook.com' ),
					'type' => 'text',
					'name' => 'facebook_id',
					'tip' => __( 'Paste your Facebook Page ID or username here. It will display Facebook icon with URL to you page in the top bar. You must have a Facebook account and page setup first.', APP_TD ),
				),
				array(
					'title' => __( 'Tracking Code', APP_TD ),
					'desc' => '<br />' . sprintf( '%s' . __( 'Sign up for a free <a target="_new" href="%s">Google Analytics account</a>.', APP_TD ), '<div class="googleico"></div>', 'http://www.google.com/analytics/' ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'google_analytics',
					'extra' => array(
						'style' => 'width: 300px; height: 100px;'
					),
					'tip' => __( 'Paste your analytics tracking code here. Google Analytics is free and the most popular but you can use other providers as well.', APP_TD ),
				),
			),
		);

		$this->tab_sections['general']['google_maps'] = array(
			'title' => __( 'Google Maps Settings', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Region Biasing', APP_TD ),
					'desc' => sprintf( __( 'Find your two-letter ISO 3166-1 region code <a href="%s">here</a>.', APP_TD ), 'http://en.wikipedia.org/wiki/ISO_3166-1' ),
					'type' => 'text',
					'name' => 'gmaps_region',
					'extra' => array( 'size' => 2 ),
					'tip' => __( "When a user enters 'Florence' in the location search field, you can let Google know that they probably meant 'Florence, Italy' rather than 'Florence, Alabama'.", APP_TD ),
				),
				array(
					'title' => __( 'Language', APP_TD ),
					'desc' => sprintf( __( 'Find your two-letter language code <a href="%s">here</a>.', APP_TD ), 'https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1' ),
					'type' => 'text',
					'name' => 'gmaps_lang',
					'extra' => array( 'size' => 5 ),
					'tip' => __( 'Used to let Google know to use this language in the formatting of addresses and for the map controls.', APP_TD ),
				),
				array(
					'title' => __( 'Distance Unit', APP_TD ),
					'type' => 'radio',
					'name' => 'distance_unit',
					'values' => array(
						'km' => __( 'Kilometers', APP_TD ),
						'mi' => __( 'Miles', APP_TD ),
					),
					'tip' => __( "Use Kilometers or Miles for your site's unit of measure for distances.", APP_TD ),
				),
			),
		);

		$this->tab_sections['general']['search_settings'] = array(
			'title' => __( 'Search Settings', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Exclude Pages', APP_TD ),
					'name' => 'search_ex_pages',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Should the pages be excluded from your website search results?', APP_TD ),
				),
				array(
					'title' => __( 'Exclude Blog Posts', APP_TD ),
					'name' => 'search_ex_blog',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Should the blog posts be excluded from your website search results?', APP_TD ),
				),
				array(
					'title' => __( 'Search Custom Fields', APP_TD ),
					'name' => 'search_custom_fields',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Do you want to search a phrase in all custom fields? This option may slow down search engine on your website due to complex database queries.', APP_TD ),
				),
				array(
					'title' => __( 'Search Field Width', APP_TD ),
					'type' => 'text',
					'name' => 'search_field_width',
					'tip' => __( 'Sometimes the search bar text field is too long and gets pushed down becuase of the categories drop-down. This option allows you to manually adjust it. Note: value must be numeric followed by either px or % (i.e. 600px or 50%).', APP_TD ),
				),
			),
		);

		$this->tab_sections['general']['search_dropdown'] = array(
			'title' => __( 'Search Drop-down Options', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Category Depth', APP_TD ),
					'type' => 'select',
					'name' => 'search_depth',
					'values' => array(
						'0' => __( 'Show All', APP_TD ),
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
					),
					'tip' => __( "This sets the depth of categories shown in the category drop-down. Use 'Show All' unless you have a lot of sub-categories and do not want them all listed.", APP_TD ),
				),
				array(
					'title' => __( 'Category Hierarchy', APP_TD ),
					'name' => 'cat_hierarchy',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will indent sub-categories within the category drop-down vs showing them all flat.', APP_TD ),
				),
				array(
					'title' => __( 'Show Ad Count', APP_TD ),
					'name' => 'cat_count',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will show an ad total next to each category name in the category drop-down.', APP_TD ),
				),
				array(
					'title' => __( 'Hide Empty Categories', APP_TD ),
					'name' => 'cat_hide_empty',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will hide any empty categories within the category drop-down.', APP_TD ),
				),
			),
		);

		$this->tab_sections['general']['category_menu_options'] = array(
			'title' => __( 'Categories Menu Item Options', APP_TD ),
			'fields' => $this->categories_options( 'cat_menu' ),
		);

		$this->tab_sections['general']['category_dir_options'] = array(
			'title' => __( 'Categories Page Options', APP_TD ),
			'fields' => $this->categories_options( 'cat_dir' ),
		);

		$this->tab_sections['general']['messages'] = array(
			'title' => __( 'Classified Ads Messages', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Home Page Message', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'ads_welcome_msg',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'This welcome message will appear in the sidebar of your home page. (HTML is allowed)', APP_TD ),
				),
				array(
					'title' => __( 'New Ad Message', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'ads_form_msg',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'This message will appear at the top of the classified ads listing page. (HTML is allowed)', APP_TD ),
				),
				array(
					'title' => __( 'Membership Purchase Message', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'membership_form_msg',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'This message will appear at the top of the classified ads listing page. (HTML is allowed)', APP_TD ),
				),
				array(
					'title' => __( 'Terms of Use', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'ads_tou_msg',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'This message will appear on the last step of your classified ad listing page. This is usually your legal disclaimer or rules for posting new ads on your site. (HTML is allowed)', APP_TD ),
				),
			),
		);


		$this->tab_sections['listings']['configuration'] = array(
			'title' => __( 'Classified Ads Configuration', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Allow Ad Editing', APP_TD ),
					'name' => 'ad_edit',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Allows the ad owner to edit and republish their existing ads from their dashboard.', APP_TD ),
				),
				array(
					'title' => __( 'Allow Ad Relisting', APP_TD ),
					'name' => 'allow_relist',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Allows the ad owner to relist and pay for their expired ads. They will receive an ad expiration email with a link to their dashboard to relist an ad.', APP_TD ),
				),
				array(
					'title' => __( 'Allow Parent Category Posting', APP_TD ),
					'type' => 'radio',
					'name' => 'ad_parent_posting',
					'values' => array(
						'yes' => __( 'Yes', APP_TD ),
						'whenEmpty' => __( 'When Empty', APP_TD ),
						'no' => __( 'No', APP_TD ),
					),
					'tip' => __( "Allows ad poster to post to top-level categories. If set to 'When Empty', it allows posting to top-level categories only if they have no child categories.", APP_TD ),
				),
				array(
					'title' => __( 'Ad Inquiry Form Requires Login', APP_TD ),
					'name' => 'ad_inquiry_form',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Require visitors to be logged in before they can fill out the ad inquiry form. In most cases you should keep this set to no to encourage visitors to ask questions without having to create an account.', APP_TD ),
				),
				array(
					'title' => __( 'Allow HTML', APP_TD ),
					'name' => 'allow_html',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Turns on the TinyMCE editor on text area fields and allows the ad owner to use html markup. Other fields do not allow html by default.', APP_TD ),
				),
				array(
					'title' => __( 'Show Ad Views Counter', APP_TD ),
					'name' => 'ad_stats_all',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( "This will show a 'total views' and 'today's views' at the bottom of each ad listing and blog post.", APP_TD ),
				),
				array(
					'title' => __( 'Show Gravatar Thumbnail', APP_TD ),
					'name' => 'ad_gravatar_thumb',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( "This will show a picture of the author next to their name on each ad listing block. A placeholder image will be used if they don't have one. Note: Enabling this may slow down your site.", APP_TD ),
				),
				array(
					'title' => __( 'Moderate Ads', APP_TD ),
					'type' => 'radio',
					'name' => 'post_status',
					'values' => array(
						'pending' => __( 'Yes', APP_TD ),
						'publish' => __( 'No', APP_TD ),
					),
					'tip' =>
						__( '<i>Yes</i> - You have to manually approve and publish each ad.', APP_TD ) . '<br />' .
						__( '<i>No</i> - Ad goes live immediately without any approvals unless it has not been paid for.', APP_TD ),
				),
				array(
					'title' => __( 'Prune Ads', APP_TD ),
					'name' => 'post_prune',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Automatically removes all listings from your site as they expire and changes the post status to draft. The frequency is based on the Cron Job Schedule set below and after each ad listing is past the expiration date. If this is disabled, the ad will remain live on your site but will be marked as expired.', APP_TD ),
				),
				array(
					'title' => __( 'Cron Job Schedule', APP_TD ),
					'type' => 'select',
					'name' => 'ad_expired_check_recurrance',
					'desc' => __( 'Note: This feature only works if you have enabled the Prune Ads option.', APP_TD ),
					'values' => array(
						'none' => __( 'None', APP_TD ),
						'hourly' => __( 'Hourly', APP_TD ),
						'twicedaily' => __( 'Twice Daily', APP_TD ),
						'daily' => __( 'Daily', APP_TD ),
					),
					'tip' => __( 'Frequency you would like ClassiPress to check for and take offline any expired ads. Twice daily is recommended. Hourly may cause performance issues if you have a lot of ads.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Listing Period', APP_TD ),
					'type' => 'text',
					'name' => 'prun_period',
					'extra' => array( 'size' => 5 ),
					'tip' => __( 'Number of days each ad will be listed on your site. This option is overridden by ad packs if you are charging for ads and using the Fixed Price Per Ad option. ', APP_TD ),
				),
				array(
					'title' => __( 'Form Validation Language', APP_TD ),
					'desc' => __( 'Leave this value blank if your site is in English.', APP_TD ),
					'type' => 'text',
					'name' => 'form_val_lang',
					'extra' => array( 'size' => 5 ),
					'tip' => __( 'This option allows you to set the language your ad submission form error messages are displayed in. Enter your two-letter country code (i.e. for German enter de). Not all languages have been translated but you can always add your own. To see the available languages, look in your /classipress/includes/js/validate/localization folder.', APP_TD ),
				),
			),
		);

		$this->tab_sections['listings']['images'] = array(
			'title' => __( 'Ad Images Options', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Allow Ad Images', APP_TD ),
					'name' => 'ad_images',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Allows the ad owner to upload and use images on their ad. Note: This will disable display of most ad images across the entire site but some images may still display.', APP_TD ),
				),
				array(
					'title' => __( 'Show Preview Image', APP_TD ),
					'name' => 'ad_image_preview',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Displays a larger image when you mouse over the smaller thumbnail image. This is on your home page, category, search results, etc. ', APP_TD ),
				),
				array(
					'title' => __( 'Max Images Per Ad', APP_TD ),
					'type' => 'select',
					'name' => 'num_images',
					'values' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
					),
					'tip' => __( 'The number of images the ad owner can upload with each of their ads.', APP_TD ),
				),
				array(
					'title' => __( 'Max Size Per Image', APP_TD ),
					'type' => 'select',
					'name' => 'max_image_size',
					'values' => array(
						'100' => '100KB',
						'250' => '250KB',
						'500' => '500KB',
						'1024' => '1MB',
						'2048' => '2MB',
						'5120' => '5MB',
						'7168' => '7MB',
						'10240' => '10MB',
					),
					'tip' => __( 'The maximum image size (per image) the ad owner can upload with each of their ads.', APP_TD ),
				),
			),
		);


		$this->tab_sections['security']['settings'] = array(
			'title' => __( 'Security Settings', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Back Office Access', APP_TD ),
					'desc' => '<br />' . sprintf( __( "View the WordPress <a target='_new' href='%s'>Roles and Capabilities</a> for more information.", APP_TD ), 'http://codex.wordpress.org/Roles_and_Capabilities' ),
					'type' => 'select',
					'name' => 'admin_security',
					'values' => array(
						'manage_options' => __( 'Admins Only', APP_TD ),
						'edit_others_posts' => __( 'Admins, Editors', APP_TD ),
						'publish_posts' => __( 'Admins, Editors, Authors', APP_TD ),
						'edit_posts' => __( 'Admins, Editors, Authors, Contributors', APP_TD ),
						'read' => __( 'All Access', APP_TD ),
						'disable' => __( 'Disable', APP_TD ),
					),
					'tip' => __( 'Allows you to restrict access to the WordPress Back Office (wp-admin) by specific role. Keeping this set to admins only is recommended. Select Disable if you have problems with this feature.', APP_TD ),
				),
			),
		);

		$this->tab_sections['security']['recaptcha'] = array(
			'title' => __( 'reCaptcha Settings', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable reCaptcha', APP_TD ),
					'name' => 'captcha_enable',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ) . '<br />' . sprintf( __( "reCaptcha is a free anti-spam service provided by Google. Learn more about <a target='_new' href='%s'>reCaptcha</a>.", APP_TD ), 'http://code.google.com/apis/recaptcha/' ),
					'tip' => __( 'Enables the reCaptcha service that will protect your site against spam registrations. It will show a verification box on your registration page that requires a human to read and enter the words.', APP_TD ),
				),
				array(
					'title' => __( 'reCaptcha Public Key', APP_TD ),
					'desc' => '<br />' . sprintf( '%s' . __( 'Sign up for a free <a target="_new" href="%s">Google reCaptcha</a> account.', APP_TD ), '<div class="captchaico"></div>', 'https://www.google.com/recaptcha/admin/create' ),
					'type' => 'text',
					'name' => 'captcha_public_key',
					'tip' => __( 'Enter your public key here to enable an anti-spam service on your new user registration page (requires a free Google reCaptcha account). Leave it blank if you do not wish to use this anti-spam feature.', APP_TD ),
				),
				array(
					'title' => __( 'reCaptcha Private Key', APP_TD ),
					'desc' => '<br />' . sprintf( '%s' . __( 'Sign up for a free <a target="_new" href="%s">Google reCaptcha</a> account.', APP_TD ), '<div class="captchaico"></div>', 'https://www.google.com/recaptcha/admin/create' ),
					'type' => 'text',
					'name' => 'captcha_private_key',
					'tip' => __( 'Enter your private key here to enable an anti-spam service on your new user registration page (requires a free Google reCaptcha account). Leave it blank if you do not wish to use this anti-spam feature.', APP_TD ),
				),
				array(
					'title' => __( 'Choose Theme', APP_TD ),
					'type' => 'select',
					'name' => 'captcha_theme',
					'values' => array(
						'red' => __( 'Red', APP_TD ),
						'white' => __( 'White', APP_TD ),
						'blackglass' => __( 'Black', APP_TD ),
						'clean' => __( 'Clean', APP_TD ),
					),
					'tip' => __( 'Select the color scheme you wish to use for reCaptcha.', APP_TD ),
				),
			),
		);


		$this->tab_sections['advertise']['header'] = array(
			'title' => __( 'Header Ad (468x60)', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable Ad', APP_TD ),
					'name' => 'adcode_468x60_enable',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Disable this option if you do not wish to have a 468x60 ad banner displayed.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Code', APP_TD ),
					'desc' => '<br />' . sprintf( __( 'Paste your ad code here. Supports many popular providers such as <a target="_new" href="%s">Google AdSense</a> and <a target="_new" href="%s">BuySellAds</a>.', APP_TD ), 'http://www.google.com/adsense/', 'http://www.buysellads.com/' ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'adcode_468x60',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'You may use html and/or javascript code provided by Google AdSense.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Image URL', APP_TD ),
					'desc' => $this->wrap_upload( 'adcode_468x60_url', '<br />' . __( 'Upload your ad creative or paste the ad creative URL directly.', APP_TD ) ),
					'type' => 'text',
					'name' => 'adcode_468x60_url',
					'tip' => __( 'If you would rather use an image ad instead of code provided by your advertiser, use this field instead.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Destination', APP_TD ),
					'desc' => '<br />' . __( 'Paste the destination URL of your custom ad creative here (i.e. http://www.yoursite.com/landing-page.html).', APP_TD ),
					'type' => 'text',
					'name' => 'adcode_468x60_dest',
					'tip' => __( 'When a visitor clicks on your ad image, this is the destination they will be sent to.', APP_TD ),
				),
			),
		);

		$this->tab_sections['advertise']['content'] = array(
			'title' => __( 'Content Ad (336x280)', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable Ad', APP_TD ),
					'name' => 'adcode_336x280_enable',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Disable this option if you do not wish to have a 336x280 ads displayed on single ad, category, or search result pages.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Code', APP_TD ),
					'desc' => '<br />' . sprintf( __( 'Paste your ad code here. Supports many popular providers such as <a target="_new" href="%s">Google AdSense</a> and <a target="_new" href="%s">BuySellAds</a>.', APP_TD ), 'http://www.google.com/adsense/', 'http://www.buysellads.com/' ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'adcode_336x280',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'You may use html and/or javascript code provided by Google AdSense.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Image URL', APP_TD ),
					'desc' => $this->wrap_upload( 'adcode_336x280_url', '<br />' . __( 'Upload your ad creative or paste the ad creative URL directly.', APP_TD ) ),
					'type' => 'text',
					'name' => 'adcode_336x280_url',
					'tip' => __( 'If you would rather use an image ad instead of code provided by your advertiser, use this field instead.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Destination', APP_TD ),
					'desc' => '<br />' . __( 'Paste the destination URL of your custom ad creative here (i.e. http://www.yoursite.com/landing-page.html).', APP_TD ),
					'type' => 'text',
					'name' => 'adcode_336x280_dest',
					'tip' => __( 'When a visitor clicks on your ad image, this is the destination they will be sent to.', APP_TD ),
				),
			),
		);


		$this->tab_sections['advanced']['settings'] = array(
			'title' => __( 'Advanced Options', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Run Ads Expired Check', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( 'Prune <a href="%s">Expired Ads</a> now.', APP_TD ), 'admin.php?page=app-settings&pruneads=1' ),
					'extra' => array(
						'style' => 'display: none;'
					),
					'tip' => __( 'Click the link to manually run the function that checks all ads expiration and prunes any ads that are expired. This event will run only one time.', APP_TD ),
				),
				array(
					'title' => __( 'Reset Stats', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( '<a href="%s">Reset Stats</a> count now.', APP_TD ), 'admin.php?page=app-settings&resetstats=1' ),
					'extra' => array(
						'style' => 'display: none;'
					),
					'tip' => __( 'Click the link to run the function that reset the stats count for all ads and posts.', APP_TD ),
				),
				array(
					'title' => __( 'Disable Core Stylesheets', APP_TD ),
					'name' => 'disable_stylesheet',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'If you are interested in creating a child theme or just want to completely disable the core ClassiPress styles, enable this option. (Note: this option is for advanced users. Do not change unless you know what you are doing.)', APP_TD ),
				),
				array(
					'title' => __( 'Enable Debug Mode', APP_TD ),
					'name' => 'debug_mode',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will print out the <code>$wp_query->query_vars</code> array at the top of your website. This should only be used for debugging.', APP_TD ),
				),
				array(
					'title' => __( 'Use Google CDN jQuery', APP_TD ),
					'name' => 'google_jquery',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( "This will use Google's hosted jQuery files which are served from their global content delivery network. This will help your site load faster and save bandwidth.", APP_TD ),
				),
				array(
					'title' => __( 'Use SelectBox JS library', APP_TD ),
					'name' => 'selectbox',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'SelectBox is a jQuery plugin that allows you to replace select boxes with gorgeous and feature rich drop downs.', APP_TD ),
				),
				array(
					'title' => __( 'Disable WordPress Login Page', APP_TD ),
					'name' => 'disable_wp_login',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'If someone tries to access <code>wp-login.php</code> directly, they will be redirected to ClassiPress themed login pages. If you want to use any "maintenance mode" plugins, you should enable the default WordPress login page.', APP_TD ),
				),
				array(
					'title' => __( 'Disable WordPress Version Meta Tag', APP_TD ),
					'name' => 'remove_wp_generator',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' =>
						sprintf( __( 'This will remove the WordPress generator meta tag in the source code of your site %s.', APP_TD ), '<code>&lt;meta name="generator" content="WordPress 3.5"&gt;</code>' ) . '<br />' .
						__( 'It\'s an added security measure which prevents anyone from seeing what version of WordPress you are using.', APP_TD ) . '<br />' .
						__( 'It also helps to deter hackers from taking advantage of vulnerabilities sometimes present in WordPress.', APP_TD ),
				),
				array(
					'title' => __( 'Disable WordPress User Toolbar', APP_TD ),
					'name' => 'remove_admin_bar',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will remove the WordPress user toolbar at the top of your web site which is displayed for all logged in users.', APP_TD ),
				),
				array(
					'title' => __( 'Disable WordPress Embeds', APP_TD ),
					'name' => 'disable_embeds',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'The Embed option allows you embed a video, image, or other media content into your content automatically by typing the URL.', APP_TD ),
				),
				array(
					'title' => __( 'Display Website Time', APP_TD ),
					'name' => 'display_website_time',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will display current time and timezone of your website in the footer section.', APP_TD ),
				),
				array(
					'title' => __( 'Cache Expires', APP_TD ),
					'desc' => __( 'This number is in seconds so one day equals 86400 seconds (60 seconds * 60 minutes * 24 hours).', APP_TD ),
					'type' => 'text',
					'name' => 'cache_expires',
					'extra' => array( 'size' => 10 ),
					'tip' => __( 'To speed up page loading on your site, ClassiPress uses a caching mechanism on certain features (i.e. category drop-down, home page). The cache automatically gets flushed whenever a category has been added/modified, however this value sets the frequency your cache is regularly emptied. We recommend keeping this at the default (every hour = 3600 seconds).', APP_TD ),
				),
				array(
					'title' => __( 'Admin Table Width', APP_TD ),
					'type' => 'text',
					'name' => 'table_width',
					'extra' => array( 'size' => 10 ),
					'tip' => __( 'Sometimes the admin option pages are set too narrow or too wide. This option allows you to override it. Note: value must be numeric followed by either px or % (i.e. 700px or 100%). The default is 850px.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Box Right Side', APP_TD ),
					'type' => 'select',
					'name' => 'ad_right_class',
					'values' => array(
						'full' => __( 'Normal Full Width', APP_TD ),
						'' => __( 'Legacy Ads Width', APP_TD ),
					),
					'tip' => __( 'Sometimes the main ad listings box is too narrow or it wraps due to legacy ad sizes. This option allows you to change it manually.', APP_TD ),
				),
			),
		);

		$this->tab_sections['advanced']['permalinks'] = array(
			'title' => __( 'Custom Post Type & Taxonomy URLs', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Ad Listing Base URL', APP_TD ),
					'desc' => '<br />' . sprintf( __( 'IMPORTANT: You must <a target="_blank" href="%s">re-save your permalinks</a> for this change to take effect.', APP_TD ), 'options-permalink.php' ),
					'type' => 'text',
					'name' => 'post_type_permalink',
					'tip' => __( 'This controls the base name of your ad listing urls. The default is ads and will look like this: http://www.yoursite.com/ads/ad-title-here/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Category Base URL', APP_TD ),
					'desc' => '<br />' . sprintf( __( 'IMPORTANT: You must <a target="_blank" href="%s">re-save your permalinks</a> for this change to take effect.', APP_TD ), 'options-permalink.php' ),
					'type' => 'text',
					'name' => 'ad_cat_tax_permalink',
					'tip' => __( 'This controls the base name of your ad category urls. The default is ad-category and will look like this: http://www.yoursite.com/ad-category/category-name/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Tag Base URL', APP_TD ),
					'desc' => '<br />' . sprintf( __( 'IMPORTANT: You must <a target="_blank" href="%s">re-save your permalinks</a> for this change to take effect.', APP_TD ), 'options-permalink.php' ),
					'type' => 'text',
					'name' => 'ad_tag_tax_permalink',
					'tip' => __( 'This controls the base name of your ad tag urls. The default is ad-tag and will look like this: http://www.yoursite.com/ad-tag/tag-name/. Do not include any slashes. This should only be alpha and/or numeric values. You should not change this value once you have launched your site otherwise you risk breaking urls of other sites pointing to yours, etc.', APP_TD ),
				),
			),
		);

		$this->tab_sections['advanced']['cufon'] = array(
			'title' => __( 'Cuf&oacute;n Font Replacement', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable Cuf&oacute;n', APP_TD ),
					'name' => 'cufon_enable',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ) . '<br />' . sprintf( __( 'Turns on the Cuf&oacute;n replacement text feature on your site. Learn more about <a target="_new" href="%s">Cuf&oacute;n</a> here.', APP_TD ), 'http://github.com/sorccu/cufon/wiki' ),
					'tip' => __( 'Disable this option if you are having conflicts or problems with certain text displaying on your site.', APP_TD ),
				),
				array(
					'title' => __( 'Cuf&oacute;n Replacement Code', APP_TD ),
					'desc' =>
						'<br />' . sprintf( __( 'Say you want to replace all %1$s elements on your site with Cuf&oacute;n. You would enter, %2$s.', APP_TD ), "<code>&lt;h1 class='dotted'&gt;</code>", "<code>Cufon.replace('h1.dotted', { fontFamily: 'Liberation Serif', textShadow:'0 1px 0 #FFFFFF' });</code>" ) . '<br />' .
						__( 'The Cuf&oacute;n system will automatically style all elements for you.', APP_TD ) . '<br />' .
						__( 'Note: You must have the font installed before it will work. To install a new font you must first generate it and then place the .js font file in your ClassiPress /includes/fonts/ theme directory.', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'cufon_code',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'Cuf&oacute;n allows you to easily replace text with stylish fonts that appear to be images. It is fast and and does not require Flash.', APP_TD ),
				),
			),
		);


	}


	private function categories_options( $prefix ) {
		$options = array(
			array(
				'title' => __( 'Show Category Count', APP_TD ),
				'type' => 'checkbox',
				'name' => $prefix . '_count',
				'desc' => __( 'Yes', APP_TD ),
				'tip' => __( 'Display the quantity of posts in that category next to the category name?', APP_TD ),
			),
			array(
				'title' => __( 'Hide Empty Sub-Categories', APP_TD ),
				'type' => 'checkbox',
				'name' => $prefix . '_hide_empty',
				'desc' => __( 'Yes', APP_TD ),
				'tip' => __( 'If a category had no listings, should it be hidden?', APP_TD ),
			),
			array(
				'title' => __( 'Category Depth', APP_TD ),
				'type' => 'select',
				'name' => $prefix . '_depth',
				'values' => array(
					'999' => __( 'Show All', APP_TD ),
					'0' => '0',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
				),
				'tip' => __( 'How many levels deep should the category tree traverse?', APP_TD ),
			),
			array(
				'title' => __( 'Number of Sub-Categories', APP_TD ),
				'type' => 'select',
				'name' => $prefix . '_sub_num',
				'values' => array(
					'999' => __( 'Show All', APP_TD ),
					'0' => '0',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
				),
				'tip' => __( 'How many sub-categories of each parent category should be shown?', APP_TD ),
			),
		);

		if ( $prefix == 'cat_dir' ) {
			$options[] = array(
				'title' => __( 'Number of Columns', APP_TD ),
				'type' => 'select',
				'name' => $prefix . '_cols',
				'values' => array(
					'2' => '2',
					'3' => '3',
				),
				'tip' => __( 'How many columns on the directory-style layout should be shown?', APP_TD ),
			);
		}

		return $options;
	}


	private function wrap_upload( $field_name, $desc ) {
		$upload_button = html( 'input', array( 'class' => 'upload_button button', 'rel' => $field_name, 'type' => 'button', 'value' => __( 'Upload Image', APP_TD ) ) );
		$clear_button = html( 'input', array( 'class' => 'delete_button button', 'rel' => $field_name, 'type' => 'button', 'value' => __( 'Clear Image', APP_TD ) ) );
		$preview = html( 'div', array( 'id' => $field_name . '_image', 'class' => 'upload_image_preview' ), html( 'img', array( 'src' => scbForms::get_value( $field_name, $this->options->get() ) ) ) );

		return $upload_button . ' ' . $clear_button . $desc . $preview;
	}


	function page_footer() {
		parent::page_footer();
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	/* upload logo and images */
	jQuery('.upload_button').click(function() {
		formfield = jQuery(this).attr('rel');
		tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true');
		return false;
	});

	/* send the uploaded image url to the field */
	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src'); // get the image url
		imgoutput = '<img src="' + imgurl + '" />'; //get the html to output for the image preview
		jQuery('#' + formfield).val(imgurl);
		jQuery('#' + formfield + '_image').html(imgoutput);
		tb_remove();
	}
});
</script>
<?php
	}


}



class CP_Theme_Settings_Emails extends APP_Tabs_Page {

	function setup() {
		$this->textdomain = APP_TD;

		$this->args = array(
			'page_title' => __( 'ClassiPress Emails', APP_TD ),
			'menu_title' => __( 'Emails', APP_TD ),
			'page_slug' => 'app-emails',
			'parent' => 'app-dashboard',
			'screen_icon' => 'options-general',
			'admin_action_priority' => 10,
		);

	}


	protected function init_tabs() {
		$this->tabs->add( 'general', __( 'General', APP_TD ) );
		$this->tabs->add( 'new_user', __( 'New User Email', APP_TD ) );

		$this->tab_sections['general']['notifications'] = array(
			'title' => __( 'Email Notifications', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Admin Notifications', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( 'Emails will be sent to: %1$s (<a href="%2$s">change</a>)', APP_TD ), get_option('admin_email'), 'options-general.php' ),
					'extra' => array(
						'style' => 'display: none;'
					),
				),
				array(
					'title' => __( 'New Ad Email', APP_TD ),
					'name' => 'new_ad_email',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Send me an email once a new ad has been submitted.', APP_TD ),
				),
				array(
					'title' => __( 'Prune Ads Email', APP_TD ),
					'name' => 'prune_ads_email',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Send me an email every time the system prunes expired ads.', APP_TD ),
				),
				array(
					'title' => __( 'Ad Approved Email', APP_TD ),
					'name' => 'new_ad_email_owner',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Send the ad owner an email once their ad has been approved either by you manually or after payment has been made (post status changes from pending to published).', APP_TD ),
				),
				array(
					'title' => __( 'Ad Expired Email', APP_TD ),
					'name' => 'expired_ad_email_owner',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Send the ad owner an email once their ad has expired (post status changes from published to draft).', APP_TD ),
				),
				array(
					'title' => __( 'Admin New User Email', APP_TD ),
					'name' => 'nu_admin_email',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Sends the default WordPress new user notification email to the site admin. It is recommended to keep this turned on so you are alerted whenever a new user registers on your site.', APP_TD ),
				),
				array(
					'title' => __( 'Membership Activated Email', APP_TD ),
					'name' => 'membership_activated_email_owner',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Send the user an email once their membership has been activated.', APP_TD ),
				),
				array(
					'title' => __( 'Send Membership Subscription Reminder Emails', APP_TD ),
					'name' => 'membership_ending_reminder_email',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Enabling this option will automatically send emails prior to a customers membership pack subscription end date.', APP_TD ),
				),
			),
		);


		$this->tab_sections['new_user']['settings'] = array(
			'title' => __( 'New User Registration Email', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable Custom Email', APP_TD ),
					'name' => 'nu_custom_email',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'Sends a custom new user notification email to your customers by using the fields you complete below. If this is disabled, the default WordPress new user notification email will be sent. This is useful for debugging if your custom emails are not being sent.', APP_TD ),
				),
				array(
					'title' => __( 'From Name', APP_TD ),
					'type' => 'text',
					'name' => 'nu_from_name',
					'tip' => __( 'This is what your customers will see as the &quot;from&quot; when they receive the new user registration email. Use plain text only.', APP_TD ),
				),
				array(
					'title' => __( 'From Email', APP_TD ),
					'type' => 'text',
					'name' => 'nu_from_email',
					'tip' => __( 'This is what your customers will see as the &quot;from&quot; email address (also the reply to) when they receive the new user registration email. Use only a valid and existing email address with no html or variables.', APP_TD ),
				),
				array(
					'title' => __( 'Email Subject', APP_TD ),
					'type' => 'text',
					'name' => 'nu_email_subject',
					'tip' => __( 'This is the subject line your customers will see when they receive the new user registration email. Use text and variables only.', APP_TD ),
				),
				array(
					'title' => __( 'Allow HTML in Body', APP_TD ),
					'name' => 'nu_email_type',
					'type' => 'radio',
					'values' => array(
						'text/HTML' => __( 'Yes', APP_TD ),
						'text/plain' => __( 'No', APP_TD ),
					),
					'tip' => __( 'This option allows you to use html markup in the email body below. It is recommended to keep it disabled to avoid problems with delivery. If you turn it on, make sure to test it and make sure the formatting looks ok and gets delivered properly.', APP_TD ),
				),
				array(
					'title' => __( 'Email Body', APP_TD ),
					'desc' => '<br />' . __( 'You may use the following variables within the email body and/or subject line.', APP_TD )
						. '<br />' . sprintf( __( '%s - prints out the username', APP_TD ), '<code>%username%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the users email address', APP_TD ), '<code>%useremail%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the users text password', APP_TD ), '<code>%password%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your website url', APP_TD ), '<code>%siteurl%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your site name', APP_TD ), '<code>%blogname%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your sites login url', APP_TD ), '<code>%loginurl%</code>' )
						. '<br /><br />' . __( 'Each variable MUST have the percentage signs wrapped around it with no spaces.', APP_TD )
						. '<br />' . __( 'Always test your new email after making any changes (register) to make sure it is working and formatted correctly. If you do not receive an email, chances are something is wrong with your email body.', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'nu_email_body',
					'extra' => array(
						'style' => 'width: 500px; height: 200px;'
					),
					'tip' => __( 'Enter the text you would like your customers to see in the new user registration email. Make sure to always at least include the %username% and %password% variables otherwise they might forget later.', APP_TD ),
				),
			),
		);

	}

}



class CP_Theme_Settings_Pricing extends APP_Tabs_Page {

	function setup() {
		$this->textdomain = APP_TD;

		$this->args = array(
			'page_title' => __( 'ClassiPress Pricing', APP_TD ),
			'menu_title' => __( 'Pricing', APP_TD ),
			'page_slug' => 'app-pricing',
			'parent' => 'app-dashboard',
			'screen_icon' => 'options-general',
			'admin_action_priority' => 10,
		);

	}


	protected function init_tabs() {
		global $cp_options;

		$this->tabs->add( 'general', __( 'General', APP_TD ) );
		$this->tabs->add( 'membership', __( 'Membership', APP_TD ) );
		if ( $cp_options->price_scheme == 'category' || ( isset( $_POST['price_scheme'] ) && $_POST['price_scheme'] == 'category' ) )
			$this->tabs->add( 'category_price', __( 'Price Per Category', APP_TD ) );
		if ( $cp_options->required_membership_type == 'category' || ( isset( $_POST['required_membership_type'] ) && $_POST['required_membership_type'] == 'category' ) )
			$this->tabs->add( 'membership_category', __( 'Membership by Category', APP_TD ) );

		$this->tab_sections['general']['configuration'] = array(
			'title' => __( 'Pricing Configuration', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Charge for Listing Ads', APP_TD ),
					'name' => 'charge_ads',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This option activates the payment system so you can start charging for ad listings on your site.', APP_TD ),
				),
				array(
					'title' => __( 'Show Featured Ads Slider', APP_TD ),
					'name' => 'enable_featured',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This option turns on the home page featured ads slider. Usually you charge extra for this space but it is not required. To manually make an ad appear here, check the &quot;stick this post to the front page&quot; box on the WordPress edit post page under &quot;Visibility&quot;.', APP_TD ),
				),
				array(
					'title' => __( 'Featured Ads Title Length', APP_TD ),
					'name' => 'featured_trim',
					'type' => 'text',
					'tip' => __( 'This number controls the length of your featured ad titles to this many characters (i.e. if you changed this value to 5, &quot;My Title&quot; would turn into &quot;My Ti...&quot;. Spaces are included in the count.)', APP_TD ),
				),
				array(
					'title' => __( 'Featured Ad Price', APP_TD ),
					'desc' => __( 'Only enter numeric values or decimal points. Do not include a currency symbol or commas.', APP_TD ),
					'type' => 'text',
					'name' => 'sys_feat_price',
					'tip' => __( 'This is the additional amount you will charge visitors to post a featured ad on your site. A featured ad appears in the slider on home page. Leave this blank if you do not want to offer featured ads.', APP_TD ),
				),
				array(
					'title' => __( 'Clean Ad Price Field', APP_TD ),
					'name' => 'clean_price_field',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ) . '<br />' . __( 'This option should be enabled in order to store valid price values.', APP_TD ),
					'tip' => __( 'This will remove any letters and special characters from the price field leaving only numbers and periods. Disable this if you prefer to allow visitors to enter text such as TBD, OBO or other contextual phrases.', APP_TD ),
				),
				array(
					'title' => __( 'Display Zero for Empty Ad Prices', APP_TD ),
					'name' => 'force_zeroprice',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will force any ad without a price to display a currency of zero for the price.', APP_TD ),
				),
				array(
					'title' => __( 'Hide Decimals for Prices', APP_TD ),
					'name' => 'hide_decimals',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ),
					'tip' => __( 'This will hide decimals for prices displayed on your site. Enable this option if your currency does not use decimals (i.e. Yen).', APP_TD ),
				),
				array(
					'title' => __( 'Ad Currency Symbol', APP_TD ),
					'name' => 'curr_symbol',
					'type' => 'text',
					'tip' => __( 'Enter the currency symbol you want to appear next to prices on your classified ads (i.e. $, &euro;, &pound;, &yen;)', APP_TD ),
				),
			),
		);

		$this->tab_sections['general']['model'] = array(
			'title' => __( 'Pricing Model', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Price Model', APP_TD ),
					'name' => 'price_scheme',
					'type' => 'select',
					'desc' => '<br />' . sprintf( __( 'If you select the &quot;Fixed Price Per Ad&quot; option, you must have at least one active <a href="%s">ad pack</a> setup.', APP_TD ), 'admin.php?page=packages' ),
					'values' => array(
						'single' => __( 'Fixed Price Per Ad', APP_TD ),
						'category' => __( 'Price Per Category', APP_TD ),
						'percentage' => __( '% of Sellers Ad Price', APP_TD ),
						'featured' => __( 'Only Charge for Featured Ads', APP_TD ),
					),
					'tip' => __( 'This option defines the pricing model for selling ads on your site. If you want to provide free and paid ads then select the &quot;Price Per Category&quot; option.', APP_TD ),
				),
				array(
					'title' => __( '% of Sellers Ad Price', APP_TD ),
					'name' => 'percent_per_ad',
					'type' => 'text',
					'extra' => array( 'size' => 2 ),
					'tip' => __( 'If you selected the &quot;% of Sellers Ad Price&quot; price model, enter your percentage here. Numbers only. No percentage symbol or commas.', APP_TD ),
				),
			),
		);


		$this->tab_sections['membership']['configuration'] = array(
			'title' => __( 'Membership Options', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable Membership Packs', APP_TD ),
					'name' => 'enable_membership_packs',
					'type' => 'checkbox',
					'desc' => __( 'Yes', APP_TD ) . '<br />' . sprintf( __( 'Manage your membership packages from the <a href="%s">Ad Packs</a> option page.', APP_TD ), 'admin.php?page=packages' ),
					'tip' => __( 'This option activates Membership Packs and their respective discounts. Disabling this does not disable the membership system, but simply stops the discounts from activating during the posting process.', APP_TD ),
				),
				array(
					'title' => __( 'Days of Reminder Messages', APP_TD ),
					'name' => 'membership_ending_reminder_days',
					'type' => 'text',
					'desc' => __( 'Affects both emails and website notifications.', APP_TD ),
					'extra' => array( 'size' => 5 ),
					'tip' => __( 'Number of days you would like to send renewal reminders before their subscription expires. Numeric values only.', APP_TD ),
				),
				array(
					'title' => __( 'Are Membership Packs Required to Purchase Ads?', APP_TD ),
					'name' => 'required_membership_type',
					'type' => 'select',
					'values' => array(
						'' => __( 'Not Required', APP_TD ),
						'all' => __( 'Required for All', APP_TD ),
						'category' => __( 'Required by Category', APP_TD ),
					),
					'tip' =>
						__( 'Choose how you would like the membership system to work.', APP_TD ) . '<br />' .
						__( '<strong>Not Required</strong> - a membership is not required in order to list an ad.', APP_TD ) . '<br />' .
						__( '<strong>Required for All</strong> - a user can only list an ad if they have an active membership.', APP_TD ) . '<br />' .
						__( '<strong>Required by Category</strong> - limits users with memberships to list ads in certain categories.', APP_TD ),
				),
			),
		);


		$this->tab_sections['category_price']['price'] = array(
			'title' => __( 'Price Per Category', APP_TD ),
			'fields' => $this->price_per_category(),
		);

		$this->tab_sections['membership_category']['required'] = array(
			'title' => __( 'Membership by Category', APP_TD ),
			'fields' => $this->membership_by_category(),
		);

	}


	private function price_per_category() {
		$options = array();
		$cats = array();
		$subcats = array();
		$categories = (array) get_terms( APP_TAX_CAT, array( 'orderby' => 'name', 'order' => 'asc', 'hide_empty' => false ) );

		// separate categories from subcategories
		foreach ( $categories as $key => $category ) {
			if ( $category->parent == 0 )
				$cats[ $key ] = $categories[ $key ];
			else
				$subcats[ $key ] = $categories[ $key ];

			unset( $categories[ $key ] );
		}

		// loop through all the cats
		foreach ( $cats as $cat ) {
			$options = $this->price_per_category_option( $options, $cat, 0 );
			$options = $this->price_per_category_walk( $options, $subcats, $cat->term_id, 0 );
		}

		return $options;
	}


	private function price_per_category_walk( $options, $subcats, $parent, $depth = 0 ) {
		$depth++;

		foreach ( $subcats as $subcat ) {
			if ( $subcat->parent != $parent )
				continue;

			$options = $this->price_per_category_option( $options, $subcat, $depth );
			$options = $this->price_per_category_walk( $options, $subcats, $subcat->term_id, $depth );
		}

		return $options;
	}


	private function price_per_category_option( $options, $category, $depth = 0 ) {
		global $cp_options;

		$pad = str_repeat( ' - ', $depth );

		$options[] = array(
			'title' => $pad . $category->name,
			'type' => 'text',
			'extra' => array( 'style' => 'width: 70px;' ),
			'name' => array( 'price_per_cat', $category->term_id ),
			'desc' => $cp_options->currency_code,
			'default' => 0,
		);

		return $options;
	}


	private function membership_by_category() {
		$options = array();
		$cats = array();
		$subcats = array();
		$categories = (array) get_terms( APP_TAX_CAT, array( 'orderby' => 'name', 'order' => 'asc', 'hide_empty' => false ) );

		// separate categories from subcategories
		foreach ( $categories as $key => $category ) {
			if ( $category->parent == 0 )
				$cats[ $key ] = $categories[ $key ];
			else
				$subcats[ $key ] = $categories[ $key ];

			unset( $categories[ $key ] );
		}

		// loop through all the cats
		foreach ( $cats as $cat ) {
			$options = $this->membership_by_category_option( $options, $cat, 0 );
			$options = $this->membership_by_category_walk( $options, $subcats, $cat->term_id, 0 );
		}

		return $options;
	}


	private function membership_by_category_walk( $options, $subcats, $parent, $depth = 0 ) {
		$depth++;

		foreach ( $subcats as $subcat ) {
			if ( $subcat->parent != $parent )
				continue;

			$options = $this->membership_by_category_option( $options, $subcat, $depth );
			$options = $this->membership_by_category_walk( $options, $subcats, $subcat->term_id, $depth );
		}

		return $options;
	}


	private function membership_by_category_option( $options, $category, $depth = 0 ) {
		$pad = str_repeat( ' - ', $depth );

		$options[] = array(
			'title' => $pad . $category->name,
			'type' => 'checkbox',
			'name' => array( 'required_categories', $category->term_id ),
			'desc' => __( 'Yes', APP_TD ),
		);

		return $options;
	}


}


