<?php
define( 'ABSPATH', dirname(__FILE__) . '/' );


$state = $_GET['state'];
if ($state == 'pre') {
        if ( file_exists( ABSPATH . 'wp-config.php') ) {
                rename('wp-config.php', 'wp-config.phpbak');
                echo '<div class="status">self</div>';
        } elseif ( file_exists( dirname(ABSPATH) . '/wp-config.php' ) && ! file_exists( dirname(ABSPATH) . '/wp-settings.php' ) ) {
                copy(ABSPATH . '/wp-settings.php', dirname(ABSPATH) . '/wp-settings.php');
                echo '<div class="status">parent</div>';        
        } else {
                echo '<div class="status">normal</div>';
        }
} elseif ($state == 'post') {
        if($_GET['return'] == 'parent') {
                unlink(dirname(ABSPATH) . '/wp-settings.php');
                echo 'removed file';
        }else {
                echo 'did nothing';
        }
unlink(__FILE__);
}

