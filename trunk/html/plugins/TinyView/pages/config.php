<?php
	$t_plugin_path = config_get( 'plugin_path' ). 'TinyView' . DIRECTORY_SEPARATOR;
	require_once( $t_plugin_path . 'core' . DIRECTORY_SEPARATOR . 'Helper.php' );
	$helper = Helper::getInstance();
	
	auth_reauthenticate( );
	access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

	html_page_top( plugin_lang_get( 'title' ) );
?>	
<script type="text/javascript" src="<?php echo plugin_file('jquery-151min.js') ?>"></script>
<script type="text/javascript">
<!--
$(document).ready(function() {
	 $("form").submit(function(e) {
		var str = $(this).serialize();
		$.post($(this).attr('action'), str, function(data){
			tinyview.reload();
			//tinyview.settings();
		});
		e.preventDefault();
	 });
	 tinyview.reload();
});
var tinyview = {		
		userchange: function(e) {
			if(e.selectedIndex > 0)  {
				$('#project').removeAttr('disabled');
				var search = '<?php echo plugin_config_get('projects_list')?>' + '&id='+e.value;
				$.post(search, function(data){
					if(data && data != ''){
						$('#project').html(data);
						$('#tiny').removeAttr('disabled');
					}	
				});
			}else{
				tinyview.disablectrl();
			}
		},
		projectchange: function(e) {
			if(e.selectedIndex > 0)  {
				var search = '<?php echo plugin_config_get('asiignee_list')?>' + '&id='+e.value;
				$.post(search, function(data){
					if(data && data != ''){
						$('#assignee').removeAttr('disabled');
						$('#assignee').html(data);
					}	
				});
				var search = '<?php echo plugin_config_get('categories_list')?>' + '&id='+e.value;
				$.post(search, function(data){
					if(data && data != ''){
						$('#category').removeAttr('disabled');
						$('#category').html(data);
					}	
				});
			}else{
				tinyview.disablectrl();
			}
		},	
		marked: function(e) {
			if(e.checked){
				$('#apply').removeAttr('disabled');
				$('#setting').fadeIn('fast', function() {});
			}else{
				$('#apply').attr('disabled', 'disabled');
				$('#setting').fadeOut('slow', function() {});
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
		settings: function(){
			$.get('<?php echo plugin_config_get('tiny_setting')?>', function(data){
				$('#setting').html(data);
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
		<td class="form-title" colspan="3">
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
		<td class="tiny">
			<label for="tiny"><?php echo plugin_lang_get('tiny')?></label>
			<input id="tiny" type="checkbox" name="tiny" disabled="disabled" onclick="tinyview.marked(this)" />
		</td>
	</tr>
	<tr <?php echo helper_alternate_class( )?> id="setting" style="display: none">
		<td class="projects">
			<label for="assignee"><?php echo plugin_lang_get('assignee')?></label>
			<select id="assignee" name="assignee" disabled="disabled">
				<option><?php echo plugin_lang_get('select_assignee')?></option>			
			</select>		
		</td>
		<td class="projects"  colspan="2">
			<label for="category"><?php echo plugin_lang_get('category')?></label>
			<select id="category" name="category" disabled="disabled">
				<option><?php echo plugin_lang_get('select_category')?></option>			
			</select>		
		</td>
	</tr>
	<tr>
		<td class="center" colspan="3">
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