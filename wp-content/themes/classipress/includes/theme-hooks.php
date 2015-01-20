<?php
/**
 * Reserved for any theme-specific hooks
 * For general AppThemes hooks, see framework/kernel/hooks.php
 *
 * @since 3.1
 * @uses add_action() calls to trigger the hooks.
 *
 */


/**
 * called in sidebar-user.php & author.php to hook into user informations
 *
 * @since 3.2
 *
 */
function cp_author_info( $location ) {
	do_action( 'cp_author_info', $location );
}


/**
 * called in cp_formbuilder() to hook into form builder
 *
 * @since 3.2.1
 * @param object $form_fields
 * @param object|bool $post
 *
 */
function cp_action_formbuilder( $form_fields, $post ) {
	do_action( 'cp_action_formbuilder', $form_fields, $post );
}


/**
 * called in cp_add_new_listing() to hook into inserting new ad process
 *
 * @since 3.2.1
 * @param int $post_id
 *
 */
function cp_action_add_new_listing( $post_id ) {
	do_action( 'cp_action_add_new_listing', $post_id );
}


/**
 * called in cp_update_listing() to hook into updating ad process
 *
 * @since 3.2.1
 * @param int $post_id
 *
 */
function cp_action_update_listing( $post_id ) {
	do_action( 'cp_action_update_listing', $post_id );
}


/**
 * called in cp_get_ad_details() to hook before ad details
 *
 * @since 3.3
 * @param object $form_fields
 * @param object $post
 * @param string $location
 *
 */
function cp_action_before_ad_details( $form_fields, $post, $location ) {
	do_action( 'cp_action_before_ad_details', $form_fields, $post, $location );
}


/**
 * called in cp_get_ad_details() to hook after ad details
 *
 * @since 3.3
 * @param object $form_fields
 * @param object $post
 * @param string $location
 *
 */
function cp_action_after_ad_details( $form_fields, $post, $location ) {
	do_action( 'cp_action_after_ad_details', $form_fields, $post, $location );
}



?>