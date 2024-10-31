<?php

class PyWebDevHtml{
	
	public function __construct($options = null){
		
	}
	
	public function pTag($value = '', $attr = array(), $options = null){
		$strAttr = '';
		if(count($attr)> 0){
			foreach ($attr as $key => $val){
				if($key != "type" && $key != 'value'){
					$strAttr .= ' ' . $key . '="' . $val . '" ';
				}
			}
		}
		
		return '<p ' . $strAttr .' >' . $value . '</p>';
	}
	
	public function label($value = '',$attr = array(), $options = null){
		$strAttr = '';
		if(count($attr)> 0){
			foreach ($attr as $key => $val){
				if($key != "type" && $key != 'value'){
					$strAttr .= ' ' . $key . '="' . $val . '" ';
				}
			}
		}
		return '<label ' . $strAttr . ' >' . $value . ':</label>';
	}
	
	//Phần tử TEXTBOX
	public function textbox($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlTextbox.php';		
		return HtmlTextbox::create($name, $value, $attr, $options);
	}	
	
	//Phần tử FILEUPLOAD
	public function fileupload($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlFileupload.php';
		return HtmlFileupload::create($name, $value, $attr, $options);
	}
	
	//Phần tử PASSWORD
	public function password($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlPassword.php';
		return HtmlPassword::create($name, $value, $attr, $options);
	}
	
	//Phần tử HIDDEN
	public function hidden($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlHidden.php';
		return HtmlHidden::create($name, $value, $attr, $options);
	}

	//Phần tử BUTTON - SUBMIT - RESET
	public function button($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlButton.php';
		return HtmlButton::create($name, $value, $attr, $options);
	}
	
	//Phần tử TEXTAREA
	public function textarea($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlTextarea.php';
		return HtmlTextarea::create($name, $value, $attr, $options);
	}
	
	//Phần tử RADIO
	public function radio($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlRadio.php';
		return HtmlRadio::create($name, $value, $attr, $options);
	}
	
	//Phần tử CHECKBOX
	public function checkbox($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlCheckbox.php';
		return HtmlCheckbox::create($name, $value, $attr, $options);
	}
		
	//Phần tử SELECTBOX
	public function selectbox($name = '', $value = '', $attr = array(), $options = null){
		require_once PYWEBDEV_AUTOTAG_INCLUDES_DIR . '/html/HtmlSelectbox.php';
		return HtmlSelectbox::create($name, $value, $attr, $options);
	}
	
}