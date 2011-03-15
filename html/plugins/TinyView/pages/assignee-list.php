<?php
	$t_plugin_path = config_get( 'plugin_path' ). 'TinyView' . DIRECTORY_SEPARATOR;
	require_once( $t_plugin_path . 'core' . DIRECTORY_SEPARATOR . 'Helper.php' );
	$helper = Helper::getInstance();
	$id = $_REQUEST['id'];
	echo $helper->getAssigneeOptions($id);
?>