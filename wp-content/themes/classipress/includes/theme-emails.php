<?php
/**
 *
 * Emails that get called and sent out for ClassiPress
 * @package ClassiPress
 * @author AppThemes
 * For wp_mail to work, you need the following:
 * settings SMTP and smtp_port need to be set in your php.ini
 * also, either set the sendmail_from setting in php.ini, or pass it as an additional header.
 *
 */


// send new ad notification email to admin
function cp_new_ad_email( $post_id ) {

		// get the post values
		$the_ad = get_post($post_id);
		$category = appthemes_get_custom_taxonomy($post_id, APP_TAX_CAT, 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author = stripslashes(cp_get_user_name($the_ad->post_author));
    $ad_slug = get_permalink( $post_id );
    //$ad_content = appthemes_filter(stripslashes($the_ad->post_content));
    $adminurl = get_edit_post_link( $post_id, '' );

    $mailto = get_option('admin_email');
    $subject = __( 'New Ad Submission', APP_TD );

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message  = __( 'Dear Admin,', APP_TD ) . "\r\n\r\n";
    $message .= sprintf( __( 'The following ad listing has just been submitted on your %s website.', APP_TD ), $blogname ) . "\r\n\r\n";
    $message .= __( 'Ad Details', APP_TD ) . "\r\n";
    $message .= __( '-----------------', APP_TD ) . "\r\n";
    $message .= __( 'Title: ', APP_TD ) . $ad_title . "\r\n";
    $message .= __( 'Category: ', APP_TD ) . $ad_cat . "\r\n";
    $message .= __( 'Author: ', APP_TD ) . $ad_author . "\r\n\r\n";
    $message .= __( '-----------------', APP_TD ) . "\r\n\r\n";
    $message .= __( 'Preview Ad: ', APP_TD ) . $ad_slug . "\r\n";
    $message .= sprintf( __( 'Edit Ad: %s', APP_TD ), $adminurl ) . "\r\n\r\n\r\n";
    $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
    $message .= __( 'ClassiPress', APP_TD ) . "\r\n\r\n";

	$email = array( 'to' => $mailto, 'subject' => $subject, 'message' => $message );
	$email = apply_filters( 'cp_email_admin_new_ad', $email, $post_id );

	wp_mail( $email['to'], $email['subject'], $email['message'] );
}


// send new ad notification email to ad owner
function cp_owner_new_ad_email( $post_id ) {

    // get the post values
    $the_ad = get_post($post_id);
    $category = appthemes_get_custom_taxonomy($post_id, APP_TAX_CAT, 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author = stripslashes(cp_get_user_name($the_ad->post_author));
    $ad_author_email = stripslashes(get_the_author_meta('user_email', $the_ad->post_author));
    $ad_status = cp_get_status_i18n($the_ad->post_status);
    //$ad_content = appthemes_filter(stripslashes($the_ad->post_content));
    $siteurl = home_url('/');

    $dashurl = trailingslashit(CP_DASHBOARD_URL);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = $ad_author_email;
    $subject = sprintf( __( 'Your Ad Submission on %s', APP_TD ), $blogname );

    $message  = sprintf( __( 'Hi %s,', APP_TD ), $ad_author ) . "\r\n\r\n";
    $message .= sprintf( __( 'Thank you for your recent submission! Your ad listing has been submitted for review and will not appear live on our site until it has been approved. Below you will find a summary of your ad listing on the %s website.', APP_TD ), $blogname ) . "\r\n\r\n";
    $message .= __( 'Ad Details', APP_TD ) . "\r\n";
    $message .= __( '-----------------', APP_TD ) . "\r\n";
    $message .= __( 'Title: ', APP_TD ) . $ad_title . "\r\n";
    $message .= __( 'Category: ', APP_TD ) . $ad_cat . "\r\n";
    $message .= __( 'Status: ', APP_TD ) . $ad_status . "\r\n";
    //$message .= __( 'Description: ', APP_TD ) . $ad_content . "\r\n";
    $message .= __( '-----------------', APP_TD ) . "\r\n\r\n";
    $message .= __( 'You may check the status of your ad(s) at anytime by logging into your dashboard.', APP_TD ) . "\r\n";
    $message .= $dashurl . "\r\n\r\n\r\n\r\n";
    $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
    $message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname ) . "\r\n";
    $message .= $siteurl . "\r\n\r\n\r\n\r\n";

	$email = array( 'to' => $mailto, 'subject' => $subject, 'message' => $message );
	$email = apply_filters( 'cp_email_user_new_ad', $email, $post_id );

	wp_mail( $email['to'], $email['subject'], $email['message'] );
}


// when an ad is approved or expires, send the ad owner an email
function cp_notify_ad_owner_email( $new_status, $old_status, $post ) {
	global $current_user, $wpdb, $cp_options;

	if ( $post->post_type != APP_POST_TYPE )
		return;

    $the_ad = get_post($post->ID);
    $category = appthemes_get_custom_taxonomy($post->ID, APP_TAX_CAT, 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author_id = stripslashes(get_the_author_meta('ID', $the_ad->post_author));
    $ad_author = stripslashes(cp_get_user_name($the_ad->post_author));
    $ad_author_email = stripslashes(get_the_author_meta('user_email', $the_ad->post_author));
    $ad_status = cp_get_status_i18n($the_ad->post_status);
    $ad_content = appthemes_filter(stripslashes($the_ad->post_content));
    $siteurl = home_url('/');
    $dashurl = trailingslashit(CP_DASHBOARD_URL);

		$mailto = $ad_author_email;

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // make sure the admin wants to send emails
    $send_approved_email = $cp_options->new_ad_email_owner;
    $send_expired_email = $cp_options->expired_ad_email_owner;

	// if the ad has been approved send email to ad owner only if owner is not equal to approver
	// admin approving own ads or ad owner pausing and reactivating ad on his dashboard don't need to send email
	if ( $old_status == 'pending' && $new_status == 'publish' && $current_user->ID != $ad_author_id && $send_approved_email ) {

        $subject = __( 'Your ad has been approved', APP_TD );

        $message  = sprintf( __( 'Hi %s,', APP_TD ), $ad_author ) . "\r\n\r\n";
        $message .= sprintf( __( 'Your ad listing, "%s" has been approved and is now live on our site.', APP_TD ), $ad_title ) . "\r\n\r\n";

        $message .= __( 'You can view your ad by clicking on the following link:', APP_TD ) . "\r\n";
        $message .= get_permalink($post->ID) . "\r\n\r\n\r\n\r\n";
        $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
        $message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname) . "\r\n";
        $message .= $siteurl . "\r\n\r\n\r\n\r\n";

		$email = array( 'to' => $mailto, 'subject' => $subject, 'message' => $message );
		$email = apply_filters( 'cp_email_user_ad_approved', $email, $post );

		wp_mail( $email['to'], $email['subject'], $email['message'] );

	// if the ad has expired, send an email to the ad owner only if owner is not equal to approver
	} elseif ( $old_status == 'publish' && $new_status == 'draft' && $current_user->ID != $ad_author_id && $send_expired_email ) {

        $subject = __( 'Your ad has expired', APP_TD );

        $message  = sprintf( __('Hi %s,', APP_TD ), $ad_author ) . "\r\n\r\n";
        $message .= sprintf( __('Your ad listing, "%s" has expired.', APP_TD ), $ad_title ) . "\r\n\r\n";

        if ( $cp_options->allow_relist ) {
            $message .= __( 'If you would like to relist your ad, please visit your dashboard and click the "relist" link.', APP_TD ) . "\r\n";
            $message .= $dashurl . "\r\n\r\n\r\n\r\n";
        }

        $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
        $message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname ) . "\r\n";
        $message .= $siteurl . "\r\n\r\n\r\n\r\n";

		$email = array( 'to' => $mailto, 'subject' => $subject, 'message' => $message );
		$email = apply_filters( 'cp_email_user_ad_expired', $email, $post );

		wp_mail( $email['to'], $email['subject'], $email['message'] );

	}
}
add_filter( 'transition_post_status', 'cp_notify_ad_owner_email', 10, 3 );


// ad poster sidebar contact form email
function cp_contact_ad_owner_email( $post_id ) {
	$errors = new WP_Error();

	// check for required post data
	$expected = array( 'from_name', 'from_email', 'subject', 'message' );
	foreach ( $expected as $field_name ) {
		if ( empty( $_POST[ $field_name ] ) ) {
			$errors->add( 'empty_field', __( 'ERROR: All fields are required.', APP_TD ) );
			return $errors;
		}
	}

	// check for required anti-spam post data
	$expected_numbers = array( 'rand_total', 'rand_num', 'rand_num2' );
	foreach ( $expected_numbers as $field_name ) {
		if ( ! isset( $_POST[ $field_name ] ) || ! is_numeric( $_POST[ $field_name ] ) ) {
			$errors->add( 'invalid_captcha', __( 'ERROR: Incorrect captcha answer.', APP_TD ) );
			return $errors;
		}
	}

	// verify captcha answer
	$rand_post_total = (int) $_POST['rand_total'];
	$rand_total = (int) $_POST['rand_num'] + (int) $_POST['rand_num2'];
	if ( $rand_total != $rand_post_total )
		$errors->add( 'invalid_captcha', __( 'ERROR: Incorrect captcha answer.', APP_TD ) );

	// verify email
	if ( ! is_email( $_POST['from_email'] ) )
		$errors->add( 'invalid_email', __( 'ERROR: Incorrect email address.', APP_TD ) );

	// verify post
	$post = get_post( $post_id );
	if ( ! $post )
		$errors->add( 'invalid_post', __( 'ERROR: Ad does not exist.', APP_TD ) );

	if ( $errors->get_error_code() )
		return $errors;

	$mailto = get_the_author_meta( 'user_email', $post->post_author );

	$from_name = appthemes_filter( appthemes_clean( $_POST['from_name'] ) );
	$from_email = appthemes_clean( $_POST['from_email'] );
	$subject = appthemes_filter( appthemes_clean( $_POST['subject'] ) );
	$posted_message = appthemes_filter( appthemes_clean( $_POST['message'] ) );

	$sitename = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
	$siteurl = home_url('/');
	$permalink = get_permalink( $post_id );

	$message = sprintf( __( 'Someone is interested in your ad listing: %s', APP_TD ), $permalink ) . "\r\n\r\n";
	$message .= '"' . wordwrap( $posted_message, 70 ) . '"' . "\r\n\r\n";
	$message .= sprintf( __( 'Name: %s', APP_TD ), $from_name ) . "\r\n";
	$message .= sprintf( __( 'E-mail: %s', APP_TD ), $from_email ) . "\r\n\r\n";
	$message .= '-----------------------------------------' . "\r\n";
	$message .= sprintf( __( 'This message was sent from %s', APP_TD ), $sitename ) . "\r\n";
	$message .=  $siteurl . "\r\n\r\n";
	$message .= __( 'Sent from IP Address: ', APP_TD ) . appthemes_get_ip() . "\r\n\r\n"; 

	$email = array( 'to' => $mailto, 'subject' => $subject, 'message' => $message, 'from' => $from_email, 'from_name' => $from_name );
	$email = apply_filters( 'cp_email_user_ad_contact', $email, $post_id );

	APP_Mail_From::apply_once( array( 'email' => $email['from'], 'name' => $email['from_name'], 'reply' => true ) );
	wp_mail( $email['to'], $email['subject'], $email['message'] );

	return $errors;
}


// email that gets sent out to new users once they register
function cp_new_user_notification( $user_id, $plaintext_pass = '' ) {
	global $cp_options;

	$user = new WP_User( $user_id );

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// variables that can be used by admin to dynamically fill in email content
	$find = array('/%username%/i', '/%password%/i', '/%blogname%/i', '/%siteurl%/i', '/%loginurl%/i', '/%useremail%/i');
	$replace = array($user_login, $plaintext_pass, get_option('blogname'), home_url('/'), wp_login_url(), $user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	// send the site admin an email everytime a new user registers
	if ( $cp_options->nu_admin_email ) {	
		$subject = sprintf( __( '[%s] New User Registration', APP_TD ), $blogname );

		$message  = sprintf( __( 'New user registration on your site %s:', APP_TD ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s', APP_TD ), $user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'E-mail: %s', APP_TD ), $user_email ) . "\r\n";

		$email = array( 'to' => get_option( 'admin_email' ), 'subject' => $subject, 'message' => $message );
		$email = apply_filters( 'cp_email_admin_new_user', $email, $user_id, $plaintext_pass );

		wp_mail( $email['to'], $email['subject'], $email['message'] );
	}

	if ( empty($plaintext_pass) )
		return;

	// check and see if the custom email option has been enabled
	// if so, send out the custom email instead of the default WP one
	if ( $cp_options->nu_custom_email ) {	

		// email sent to new user starts here				
		$from_name = strip_tags( $cp_options->nu_from_name );
		$from_email = strip_tags( $cp_options->nu_from_email );

		// search and replace any user added variable fields in the subject line
		$subject = stripslashes( $cp_options->nu_email_subject );
		$subject = preg_replace( $find, $replace, $subject );
		$subject = preg_replace( "/%.*%/", "", $subject );	

		// search and replace any user added variable fields in the body
		$message = stripslashes( $cp_options->nu_email_body );
		$message = preg_replace( $find, $replace, $message );
		$message = preg_replace( "/%.*%/", "", $message );

		$email = array( 'to' => $user_email, 'subject' => $subject, 'message' => $message, 'from' => $from_email, 'from_name' => $from_name );
		$email = apply_filters( 'cp_email_user_new_user_custom', $email, $user_id, $plaintext_pass );

		APP_Mail_From::apply_once( array( 'email' => $email['from'], 'name' => $email['from_name'] ) );
		if ( $cp_options->nu_email_type == 'text/plain' ) {
			wp_mail( $email['to'], $email['subject'], $email['message'] );
		} else {
			appthemes_send_email( $email['to'], $email['subject'], $email['message'] );
		}

	// send the default email to debug
	} else {

		$subject = sprintf( __( '[%s] Your username and password', APP_TD ), $blogname );

		$message  = sprintf( __( 'Username: %s', APP_TD ), $user_login ) . "\r\n";
		$message .= sprintf( __( 'Password: %s', APP_TD ), $plaintext_pass ) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		$email = array( 'to' => $user_email, 'subject' => $subject, 'message' => $message );
		$email = apply_filters( 'cp_email_user_new_user', $email, $user_id, $plaintext_pass );

		wp_mail( $email['to'], $email['subject'], $email['message'] );

	}

}

// send new post report email to admin
function app_report_post( $post_id ) {

    // get the post values
    $the_ad = get_post($post_id);
    $category = appthemes_get_custom_taxonomy($post_id, APP_TAX_CAT, 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author = stripslashes(cp_get_user_name($the_ad->post_author));
  	$ad_slug = get_permalink( $post_id );
    $adminurl = get_edit_post_link( $post_id, '' );

    $mailto = get_option('admin_email');
    $subject = __( 'Post Reported', APP_TD );

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message  = __( 'Dear Admin,', APP_TD ) . "\r\n\r\n";
    $message .= sprintf( __( 'The following ad listing has just been reported on your %s website.', APP_TD ), $blogname ) . "\r\n\r\n";
    $message .= __( 'Ad Details', APP_TD ) . "\r\n";
    $message .= __( '-----------------', APP_TD ) . "\r\n";
    $message .= __( 'Title: ', APP_TD ) . $ad_title . "\r\n";
    $message .= __( 'Category: ', APP_TD ) . $ad_cat . "\r\n";
    $message .= __( 'Author: ', APP_TD ) . $ad_author . "\r\n";
    $message .= __( '-----------------', APP_TD ) . "\r\n\r\n";
    $message .= __( 'Preview Ad: ', APP_TD ) . $ad_slug . "\r\n";
    $message .= sprintf( __( 'Edit Ad: %s', APP_TD ), $adminurl ) . "\r\n\r\n\r\n";

    $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
    $message .= __( 'ClassiPress', APP_TD ) . "\r\n\r\n";

    wp_mail( $mailto, $subject, $message );
}


// send notification email to buyer when membership was activated
function cp_owner_activated_membership_email( $user, $order ) {
	global $cp_options;

	if ( $cp_options->membership_activated_email_owner ) {	
    $membership_user_email = stripslashes($user->user_email);
    $membership_user_login = stripslashes(cp_get_user_name($user->ID));
    $membership_pack_name = stripslashes($order['pack_name']);

    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $siteurl = home_url('/');

    $mailto = $membership_user_email;

    $subject = __( 'Your membership has been activated', APP_TD );

    $message  = sprintf( __( 'Hi %s,', APP_TD ), $membership_user_login ) . "\r\n\r\n";
    $message .= sprintf( __( 'Your membership, "%s" has been activated on our site, and You are ready to post ad listings.', APP_TD ), $membership_pack_name ) . "\r\n\r\n";
    $message .= __( 'You can post your ad by clicking on the following link:', APP_TD ) . "\r\n";
    $message .= CP_ADD_NEW_URL . "\r\n\r\n\r\n\r\n";
    $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
    $message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname ) . "\r\n";
    $message .= $siteurl . "\r\n\r\n\r\n\r\n";

		$email = array( 'to' => $mailto, 'subject' => $subject, 'message' => $message );
		$email = apply_filters( 'cp_email_user_membership_activated', $email, $user, $order );

		wp_mail( $email['to'], $email['subject'], $email['message'] );
	}

}


/**
 * Sends email with receipt to customer after completed purchase.
 *
 * @param object $order
 */
function cp_send_buyer_receipt( $order ) {
	global $app_abbr;

	$recipient = get_user_by( 'id', $order->get_author() );

	$items_html = '';
	foreach ( $order->get_items() as $item ) {
		$ptype_obj = get_post_type_object( $item['post']->post_type );
		if ( ! $ptype_obj->public )
			continue;

		if ( $order->get_id() != $item['post']->ID )
			$items_html .= html( 'p', html_link( get_permalink( $item['post']->ID ), $item['post']->post_title ) );
		else
			$items_html .= html( 'p', APP_Item_Registry::get_title( $item['type'] ) );
	}

	$table = new APP_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();

	$content = '';
	$content .= html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );
	$content .= html( 'p', __( 'This email confirms that you have purchased the following items:', APP_TD ) );
	$content .= $items_html;
	$content .= html( 'p', __( 'Order Summary:', APP_TD ) );
	$content .= $table_output;

	$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject = sprintf( __( '[%1$s] Receipt for your order #%2$d', APP_TD ), $blogname, $order->get_id() );

	$email = array( 'to' => $recipient->user_email, 'subject' => $subject, 'message' => $content );
	$email = apply_filters( 'cp_email_user_receipt', $email, $order );

	appthemes_send_email( $email['to'], $email['subject'], $email['message'] );
}
add_action( 'appthemes_transaction_completed', 'cp_send_buyer_receipt' );


/**
 * Sends email with receipt to admin after completed purchase.
 *
 * @param object $order
 */
function cp_send_admin_receipt( $order ) {
	global $cp_options;

	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		return;

	$moderation = ( $cp_options->post_status != 'publish' );

	$items_html = '';
	foreach ( $order->get_items() as $item ) {
		$ptype_obj = get_post_type_object( $item['post']->post_type );
		if ( ! $ptype_obj->public )
			continue;

		if ( $order->get_id() != $item['post']->ID )
			$items_html .= html( 'p', html_link( get_permalink( $item['post']->ID ), $item['post']->post_title ) );
		else
			$items_html .= html( 'p', APP_Item_Registry::get_title( $item['type'] ) );
	}

	$table = new APP_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();

	$content = '';
	$content .= html( 'p', __( 'Dear Admin,', APP_TD ) );
	$content .= html( 'p', __( 'You have received payment for the following items:', APP_TD ) );
	$content .= $items_html;
	if ( $moderation && $order->get_items( CP_ITEM_LISTING ) )
		$content .= html( 'p', __( 'Please review submitted ad listing, and approve it.', APP_TD ) );
	$content .= html( 'p', __( 'Order Summary:', APP_TD ) );
	$content .= $table_output;

	$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject = sprintf( __( '[%1$s] Received payment for order #%2$d', APP_TD ), $blogname, $order->get_id() );

	$email = array( 'to' => get_option( 'admin_email' ), 'subject' => $subject, 'message' => $content );
	$email = apply_filters( 'cp_email_admin_receipt', $email, $order );

	appthemes_send_email( $email['to'], $email['subject'], $email['message'] );
}
add_action( 'appthemes_transaction_completed', 'cp_send_admin_receipt' );


/**
 * Sends email notification to admin if payment failed.
 *
 * @param object $order
 */
function cp_send_admin_failed_transaction( $order ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		return;

	$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject = sprintf( __( '[%1$s] Failed Order #%2$d', APP_TD ), $blogname, $order->get_id() );

	$content = '';
	$content .= html( 'p', sprintf( __( 'Payment for the order #%s has failed.', APP_TD ), $order->get_id() ) );
	$content .= html( 'p', sprintf( __( 'Please <a href="%s">review this order</a>, and if necessary disable assigned services.', APP_TD ), get_edit_post_link( $order->get_id() ) ) );

	$email = array( 'to' => get_option( 'admin_email' ), 'subject' => $subject, 'message' => $content );
	$email = apply_filters( 'cp_email_admin_transaction_failed', $email, $order );

	appthemes_send_email( $email['to'], $email['subject'], $email['message'] );
}
add_action( 'appthemes_transaction_failed', 'cp_send_admin_failed_transaction' );


