<?php
	$t_plugin_path = config_get( 'plugin_path' ). 'TinyView' . DIRECTORY_SEPARATOR;
	require_once( $t_plugin_path . 'core' . DIRECTORY_SEPARATOR . 'Helper.php' );
	$helper = Helper::getInstance();
	
	auth_reauthenticate( );
	access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

	html_page_top( plugin_lang_get( 'title' ) );
	//TODO: remove inline JavaScript into external file 
?>	
<script type="text/javascript" src="<?php echo plugin_file('jquery-151min.js') ?>"></script>
<script type="text/javascript">
<!--
$(document).ready(function() {
	 $("form").submit(function(e) {
		var str = $(this).serialize();
		$.post($(this).attr('action'), str, function(data){
			tinyview.reload();
		});
		e.preventDefault();
	 });
	 tinyview.reload();
});
var tinyview = {		
		userchange: function(e) {
			if(e.selectedIndex > 0)  {
				$('#project').removeAttr('disabled');
				$('#assignee').removeAttr('disabled');
				$('#category').removeAttr('disabled');
				var search = '<?php echo plugin_config_get('projects_list')?>' + '&id='+e.value;
				$.post(search, function(data){
					if(data && data != ''){
						$('#project').html(data);
					}	
				});
			}else{
				tinyview.disablectrl();
			}
		},
		projectchange: function(e) {
			if(e.selectedIndex > 0)  {
				$('#apply').removeAttr('disabled');
				var search = '<?php echo plugin_config_get('asiignee_list')?>' + '&id='+e.value;
				$.post(search, function(data){
					if(data && data != ''){
						$('#assignee').html(data);
					}	
				});
				var search = '<?php echo plugin_config_get('categories_list')?>' + '&id='+e.value;
				$.post(search, function(data){
					if(data && data != ''){
						$('#category').html(data);
					}	
				});
			}else{
				tinyview.disablectrl();
			}
		},	
		remove: function(e){
			$.get(e.href, function(data){
				tinyview.reload();
				return false;
			});			
		},		
		reload: function(){
			$.get('<?php echo plugin_config_get('tiny_table')?>', function(data){
				$('#list').html(data);
			});
		},
		disablectrl: function(){
			$('#project').attr('disabled', 'disabled');
			$('#tiny').attr('disabled', 'disabled');
			$('#tiny').attr('checked', false);
			$('#apply').attr('disabled', 'disabled');
		}
   };
//-->
</script>
<?php
	print_manage_menu();	
?>	
<br/>
<form action="<?php echo plugin_page( 'submit' )?>" method="post">
<?php echo form_security_field( 'plugin_graph_config_edit' ) ?>
<div id="control">
	<table align="center" class="width75" cellspacing="1">
	<tr>
		<td class="form-title" colspan="2">
			<?php echo plugin_lang_get( 'title' ) . ': ' . plugin_lang_get( 'config' )?>
		</td>
	</tr>
	<tr <?php echo helper_alternate_class( )?>>
		<td class="users">
			<label for="user"><?php echo plugin_lang_get('user')?></label>
			<select id="user" name="user" onchange="tinyview.userchange(this)">
				<option><?php echo plugin_lang_get('select_user')?></option>
				<?php echo $helper->getUsersOptions() ?>		
			</select>
		</td>
		<td class="projects">
			<label for="project"><?php echo plugin_lang_get('project')?></label>
			<select id="project" name="project" disabled="disabled" onchange="tinyview.projectchange(this)">
				<option><?php echo plugin_lang_get('select_project')?></option>			
			</select>		
		</td>
	</tr>
	<tr <?php echo helper_alternate_class( )?>>
		<td class="projects">
			<label for="assignee"><?php echo plugin_lang_get('assignee')?></label>
			<select id="assignee" name="assignee" disabled="disabled">
				<option><?php echo plugin_lang_get('select_assignee')?></option>			
			</select>		
		</td>
		<td class="projects" >
			<label for="category"><?php echo plugin_lang_get('category')?></label>
			<select id="category" name="category" disabled="disabled">
				<option><?php echo plugin_lang_get('select_category')?></option>			
			</select>		
		</td>
	</tr>
	<tr>
		<td class="center" colspan="2">
			<input type="submit" class="button" id="apply" value="<?php echo plugin_lang_get( 'apply' )?>" disabled="disabled" />
		</td>
	</tr>
	</table>
</div>
<div id="list">
<!-- List begin -->
<!-- List end -->
</div>	
<?php 	
	html_page_bottom();
?>