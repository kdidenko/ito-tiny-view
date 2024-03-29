<?php

/**
 * TinyView Plugin Helper singleton class
 * @author kdidenko
 * @author astabryn
 */

class Helper {
	
	private $instance = null;
	
	/**
	 * Hidden constructor. Singleton pattern
	 */
	private final function __construct() {
	}
	
	/**
	 * Default method used to retreive Helper Class instance
	 * @return Helper
	 */
	public static function getInstance() {
		$instance = $instance == null ? new Helper () : $instance;
		return $instance;
	}
	
	/**
	 * Builds the Users selection table.
	 * @return string
	 */
	public function getUsersOptions() {
		$result = '';
		$t_user_table = plugin_config_get ( 'mantis_user_table' );
		$t_order_field = plugin_config_get ( 'order_field' );
		$query = "SELECT * FROM $t_user_table ORDER BY $t_order_field";
		$set = db_query_bound ( $query );
		$count = db_num_rows ( $set );
		for($i = 0; $i < $count; $i ++) {
			$row = db_fetch_array ( $set );
			$result .= '<option value="' . $row ['id'] . '">' . $row ['realname'] . "</option>\r";
		}
		return $result;
	}
	
	/**
	 * Builds the Projects selection table.
	 * @param Integer $id user ID to retreive projects available.
	 * @return string
	 */
	public function getProjectsOptions($id) {
		$result = '';
		$t_projects = user_get_accessible_projects ( $id, true );
		$t_full_projects = array ();
		foreach ( $t_projects as $t_project_id ) {
			$t_full_projects [] = project_get_row ( $t_project_id );
		}
		$t_projects = multi_sort ( $t_full_projects, 'name', 'ASC' );
		
		for($i = 0; $i < count ( $t_projects ); $i ++) {
			$result .= '<option value="' . $t_projects [$i] ['id'] . '">' . $t_projects [$i] ['name'] . "</option>\r";
		}
		return $result;
	}
	
	/**
	 * Builds the default assignee selection table.
	 * @param Integer $id project ID to retreive all users assigned to.
	 * @return string
	 */
	public function getAssigneeOptions($id) {
		$result = '';
		$t_projects = project_get_all_user_rows ( $id );
		for($i = 0; $i < count ( $t_projects ); $i ++) {
			$result .= '<option value="' . $t_projects [$i] ['id'] . '">' . $t_projects [$i] ['realname'] . "</option>\r";
		}		
		return $result;
	}
	
	/**
	 * Builds the default category selection table.
	 * @param Integer $id project ID to retreive all possible categories.
	 * @return string
	 */
	public function getCategoriesOptions($id) {
		$result = '';
		$t_projects = category_get_all_rows ( $id );
		for($i = 0; $i < count ( $t_projects ); $i ++) {
			$result .= '<option value="' . $t_projects [$i] ['id'] . '">' . $t_projects [$i] ['name'] . "</option>\r";
		}
		return $result;
	}
	
	/**
	 * Method used to fill the table with TinyView setting options.
	 * @param Integer $user ID
	 * @param Integer $project ID
	 * @param Integer $category ID
	 * @param Integer $assignee ID
	 * @return true or false depending on the result.
	 */
	public function save($user, $project, $category, $assignee){		
		$result = null;
		if ($user && $user != '' && $project && $project != '') {
			$query = "INSERT INTO " . plugin_config_get ( 'mantis_tiny_table' ) . 
					  " (user_id, project_id, category_id, assignee_id) VALUES ($user, $project, $category, $assignee)";
			$result = db_query_bound ( $query );
		}
		return $result;		
	} 
	
	/**
	 * Removes the configuration record by ID
	 * @param Integer $id
	 * @return Ambigous <NULL, ADORecordSet, boolean, unknown>
	 */
	public function remove($id) {
		$result = null;
		if ($id && $id != '') {
			$query = "DELETE FROM " . plugin_config_get ( 'mantis_tiny_table' ) . " WHERE id=$id";
			$result = db_query_bound ( $query );
		}
		return $result;
	}
	
	/**
	  * Retreives the default category stored in TinyView configuration.
	 * @param Integer $user
	 * @param Integer $project
	 * @return Integer or FALSE if category was not found.
	 */
	public function getDefCategory($user, $project){
		$result = '';
		$query = 'SELECT category_id FROM mantis_tiny_view
				WHERE user_id =' . $user .' AND project_id=' . $project;
		$set = db_query_bound ( $query );
		$result = db_fetch_array ( $set );
		return $result!=NULL ? $result['category_id'] : false;
	}
	
	/**
	 * Retreives the default assignee stored in TinyView configuration. 
	 * @param Integer $user
	 * @param Integer $project
	 * @return Integer or FALSE if assignee was not found. 
	 */
	public function getDefAssignee($user, $project){
		$result = '';
		$query = 'SELECT assignee_id FROM mantis_tiny_view
				WHERE user_id =' . $user .' AND project_id=' . $project;
		$set = db_query_bound ( $query );
		$result = db_fetch_array ( $set );
		return $result!=NULL ? $result['assignee_id'] : false;
	}
	
	/**
	 * Builds Tiny view table of existing users configuration.
	 * @return string
	 */
	public function getTinyTable() {
		$result = '';
		$query = 'SELECT t.id, user_id, project_id, realname, name ' . 'FROM ' . 
				 plugin_config_get ( 'mantis_tiny_table' ) . ' AS t ' . 
				 'LEFT JOIN `mantis_user_table` AS u ON u.id=t.user_id ' . 
				 'LEFT JOIN `mantis_project_table` AS p ON p.id=t.project_id ' . 
				 'ORDER BY realname ASC';
		$set = db_query_bound ( $query );
		$count = db_num_rows ( $set );
		if ($count > 0) {
			$result = '<table align="center" class="width75" cellspacing="1">';
			for($i = 0; $i < $count; $i ++) {
				$result .= "<tr " . helper_alternate_class () . ">";
				$row = db_fetch_array ( $set );
				$result .= "<td>" . $row ['realname'] . "</td>";
				$result .= "<td>" . $row ['name'] . "</td>";
				$result .= "<td><a href=\"" . plugin_page ( 'remove' ) . "&id=" . $row ['id'] . "\" onclick=\"tinyview.remove(this); return false\">" . plugin_lang_get ( 'remove' ) . "</a></td>";
				$result .= "</tr>";
			}
			$result .= "</table>";
		}
		return $result;
	}
	
	/**
	 * Virifies if Tiny view should be applied for particlar user based on the project
	 * he is accessing.
	 * @param Integer $user
	 * @param Integer $project
	 * @return boolean
	 */
	public function isTinyView($user, $project = null) {
		$query = "SELECT * FROM " . plugin_config_get ( 'mantis_tiny_table' ) . " WHERE user_id = $user";
		if ($project) {
			$query .= " AND project_id = $project";
		}
		$set = db_query_bound ( $query );
		return db_num_rows ( $set ) > 0;
	}

}

?>