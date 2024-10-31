<?php
class Pywebdev_Autotag_Mb_Data{
	
	public function __construct(){
		add_action('add_meta_boxes', array($this,'create'));
		
		add_action('save_post', array($this,'save'));
	}
	
	public function create(){
		add_action('admin_enqueue_scripts', array($this,'add_css_file'));
		//echo '<br/>' . __METHOD__;
		add_meta_box('zend-mp-mb-data', 'My Data', array($this,'display'),'post');
	}
	
	public function save($post_id){
		
		/* echo '<pre>';
		print_r($post_id);
		echo '</pre>';
		echo '<pre>';
		print_r($_POST);
		echo '</pre>'; */
		$postVal = $_POST;
		update_post_meta($post_id, '_zend_mp_mb_data_price', 
						sanitize_text_field($postVal['zend-mp-mb-data-price']));
		update_post_meta($post_id, '_zend_mp_mb_data_author', 
						sanitize_text_field($postVal['zend-mp-mb-data-author']));
		update_post_meta($post_id, '_zend_mp_mb_data_level', 
						sanitize_text_field($postVal['zend-mp-mb-data-level']));
		update_post_meta($post_id, '_zend_mp_mb_data_profile', 
						strip_tags($postVal['zend-mp-mb-data-profile']));
		//die();
	}
	
	public function display($post){
		/* echo '<pre>';
		print_r($post);
		echo '</pre>'; */
		echo '<div class="zendvn-mb-wrap">';
		echo '<p><b><i>' . translate('Xin vui lòng nhập đầy đủ thông tin vào các ô sau') . ':</i></b></p>';
		
		$htmlObj = new ZendvnHtml();
		
		//Tao phan tu chua Price
		$inputID 	= 'zend-mp-mb-data-price';
		$inputName 	= 'zend-mp-mb-data-price';
		$inputValue = @get_post_meta($post->ID,'_zend_mp_mb_data_price',true);
		$arr = array('size' =>'25','id' => $inputID);
		echo '<p><label for="' . $inputID . '">' . translate('Price') . ':</label>'
				. $htmlObj->textbox($inputName,$inputValue,$arr)
				. '</p>';
		

		//Tao phan tu chua Author
		$inputID 	= 'zend-mp-mb-data-author';
		$inputName 	= 'zend-mp-mb-data-author';
		$inputValue = @get_post_meta($post->ID,'_zend_mp_mb_data_author',true);
		$arr = array('size' =>'25','id' => $inputID);
		echo '<p><label for="' . $inputID . '">' . translate('Author') . ':</label>'
				. $htmlObj->textbox($inputName,$inputValue,$arr)
				. '</p>';
		
		//Tao phan tu chua Level
		$inputID 	= 'zend-mp-mb-data-level';
		$inputName 	= 'zend-mp-mb-data-level';
		$inputValue = @get_post_meta($post->ID,'_zend_mp_mb_data_level',true);;
		$arr = array('id' => $inputID);
		$options['data'] = array(
					'beginner' => translate('Beginner'),
					'intermediate' => translate('Intermediate'),
					'advanced' => translate('Advanced'),
				);
		echo '<p><label for="' . $inputID . '">' . translate('Level') . ':</label>'
				. $htmlObj->selectbox($inputName,$inputValue,$arr,$options)
				. '</p>';
		
		//Tao phan tu chua Level
		$inputID 	= 'zend-mp-mb-data-profile';
		$inputName 	= 'zend-mp-mb-data-profile';
		$inputValue = @get_post_meta($post->ID,'_zend_mp_mb_data_profile',true);;
		$arr 		= array('id' => $inputID,'rows'=>6, 'cols'=>60);
		echo '<p><label for="' . $inputID . '">' . translate('Author profile') . ':</label>'
				. $htmlObj->textarea($inputName,$inputValue,$arr)
				. '</p>';
		echo '</div>';
	}
	
	public function add_css_file(){
		wp_register_style('zendvn_mp_mb_data', ZENDVN_MP_CSS_URL . '/mb-data.css', array(),'1.0');
		wp_enqueue_style('zendvn_mp_mb_data');
	}
}








