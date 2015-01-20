
<!-- right block -->
<div class="content_right">

	<?php appthemes_before_sidebar_widgets(); ?>

	<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_author') ) : else : ?>

	<!-- no dynamic sidebar so don't do anything -->

	<?php endif; ?>

	<?php appthemes_after_sidebar_widgets(); ?>

</div><!-- /content_right -->
