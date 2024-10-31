<?php
class Pywebdev_Autotag_Metabox_Main{
	
	public function __construct(){
		add_action('add_meta_boxes', array($this,'create'));
		
		add_action('admin_init', array($this,'admin_init'));
	}
	
	public function admin_init() {
		add_action('admin_enqueue_scripts', array($this,'add_js_file'));
	}
	
	public function add_js_file(){
		wp_register_script('autotag_metbox_main', PYWEBDEV_AUTOTAG_JS_URL . '/ajax2.js', array('jquery'),'1.0');
		wp_enqueue_script('autotag_metbox_main');
	}
	
	public function create(){
		add_action('admin_enqueue_scripts', array($this,'add_css_file'));
		//echo '<br/>' . __METHOD__;
		add_meta_box('pywebdev-autotag-mb-data', 'My Data', array($this,'display'),'post');
	}
	
	public function display($post){
		echo '<div id="tagchecklist"></div>';
		echo '<p>Add these tag to the box "tags" on the right column : <input class="button tagadd-right" value="Add" type="button"></p>';
	}
	
	public function my_ajaxtag(){
		$postVal = $_POST;
		
		$content = $postVal['content'];
		$content_new = $this->strip_word_html($content);
		$content_new = $this->strip_tags_content($content_new);
		$rake = new Rake(PYWEBDEV_AUTOTAG_DIR.'stoplist_smart.txt');
		$phrases = $rake->extract($content_new);
		
		$array = array();
		foreach ($phrases as $key => $val) {
			$phrase = preg_replace("/[^a-zA-Z 0-9]+/", "", $key );
			if ($phrase && $val > 3)
			$array[] = trim($phrase);
		}
		
		
		$title = $postVal['value'];
		
		$choose = $this->_setting_options['search_engine'];
		if ($choose == '')
		$choose = 'google';
		
		if ($title) {
			if ($choose == 'google') {
				//$array = array_merge($array,self::googleSearch($postVal['value']));
			}
			if ($choose == 'bing') {
				$array = array_merge($array,self::bingSearch($postVal['value']));
			}
			if ($choose == 'stack') {
				$array = array_merge($array,self::stackOverflow($postVal['value']));
			}
		}
		
		
		
		//$array = $this->getNewContent($array,$title);
		
		$array = array_merge($array,self::getTextBetweenTags($content,'h1'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h2'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h3'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h4'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h5'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h6'));
		$array = array_merge($array,self::getTextFromAnchor($content));
		$array = array_unique($array);
		$str = implode(', ',$array);
		
		$msg = array();
		if(count($array)){
			$msg['str'] = (count($array)?trim($str):'');
			$msg['title'] = $postVal['value'];
			//$msg['myvar'] = $myvar;
			$msg['status'] = true;
		}else{
			$msg['str'] = '';
			$msg['status'] = false;
			$msg['title'] = 'sbc';
		}
		
		echo json_encode($msg);
		
		die();
		
		$var = '<div class="tagchecklist">';
		$mang = array();
		foreach ($array as $k => $ele) {
			$mang[] = '<span><a id="post_tag-check-num-'.$k.'" class="ntdelbutton" tabindex="0">X</a>&nbsp;'.$ele.'</span>';
		}
		$var .= '</div>';
	}
}