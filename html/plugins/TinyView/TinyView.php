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
	
	/**
	 * Methods used for making a backup of native Mantis files required to be changed.
	 */
	private function doBackup() {
		
	}
	
	/**
	 * Method used to rollback the backuped Mantis native files during plugin uninstall process.
	 */
	private function doRollback() {
		
	}	
	
	/**
	 * Method used by installer to copy new files into Mantis root directory.
	 */
	private function doCopy() {
		
	}
	
	/**
	 * Method used by installer during plugin uninstall process
	 */
	private function doRemove() {
		
	}	
	
	/** 
	 * Metod required for database creation during plugin installation
	 */
	public function install() {
		$result = null;
		try {
			$query = 'CREATE TABLE IF NOT EXISTS `mantis_tiny_view` (' . 
					 '`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,' . 
					 '`user_id` INT( 10 ) NOT NULL ,' . 
					 '`project_id` INT( 10 ) NOT NULL , INDEX (`user_id`))';
			db_query_bound ( $query );
			$this->doBackup();
			$this->doCopy();
			$result = true;			
		} catch (Exception $e) {
			$result = $e;	
		}
		return $result;
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
					  'tiny_table' => '?page=TinyView/tiny-table',
					  'backup_ext' => '.save',
					  'install_dir' => 'install' 
		);	
	}
	
	/** 
	 * Plugin's custom events
	 */
	public function events() {
		return array ('EVENT_VIEW_BUG_BEGIN' => EVENT_VIEW_BUG_BEGIN );
	}
	
	public function switch_view($event, $user = null, $f_bug_id = null) {
		$t_project = helper_get_current_project();
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
		$result = null;
		try {
			$query = 'DROP TABLE IF EXISTS `mantis_tiny_view`';
			db_query_bound ( $query );
			$this->doRemove();
			$this->doRollback();
			$result = true;			
		} catch (Exception $e) {
			$result = $e;
		}
		return $result;
	}
}
?>