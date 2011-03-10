<?php
	$t_plugin_path = config_get( 'plugin_path' ). 'TinyView' . DIRECTORY_SEPARATOR;
	require_once( $t_plugin_path . 'core' . DIRECTORY_SEPARATOR . 'Helper.php' );
	$id = $_REQUEST['id'];
	$helper = Helper::getInstance();
	$helper->remove($id);
?>