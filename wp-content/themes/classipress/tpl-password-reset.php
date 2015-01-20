<?php
// Template Name: Password Reset
?>

<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<!-- full block -->
			<div class="shadowblock_out">

				<div class="shadowblock">

					<h2 class="dotted"><span class="colour"><?php _e( 'Password Reset', APP_TD ); ?></span></h2>

					<?php do_action( 'appthemes_notices' ); ?>

					<p><?php _e( 'Enter your new password below.', APP_TD ); ?></p>

					<div class="left-box">

						<form action="<?php echo appthemes_get_password_reset_url(); ?>" method="post" class="loginform password-reset-form" name="resetpassform" id="resetpassform">

							<input type="hidden" id="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />

							<p>
								<label for="pass1"><?php _e( 'New password', APP_TD ); ?></label>
								<input type="password" name="pass1" id="pass1" class="text" size="20" value="" autocomplete="off" />
							</p>

							<p>
								<label for="pass2"><?php _e( 'Confirm new password', APP_TD ); ?></label>
								<input type="password" name="pass2" id="pass2" class="text" size="20" value="" autocomplete="off" />
							</p>

							<div class="strength-meter">
								<div id="pass-strength-result" class="hide-if-no-js"><?php _e( 'Strength indicator', APP_TD ); ?></div>
								<span class="description indicator-hint"><?php _e( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', APP_TD ); ?></span>
							</div>

							<div id="checksave">
								<?php do_action('lostpassword_form'); ?>
								<p class="submit"><input type="submit" class="btn_orange" name="resetpass" id="resetpass" value="<?php _e( 'Reset Password', APP_TD ); ?>" tabindex="100" /></p>
							</div>

							<!-- autofocus the field -->
							<script type="text/javascript">try{document.getElementById('pass1').focus();}catch(e){}</script>

						</form>

					</div>

					<div class="right-box">

					</div><!-- /right-box -->

					<div class="clr"></div>

				</div><!-- /shadowblock -->

			</div><!-- /shadowblock_out -->

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
