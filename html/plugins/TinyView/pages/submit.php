<?php
	$t_plugin_path = config_get( 'plugin_path' ). 'TinyView' . DIRECTORY_SEPARATOR;
	require_once( $t_plugin_path . 'core' . DIRECTORY_SEPARATOR . 'Helper.php' );
	$helper = Helper::getInstance();
	$user = $_REQUEST['user'];
	$project =  $_REQUEST['project'];
	$category = $_REQUEST['category'];
	$assignee =  $_REQUEST['assignee'];
	$helper->save($user, $project, $category, $assignee);
?>