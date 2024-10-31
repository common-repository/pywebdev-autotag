<?php
class PyWebDev_AutoTag_Setting{
	
	//
	private $_menu_slug = 'pywebdev-autotag-st-ajax';
	
	private $_option_name = 'pywebdev_autotag_st_ajax';
	
	private $_setting_options;
	
	public function __construct(){
		//echo "<br/>" . __METHOD__;
		

		$this->_setting_options = get_option($this->_option_name,array());
		
		add_action('admin_menu', array($this,'settingMenu'));
		
		add_action('admin_init', array($this,'register_setting_and_fields'));
		
		
	}

	public function register_setting_and_fields(){		
		add_action('wp_ajax_pywebdev_check_form', array($this,'pywebdev_check_form'));
		
		register_setting($this->_menu_slug,$this->_option_name, array($this,'validate_setting'));
	
		//MAIN SETTING
		$mainSection = 'pywebdev_autotag_main_section';
		add_settings_section($mainSection, "Main setting",
					array($this,'main_section_view'), $this->_menu_slug);
	
		add_settings_field($this->create_id('second'), 'Enter seconds that it will extract keyword each time when you change on content post', array($this,'create_form'),
						$this->_menu_slug,$mainSection,array('name'=>'second'));
	
		add_settings_field($this->create_id('search_engine'), 'Choose default search engine', array($this,'create_form'),
						$this->_menu_slug,$mainSection,array('name'=>'search_engine'));
	
		
	}
	
	public function pywebdev_check_form(){
		$postVal = $_POST;
		$errors = array();
		
		if(!empty($postVal['value'])){
			if($this->stringMaxValidate($postVal['value'], 20) == false){
				$errors['pywebdev_autotag_st_ajax_title'] = "Chuoi dai qua 20 ky tu";
			}
		}
		
		$msg = array();
		if(count($errors)>0){
			$msg['status'] = false;
			$msg['errors'] = $errors;
		}else{
			$msg['status'] = true;
		}
		
		echo json_encode($msg);
		/* echo '<pre>';
		print_r($msg);
		echo '</pre>'; */
		
		die();
	}
	

	public function create_form($args){
	
		$htmlObj = new PywebdevHtml();
		if($args['name']== 'second'){
			$inputID 	= $this->create_id('second');
			$inputName 	= $this->create_name('second');
			$inputValue = @$this->_setting_options['second'];
			$arr 		= array('size' =>'25','id' => $inputID);
			$html 		= $htmlObj->textbox($inputName,$inputValue,$arr)
			. $htmlObj->pTag('Enter seconds',array('class'=>'description'));
			echo $html;
		}
		if($args['name']== 'search_engine'){
			$inputID 	= $this->create_id('search_engine');
			$inputName 	= $this->create_name('search_engine');
			$inputValue = @$this->_setting_options['search_engine'];
			$arr 		= array('id' => $inputID);
			$options['data'] = array(
				'google'=> translate('Google'),
				'bing'  => translate('Bing'),
				'stack' => translate('Stack'),
			);
			$html 		= $htmlObj->selectbox($inputName,$inputValue,$arr,$options);
			echo $html;
		}
	
	}
	
	public function validate_setting($data_input){
	
		//Mang chua cac thong bao loi cua form
		$errors = array();
	
		if($this->stringMaxValidate($data_input['second'], 20) == false){
			$errors['second'] = "Sencond: not empty";
		}

	
		if(count($errors)>0){
			$data_input = $this->_setting_options;
			$strErrors = '';
			foreach ($errors as $key => $val){
				$strErrors .= $val . '<br/>';
			}
				
			add_settings_error($this->_menu_slug, 'my-setting', $strErrors,'error');
		}else{
			add_settings_error($this->_menu_slug, 'my-setting', 'Update setting successful','updated');
		}
		//die();
		return $data_input; 
	}
	

	//===============================================
	//Kiem tra chieu chieu dai cua chuoi
	//===============================================
	private function stringMaxValidate($val, $max){
		$flag = false;
	
		$str = trim($val);
		if(strlen($str) <= $max){
			$flag = true;
		}
	
		return $flag;
	}
	
	//===============================================
	//Kiem tra phần mở rộng của file
	//===============================================
	private function fileExtionsValidate($file_name, $file_type){
		$flag = false;
	
		$pattern = '/^.*\.('. strtolower($file_type) . ')$/i'; //$file_type = JPG|PNG|GIF
		if(preg_match($pattern, strtolower($file_name)) == 1){
			$flag = true;
		}
	
		return $flag;
	}
	
	
	private function create_id($val){
		return $this->_option_name . '_' . $val;
	}
	
	private function create_name($val){
		return $this->_option_name . '[' . $val . ']';
	}


	public function main_section_view(){
	
	}
	

	public function settingMenu(){
	
		add_menu_page( 
				'Autotag Setting',
				'Autotag Setting',
				'manage_options',
				$this->_menu_slug,
				array($this,'display')
			);
	}
	

	public function display(){
		require_once PYWEBDEV_AUTOTAG_VIEWS_DIR . '/setting-page.php';
	}
	
}