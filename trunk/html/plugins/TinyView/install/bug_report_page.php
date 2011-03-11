<?php
# MantisBT - a php based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

	/**
	 * This file POSTs data to report_bug.php
	 *
	 * @package MantisBT
	 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	 * @copyright Copyright (C) 2002 - 2010  MantisBT Team - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */
	 $g_allow_browser_cache = 1;

	 /**
	  * MantisBT Core API's
	  */
	require_once( 'core.php' );

	require_once( 'file_api.php' );
	require_once( 'custom_field_api.php' );
	require_once( 'last_visited_api.php' );
	require_once( 'projax_api.php' );
	require_once( 'collapse_api.php' );
	
	//print_r($g_cache_user);exit;
	$tpl_view = event_signal( 'EVENT_REPORT_BUG_BEGIN', $g_cache_user );
	
	$tpl_view_include = $tpl_view['TinyView']['switch_report'][0];
	 
	include ($tpl_view_include);
	
	
html_page_bottom();
