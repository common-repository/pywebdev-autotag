<div class="wrap">

	<h2>My Setting</h2>
	
	<?php settings_errors( $this->_menuSlug, false, false );?>
	
	<p>Đây là trang hiển thị các cấu hình của PyWebDev Autotag</p>
	<form method="post" action="options.php" id="pywebdev-mp-form-setting" enctype="multipart/form-data">
		<?php echo settings_fields('pywebdev_autotag_options');?>
		<?php echo do_settings_sections($this->_menuSlug);?>
		
		<p class="submit">
			<input type="submit" name="submit" value="Save change"  class="button button-primary" >
		</p>
	</form>

</div>