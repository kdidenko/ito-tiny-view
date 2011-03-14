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
	 * Method used to get config values when plugin_config_get API can't be used yet.
	 * @param string $key
	 * @return Ambigous <NULL, string>
	 */
	private function getConfig($key){
		$config = $this->config();
		return array_key_exists($key, $config) ? $config[$key] : null;
	}
	
	/**
	 * Methods used for making a backup of native Mantis files required to be changed.
	 */
	private function doBackup() {
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('view_file');
		$newfile = $file . '.save';
		file_exists($file) ? rename($file, $newfile): null;
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('report_page');
		$newfile = $file . '.save';
		file_exists($file) ? rename($file, $newfile): null;
	}
	
	/**
	 * Method used to rollback the backuped Mantis native files during plugin uninstall process.
	 */
	private function doRollback() {
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('view_file');
		$newfile = $file . '.save';
		file_exists($newfile) ? rename($newfile, $file): null;
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('report_page');
		$newfile = $file . '.save';
		file_exists($newfile) ? rename($newfile, $file): null;
	}	
	
	/**
	 * Method used by installer to copy new files into Mantis root directory.
	 */
	private function doCopy() {
		#view.php copy
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('instal_path') . $this->getConfig('view_file');
		$newfile = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('view_file');
		file_exists($file) ? copy($file, $newfile): null;
		#bug_view_inc_tiny.php copy
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('instal_path') . $this->getConfig('tiny_view');
		$newfile = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('tiny_view');
		file_exists($file) ? copy($file, $newfile): null;
		#bug_report_tiny.php copy
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('instal_path') . $this->getConfig('tiny_report');
		$newfile = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('tiny_report');
		file_exists($file) ? copy($file, $newfile): null;
		#bug_report_standard.php copy
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('instal_path') . $this->getConfig('standard_report');
		$newfile = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('standard_report');
		file_exists($file) ? copy($file, $newfile): null;
		#bug_report_page.php copy
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('instal_path') . $this->getConfig('report_page');
		$newfile = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('report_page');
		file_exists($file) ? copy($file, $newfile): null;
	}
	
	/**
	 * Method used by installer during plugin uninstall process
	 */
	private function doRemove() {
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('view_file');
		file_exists($file) ? unlink($file) : null;
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('tiny_view');
		file_exists($file) ? unlink($file) : null;
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('tiny_report');
		file_exists($file) ? unlink($file) : null;
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('standard_report');
		file_exists($file) ? unlink($file) : null;
		$file = $_SERVER[DOCUMENT_ROOT] . '/' . $this->getConfig('report_page');
		file_exists($file) ? unlink($file) : null;
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
					 '`project_id` INT( 10 ) NOT NULL ,' . 
					 '`category_id` INT( 10 ) NOT NULL ,' .  
					 '`assignee_id` INT( 10 ) NOT NULL , INDEX (`user_id`))';
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
		$hooks = array ('EVENT_VIEW_BUG_BEGIN' => 'switch_view',
						'EVENT_REPORT_BUG_BEGIN' => 'switch_report',);
		return $hooks;
	}
	
	/**
	 * Plugin configuration options
	 */
	public function config() {
		return array ('view_file' => 'view.php', 
					  'instal_path' => 'plugins/TinyView/install/', 
					  'tiny_view' => 'bug_view_inc_tiny.php',
					  'tiny_report' => 'bug_report_tiny.php', 
					  'standard_view' => 'bug_view_inc.php',
					  'standard_report' => 'bug_report_standard.php',
					  'report_page' => 'bug_report_page.php',
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
		return array ('EVENT_VIEW_BUG_BEGIN' => 'switch_view',
					  'EVENT_REPORT_BUG_BEGIN' => 'switch_report',);
	}
	
	public function switch_view($event, $user = null) {
		$t_project = helper_get_current_project();
		$result = array (plugin_config_get ( 'standard_view' ) );
		if ($user) {
			$helper = Helper::getInstance ();
			if ($helper->isTinyView ( $user['id'], $t_project )) {
				$result = array (plugin_config_get ( 'tiny_view' ) );
			}
		}
		return $result;
	}
	
	public function switch_report($event, $user = null ) {
		$t_project = helper_get_current_project();
		$result = array (plugin_config_get ( 'standard_report' ) );
		if ($user) {
			$helper = Helper::getInstance ();
			if ($helper->isTinyView ( $user['id'], $t_project )) {
				$result = array (plugin_config_get ( 'tiny_report' ) );
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