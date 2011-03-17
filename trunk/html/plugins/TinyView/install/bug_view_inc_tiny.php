<?php
# kdidenko: 10/02/2011 was modified according to TinyView plugin 
# original file: bug_view_inc.php

	/**
	 * This include file prints out the bug information
	 * $f_bug_id MUST be specified before the file is included
	 *
	 * @package MantisBT
	 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	 * @copyright Copyright (C) 2002 - 2010  MantisBT Team - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */

	if ( !defined( 'BUG_VIEW_INC_ALLOW' ) ) {
		access_denied();
	}

	 /**
	  * MantisBT Core API's
	  */
	require_once( 'core.php' );

	require_once( 'bug_api.php' );
	require_once( 'custom_field_api.php' );
	require_once( 'file_api.php' );
	require_once( 'date_api.php' );
	require_once( 'relationship_api.php' );
	require_once( 'last_visited_api.php' );
	require_once( 'tag_api.php' );

	$f_bug_id = gpc_get_int( 'id' );

	bug_ensure_exists( $f_bug_id );

	$tpl_bug = bug_get( $f_bug_id, true );

	# In case the current project is not the same project of the bug we are
	# viewing, override the current project. This ensures all config_get and other
	# per-project function calls use the project ID of this bug.
	$g_project_override = $tpl_bug->project_id;

	access_ensure_bug_level( VIEWER, $f_bug_id );

	$t_fields = config_get( $tpl_fields_config_option );
	$t_fields = columns_filter_disabled( $t_fields );

	compress_enable();

	if ( $tpl_show_page_header ) {
		html_page_top( bug_format_summary( $f_bug_id, SUMMARY_CAPTION ) );
		print_recently_visited();
	}

	$t_action_button_position = config_get( 'action_button_position' );

	$t_bugslist = gpc_get_cookie( config_get( 'bug_list_cookie' ), false );

	$tpl_bug_id = $f_bug_id;
	$tpl_form_title = lang_get( 'bug_view_title' );
	$tpl_wiki_link = config_get_global( 'wiki_enable' ) == ON ? 'wiki.php?id=' . $f_bug_id : '';

	$tpl_show_project = in_array( 'project', $t_fields );
	$tpl_project_name = $tpl_show_project ? string_display_line( project_get_name( $tpl_bug->project_id ) ): '';
	$tpl_show_id = in_array( 'id', $t_fields );
	$tpl_formatted_bug_id = $tpl_show_id ? string_display_line( bug_format_id( $f_bug_id ) ) : '';

	$tpl_show_date_submitted = in_array( 'date_submitted', $t_fields );
	$tpl_date_submitted = $tpl_show_date_submitted ? date( config_get( 'normal_date_format' ), $tpl_bug->date_submitted ) : '';

	$tpl_show_last_updated = in_array( 'last_updated', $t_fields );
	$tpl_last_updated = $tpl_show_last_updated ? date( config_get( 'normal_date_format' ), $tpl_bug->last_updated ) : '';

	$tpl_show_due_date = in_array( 'due_date', $t_fields ) && access_has_bug_level( config_get( 'due_date_view_threshold' ), $f_bug_id );

	if ( $tpl_show_due_date ) {
		if ( !date_is_null( $tpl_bug->due_date ) ) {
			$tpl_bug_due_date = date( config_get( 'normal_date_format' ), $tpl_bug->due_date );
		} else {
			$tpl_bug_due_date = '';
		}
	}
$tpl_show_upload_form = !$tpl_force_readonly && !bug_is_readonly( $f_bug_id );
	$tpl_show_additional_information = !is_blank( $tpl_bug->additional_information ) && in_array( 'additional_info', $t_fields );
	$tpl_show_steps_to_reproduce = !is_blank( $tpl_bug->steps_to_reproduce ) && in_array( 'steps_to_reproduce', $t_fields );
	$tpl_show_projection = in_array( 'projection', $t_fields );
	$tpl_projection = $tpl_show_projection ? string_display_line( get_enum_element( 'projection', $tpl_bug->projection ) ) : '';
	$tpl_show_attachments = in_array( 'attachments', $t_fields ) && ( ( $tpl_bug->reporter_id == auth_get_current_user_id() ) || access_has_bug_level( config_get( 'view_attachments_threshold' ), $f_bug_id ) );
	$tpl_show_status = in_array( 'status', $t_fields );
	$tpl_status = $tpl_show_status ? string_display_line( get_enum_element( 'status', $tpl_bug->status ) ) : '';
	$tpl_show_summary = in_array( 'summary', $t_fields );
	$tpl_show_description = in_array( 'description', $t_fields );
	$tpl_summary = $tpl_show_summary ? string_display_line_links( bug_format_summary( $f_bug_id, SUMMARY_FIELD ) ) : '';
	$tpl_description = $tpl_show_description ? string_display_links( $tpl_bug->description ) : '';
	$tpl_steps_to_reproduce = $tpl_show_steps_to_reproduce ? string_display_links( $tpl_bug->steps_to_reproduce ) : '';
	$tpl_additional_information = $tpl_show_additional_information ? string_display_links( $tpl_bug->additional_information ) : '';

	$tpl_links = event_signal( 'EVENT_MENU_ISSUE', $f_bug_id );

	#
	# Start of Template
	#

	echo '<br />';
	echo '<table class="width100" cellspacing="1">';
	echo '<tr>';

	# Form Title
	echo '<td class="form-title" colspan="', $t_bugslist ? '3' : '4', '">';

	echo $tpl_form_title;

	echo '&nbsp;<span class="small">';

	if ( !is_blank( $tpl_wiki_link ) ) {
		print_bracket_link( $tpl_wiki_link, lang_get( 'wiki' ) );
	}

	foreach ( $tpl_links as $t_plugin => $t_hooks ) {
		foreach( $t_hooks as $t_hook ) {
			if ( is_array( $t_hook ) ) {
				foreach( $t_hook as $t_label => $t_href ) {
					if ( is_numeric( $t_label ) ) {
						print_bracket_link_prepared( $t_href );
					} else {
						print_bracket_link( $t_href, $t_label );
					}
				}
			} else {
				print_bracket_link_prepared( $t_hook );
			}
		}
	}

	echo '</span></td>';

	# prev/next links
	if ( $t_bugslist ) {
		echo '<td class="center"><span class="small">';

		$t_bugslist = explode( ',', $t_bugslist );
		$t_index = array_search( $f_bug_id, $t_bugslist );
		if ( false !== $t_index ) {
			if ( isset( $t_bugslist[$t_index-1] ) ) {
				print_bracket_link( 'bug_view_page.php?bug_id='.$t_bugslist[$t_index-1], '&lt;&lt;' );
			}

			if ( isset( $t_bugslist[$t_index+1] ) ) {
				print_bracket_link( 'bug_view_page.php?bug_id='.$t_bugslist[$t_index+1], '&gt;&gt;' );
			}
		}
		echo '</span></td>';
	}


	# Links
	echo '<td class="right" colspan="2">';

	if ( $tpl_show_id || $tpl_show_project || $tpl_show_category || $tpl_show_date_submitted || $tpl_show_last_updated ) {
		# Labels
		echo '<tr>';
		echo '<td class="category" width="15%">', $tpl_show_id ? lang_get( 'id' ) : '', '</td>';
		echo '<td class="category" width="20%" colspan="3">', $tpl_show_project ? lang_get( 'email_project' ) : '', '</td>';
		echo '<td class="category" width="15%">', $tpl_show_date_submitted ? lang_get( 'date_submitted' ) : '', '</td>';
		echo '<td class="category" width="20%">', $tpl_show_last_updated ? lang_get( 'last_update' ) : '','</td>';
		echo '</tr>';

		echo '<tr ', helper_alternate_class(), '>';

		# Bug ID
		echo '<td>', $tpl_formatted_bug_id, '</td>';

		# Project
		echo '<td colspan="3">', $tpl_project_name, '</td>';

		# Date Submitted
		echo '<td>', $tpl_date_submitted, '</td>';

		# Date Updated
		echo '<td>', $tpl_last_updated, '</td>';

		echo '</tr>';
	}

	#
	# Status, Resolution
	#

	if ( $tpl_show_status || $tpl_show_resolution ) {
		echo '<tr ', helper_alternate_class(), '>';

		$t_spacer = 2;

		# Status
		if ( $tpl_show_status ) {
			echo '<td class="category">', lang_get( 'status' ), '</td>';
			echo '<td bgcolor="', get_status_color( $tpl_bug->status ), '">', $tpl_status, '</td>';
		} else {
			$t_spacer += 2;
		}

		# Resolution
		if ( $tpl_show_resolution ) {
			echo '<td class="category">', lang_get( 'resolution' ), '</td>';
			echo '<td>', $tpl_resolution, '</td>';
		} else {
			$t_spacer += 2;
		}

		# spacer
		if ( $t_spacer > 0 ) {
			echo '<td colspan="', $t_spacer, '">&nbsp;</td>';
		}

		echo '</tr>';
	}

	#
	# Projection, ETA
	#

	if ( $tpl_show_projection || $tpl_show_eta ) {
		echo '<tr ', helper_alternate_class(), '>';

		$t_spacer = 2;

		if ( $tpl_show_projection ) {
			# Projection
			echo '<td class="category">', lang_get( 'projection' ), '</td>';
			echo '<td>', $tpl_projection, '</td>';
		} else {
			$t_spacer += 2;
		}

		# ETA
		if ( $tpl_show_eta ) {
			echo '<td class="category">', lang_get( 'eta' ), '</td>';
			echo '<td>', $tpl_eta, '</td>';
		} else {
			$t_spacer += 2;
		}

		echo '<td colspan="', $t_spacer, '">&nbsp;</td>';
		echo '</tr>';
	}

	#
	# Bug Details Event Signal
	#

	event_signal( 'EVENT_VIEW_BUG_DETAILS', array( $tpl_bug_id ) );

	#
	# Bug Details (screen wide fields)
	#

	# Summary
	if ( $tpl_show_summary ) {
		echo '<tr ', helper_alternate_class(), '>';
		echo '<td class="category">', lang_get( 'summary' ), '</td>';
		echo '<td colspan="5">', $tpl_summary, '</td>';
		echo '</tr>';
	}

	# Description
	if ( $tpl_show_description ) {
		echo '<tr ', helper_alternate_class(), '>';
		echo '<td class="category">', lang_get( 'description' ), '</td>';
		echo '<td colspan="5">', $tpl_description, '</td>';
		echo '</tr>';
	}

	# Steps to Reproduce
	if ( $tpl_show_steps_to_reproduce ) {
		echo '<tr ', helper_alternate_class(), '>';
		echo '<td class="category">', lang_get( 'steps_to_reproduce' ), '</td>';
		echo '<td colspan="5">', $tpl_steps_to_reproduce, '</td>';
		echo '</tr>';
	}

	# Additional Information
	if ( $tpl_show_additional_information ) {
		echo '<tr ', helper_alternate_class(), '>';
		echo '<td class="category">', lang_get( 'additional_information' ), '</td>';
		echo '<td colspan="5">', $tpl_additional_information, '</td>';
		echo '</tr>';
	}

	# Custom Fields
	$t_custom_fields_found = false;
	if ( $t_custom_fields_found ) {
		# spacer
		echo '<tr class="spacer"><td colspan="6"></td></tr>';
	} # custom fields found

	# Attachments
	if ( $tpl_show_attachments ) {
		echo '<tr ', helper_alternate_class(), '>';
		echo '<td class="category"><a name="attachments" id="attachments" />', lang_get( 'attached_files' ), '</td>';
		echo '<td colspan="5">';
		print_bug_attachments_list( $tpl_bug_id );
		echo '</td></tr>';
	}

	echo '</table>';

	# File upload box
	if ( $tpl_show_upload_form ) { 
		include( $tpl_mantis_dir . 'bug_file_upload_inc.php' );
	}
	
	# User list sponsoring the bug
	include( $tpl_mantis_dir . 'bug_sponsorship_list_view_inc.php' );

	# Bugnotes and "Add Note" box
	if ( 'ASC' == current_user_get_pref( 'bugnote_order' ) ) {
		include( $tpl_mantis_dir . 'bugnote_view_inc.php' );

		if ( !$tpl_force_readonly ) {
			include( $tpl_mantis_dir . 'bugnote_add_inc.php' );
		}
	} else {
		if ( !$tpl_force_readonly ) {
			include( $tpl_mantis_dir . 'bugnote_add_inc.php' );
		}

		include( $tpl_mantis_dir . 'bugnote_view_inc.php' );
	}
	
	# Allow plugins to display stuff after notes
	event_signal( 'EVENT_VIEW_BUG_EXTRA', array( $f_bug_id ) );

	# Time tracking statistics
	if ( config_get( 'time_tracking_enabled' ) &&
		access_has_bug_level( config_get( 'time_tracking_view_threshold' ), $f_bug_id ) ) {
		include( $tpl_mantis_dir . 'bugnote_stats_inc.php' );
	}

	html_page_bottom();

	last_visited_issue( $tpl_bug_id );
