<div class="wrap">

<div id="icon-themes" class="icon32"><br /></div>
<h2><?php _e('Sidebars', 'gp_lang'); ?></h2>
<?php $this->message(); ?>	

<div id="customsidebarspage">
<div id="poststuff">
	
	<h2 class="title"><?php _e('New Sidebar', 'gp_lang'); ?></h2>
	<p><?php _e('Add your sidebars below and then you can assign one of these sidebars from the individual posts/pages. When a sidebar is created, it is shown on the widgets page where you will be able to configure it.', 'gp_lang'); ?></p>
	<form action="themes.php?page=sidebars" method="post">
		<?php wp_nonce_field( 'custom-sidebars-new');?>
		<div id="namediv" class="stuffbox">
			<h3><label for="sidebar_name"><?php _e('Name', 'gp_lang'); ?></label></h3>
			<div class="inside">
				<input type="text" name="sidebar_name" size="30" tabindex="1" value="" id="link_name" />
				<p><?php _e('This name has to be unique.', 'gp_lang')?></p>
			</div>
		</div>
			
		<div id="namediv" class="stuffbox">			
			<h3><label for="sidebar_description"><?php echo _e('Description', 'gp_lang'); ?></label></h3>
			<div class="inside">
				<input type="text" name="sidebar_description" size="30" class="code" tabindex="1" value="" id="link_url" />
			</div>
		</div>
		
		<p class="submit"><input type="submit" class="button-primary" name="create-sidebars" value="<?php _e('Create Sidebar', 'gp_lang'); ?>" /></p>
	</form>


<?php
////////////////////////////////////////////////////////
//SIDEBARLIST
////////////////////////////////////////////////////////////
?>

<div id="sidebarslistdiv">
	<script type="text/javascript">
		jQuery(document).ready( function($){
			$('.csdeletelink').click(function(){
				return confirm('<?php _e('Are you sure to delete this sidebar?', 'gp_lang');?>');
			});
		});
	</script>
	<h2><?php _e('Custom Sidebars', 'gp_lang'); ?></h2>

	<table class="widefat fixed" cellspacing="0">
	
	<thead>
		<tr class="thead">
			<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Name', 'gp_lang'); ?></th>
			<th scope="col" id="email" class="manage-column column-email" style=""><?php _e('Description', 'gp_lang'); ?></th>
			<th scope="col" id="email" class="manage-column column-email" style=""><?php _e('ID', 'gp_lang'); ?></th>
			<th scope="col" id="config" class="manage-column column-date" style=""></th>
			<th scope="col" id="edit" class="manage-column column-rating" style=""></th>
			<th scope="col" id="delete" class="manage-column column-rating" style=""></th>
		</tr>
	</thead>
	
	
	<tbody id="custom-sidebars" class="list:user user-list">
	
		<?php if(sizeof($customsidebars)>0): foreach($customsidebars as $cs):?>
		<tr id="cs-1" class="alternate">
			<td class="name column-name"><?php echo $cs['name']?></td>
			<td class="email column-email"><?php echo $cs['description']?></td>
			<td class="email column-email"><?php echo $cs['id']?></td>
			<td class="role column-date"><a class="" href="widgets.php"><?php _e('Configure Widgets', 'gp_lang'); ?></a></td>
			<td class="role column-rating"><a class="" href="themes.php?page=sidebars&p=edit&id=<?php echo $cs['id']; ?>"><?php _e('Edit', 'gp_lang'); ?></a></td>
			<td class="role column-rating"><a class="csdeletelink" href="themes.php?page=sidebars&delete=<?php echo $cs['id']; ?>&_n=<?php echo $deletenonce; ?>"><?php _e('Delete', 'gp_lang'); ?></a></td>
		</tr>
		<?php endforeach;else:?>
		<tr id="cs-1" class="alternate">
			<td colspan="3"><?php _e('There are no custom sidebars available.', 'gp_lang'); ?></td>
		</tr>
		<?php endif;?>
		
	</tbody>
	
	</table>
</div>





<?php
////////////////////////////////////////////////////////
//RESET SIDEBARS
////////////////////////////////////////////////////////////
?>
<div id="resetsidebarsdiv">
	<form action="themes.php?page=sidebars" method="post">
	<input type="hidden" name="reset-n" value="<?php echo $deletenonce; ?>" />
	<h2><?php _e('Reset Sidebars', 'gp_lang'); ?></h2>
	<p><?php _e('Click on the button below to delete all the custom sidebars data from the database. Keep in mind that once the button is clicked you will have to create new sidebars and customize them to restore your current sidebars configuration.</p>', 'gp_lang'); ?></p>
	
	<p class="submit"><input onclick="return confirm('<?php _e('Are you sure to reset the sidebars?', 'gp_lang'); ?>')"type="submit" class="button-primary" name="reset-sidebars" value="<?php _e('Reset Sidebars', 'gp_lang'); ?>" /></p>
	
	</form>
</div>

<?php /*REMOVE include('footer.php'); */ ?>


</div>
</div>

</div>