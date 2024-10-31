<?php
require_once PYWEBDEV_AUTOTAG_DIR . '/includes/support.php';
class PyWebDevAutotag{
	
	public function __construct(){
		//echo '<br/>' . __METHOD__;
		//=====================================================
		//Hiển thị các Action đang thực thi trong Hook
		//=====================================================
		add_action('wp_footer', array($this,'showFunction'));				
		
	}
		
	//=====================================================
	//Hiển thị các Action đang thực thi trong Hook
	//=====================================================
	public function showFunction(){
		PyWebDevAutotagSupport::showFunc('widget_title');
	}	
	
}