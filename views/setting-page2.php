<div class="wrap">

	<h2>My Setting</h2>
	
	<?php settings_errors( $this->_menu_slug, false, false );?>
	
	<p>Đây là trang hiển thị các cấu hình của PyWebDev Autotag</p>
	<form method="post" action="options.php" id="<?php echo $this->_menu_slug;?>" enctype="multipart/form-data">
		<?php echo settings_fields($this->_menu_slug);?>
		<?php echo do_settings_sections($this->_menu_slug);?>
		
		<p class="submit">
			<input id="btn-save-change" type="submit" name="submit" value="Save change"  class="button button-primary" >
		</p>
	</form>

</div>