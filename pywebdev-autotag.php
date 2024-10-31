<?php
/*
Plugin Name: PyWebDev Autotag
Plugin URI: http://#
Description: Autotag suggest tag on edit post from search engine like Google, Bing, Stackoverflow
Author: PyWebDev
Version: 1.0
Author URI: http://#
*/

define('PYWEBDEV_AUTOTAG_URL', plugin_dir_url(__FILE__));

define('PYWEBDEV_AUTOTAG_DIR', plugin_dir_path(__FILE__));
define('PYWEBDEV_AUTOTAG_JS_URL', PYWEBDEV_AUTOTAG_URL . '/js');
define('PYWEBDEV_AUTOTAG_VIEWS_DIR', PYWEBDEV_AUTOTAG_DIR . '/views');
define('PYWEBDEV_AUTOTAG_INCLUDES_DIR', PYWEBDEV_AUTOTAG_DIR . '/includes');
define('PYWEBDEV_AUTOTAG_SHORTCODE_DIR', PYWEBDEV_AUTOTAG_DIR . '/shortcodes');
define('PYWEBDEV_AUTOTAG_METABOX_DIR', PYWEBDEV_AUTOTAG_DIR . '/metabox');
define('PYWEBDEV_AUTOTAG_SETTING_DIR', PYWEBDEV_AUTOTAG_DIR . '/settings');

if(!is_admin()){
	require_once PYWEBDEV_AUTOTAG_DIR . '/public.php';
	new PyWebDevAutotag();
}else{
	require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/simple_html_dom.php';
	require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/rake.php';
	require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html.php';
	require_once PYWEBDEV_AUTOTAG_DIR . '/admin.php';
	new PyWebDevAutotagAdmin();
	
	require_once PYWEBDEV_AUTOTAG_SETTING_DIR . '/setting.php';
	new PyWebDev_AutoTag_Setting();
}

















