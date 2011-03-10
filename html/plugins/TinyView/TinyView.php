<?php

/**
 * Extended from the Mantis plugin class
 */
require_once (config_get ( 'class_path' ) . 'MantisPlugin.class.php');
require_once 'core/Helper.php';

/**
 * Plugin base class
 * @author kdidenko
 */
class TinyViewPlugin extends MantisPlugin {
	
	private function replace(){
		
	}
	
	/** 
	 * Metod required for database creation during plugin installation
	 */
	public function install() {
		$query = 'CREATE TABLE IF NOT EXISTS `mantis_tiny_view` (' . 
				 '`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,' . 
				 '`user_id` INT( 10 ) NOT NULL ,' . 
				 '`project_id` INT( 10 ) NOT NULL , INDEX (`user_id`))';
		return db_query_bound ( $query );
	}
	
	/** 
	 * Method required for proper plugin registration
	 */
	public function register() {
		$this->name = plugin_lang_get ( 'title' );
		$this->description = plugin_lang_get ( 'description' );
		$this->page = 'config';
		
		$this->version = '0.1';
		$this->requires = array ('MantisCore' => '1.2.0' );
		
		$this->author = 'ITO Global Team';
		$this->contact = 'support@ito-global.com';
		$this->url = 'http://www.ito-global.com';
	}
	
	/**
	 * Plugin's hooks
	 */
	public function hooks() {
		$hooks = array ('EVENT_VIEW_BUG_BEGIN' => 'switch_view',);
		return $hooks;
	}
	
	/**
	 * Plugin configuration options
	 */
	public function config() {
		return array ('view_file' => 'view.php', 
					  'tiny_file' => 'bug_view_inc_tiny.php', 
					  'standard' => 'bug_view_inc.php', 
					  'mantis_user_table' => 'mantis_user_table', 
					  'mantis_tiny_table' => 'mantis_tiny_view', 
					  'order_field' => 'realname', 
					  'projects_list' => '?page=TinyView/projects-list', 
					  'tiny_table' => '?page=TinyView/tiny-table' 
		);	
	}
	
	/** 
	 * Plugin's custom events
	 */
	public function events() {
		return array ('EVENT_VIEW_BUG_BEGIN' => EVENT_VIEW_BUG_BEGIN );
	}
	
	public function switch_view($event, $user = null, $f_bug_id = null) {
		// get the project id
		$t_project = null;
		$f_bug_id = gpc_get_int( 'id' );
		if ($f_bug_id){
			$tpl_bug = bug_get( $f_bug_id, true );
			$t_project = $tpl_bug->project_id;			
		}			
		$result = $result = array (plugin_config_get ( 'standard' ) );
		if ($user) {
			$helper = Helper::getInstance ();
			if ($helper->isTinyView ( $user['id'], $t_project )) {
				$result = array (plugin_config_get ( 'tiny_file' ) );
			}
		}
		return $result;
	}
	
	/**
	 * Method required to uninstall the plugin and to remove all it's data
	 */
	public function uninstall() {
		$query = 'DROP TABLE IF EXISTS `mantis_tiny_view`';
		db_query_bound ( $query );
	}
}
?>