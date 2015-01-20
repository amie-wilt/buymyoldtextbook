<?php

class CP_Theme_System_Info extends APP_System_Info {


	function __construct( $args = array(), $options = null ) {

		parent::__construct( $args, $options );

		add_action( 'admin_notices', array( $this, 'admin_tools' ) );
	}


	public function admin_tools() {

		if ( ! empty( $_POST['cp_tools']['flush_cache'] ) ) {
			$message = cp_flush_all_cache();
			echo scb_admin_notice( $message );
		}

		if ( ! empty( $_POST['cp_tools']['delete_tables'] ) ) {
			cp_delete_db_tables();
		}

		if ( ! empty( $_POST['cp_tools']['delete_options'] ) ) {
			cp_delete_all_options();
		}

		if ( ! empty( $_POST['cp_tools']['rerun_migration'] ) ) {
			update_option( 'cp_tools_run_convertToCustomPostType', 'no' );
			$message = cp_convert_posts2Ads();
			echo scb_admin_notice( $message );
		}

	}


	function form_handler() {
		if ( empty( $_POST['action'] ) || ! $this->tabs->contains( $_POST['action'] ) )
			return;

		check_admin_referer( $this->nonce );

		if ( ! empty( $_POST['cp_tools'] ) )
			return;
		else
			parent::form_handler();
	}


	protected function init_tabs() {
		parent::init_tabs();

		$this->tabs->add( 'cp_tools', __( 'Advanced', APP_TD ) );

		$this->tab_sections['cp_tools']['cache'] = array(
			'title' => __( 'Theme Cache', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Flush Theme Cache', APP_TD ),
					'type' => 'submit',
					'name' => array( 'cp_tools', 'flush_cache' ),
					'extra' => array(
						'class' => 'button-secondary'
					),
					'value' => __( 'Flush Entire ClassiPress Cache', APP_TD ),
					'desc' =>
						'<br />' . __( "Sometimes you may have changed something and it hasn't been updated on your site.", APP_TD ) .
						'<br />' . __( 'Flushing the cache will empty anything that ClassiPress has stored in the cache (i.e. category drop-down menu, home page directory structure, etc).', APP_TD ),
				),
			),
		);


		$this->tab_sections['cp_tools']['uninstall'] = array(
			'title' => __( 'Uninstall Theme', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Delete Database Tables', APP_TD ),
					'type' => 'submit',
					'name' => array( 'cp_tools', 'delete_tables' ),
					'extra' => array(
						'class' => 'button-secondary',
						'onclick' => 'return cp_confirmBeforeDeleteTables();',
					),
					'value' => __( 'Delete ClassiPress Database Tables', APP_TD ),
					'desc' =>
						'<br />' . __( 'Do you wish to completely delete all ClassiPress database tables?', APP_TD ) .
						'<br />' . __( 'Once you do this you will lose any custom fields, forms, ad packs, etc that you have created.', APP_TD ),
				),
				array(
					'title' => __( 'Delete Config Options', APP_TD ),
					'type' => 'submit',
					'name' => array( 'cp_tools', 'delete_options' ),
					'extra' => array(
						'class' => 'button-secondary',
						'onclick' => 'return cp_confirmBeforeDeleteOptions();',
					),
					'value' => __( 'Delete ClassiPress Config Options', APP_TD ),
					'desc' =>
						'<br />' . __( 'Do you wish to completely delete all ClassiPress configuration options?', APP_TD ) .
						'<br />' . __( 'This will delete all values saved on the settings, pricing, gateways, etc admin pages from the wp_options database table.', APP_TD ),
				),
			),
		);


		$this->tab_sections['cp_tools']['theme'] = array(
			'title' => __( 'Theme', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Rerun Migration Script', APP_TD ),
					'type' => 'submit',
					'name' => array( 'cp_tools', 'rerun_migration' ),
					'extra' => array(
						'class' => 'button-secondary'
					),
					'value' => __( 'Rerun ClassiPress Migration Script', APP_TD ),
					'desc' =>
						'<br />' . __( "If you're still using ClassiPress version 3.0.4 (or earlier) and were not prompted to upgrade to 3.0.5 or the script timed out, click this button.", APP_TD ) .
						'<br />' . __( "It will attempt to rerun the migration script again. Running this script won't do any harm if you aren't sure about it.", APP_TD ),
				),
			),
		);


	}


	function page_footer() {
		parent::page_footer();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	if ( $("form input[name^='cp_tools']").length ) {
		$('form p.submit').html('');
	}
});
</script>
<?php
	}


}

