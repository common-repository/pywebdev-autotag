<div class="pywebdev-suggestion">
	<form method="post" action="options.php" id="<?php echo $this->_menu_slug;?>" enctype="multipart/form-data">
		<?php //echo settings_fields($this->_menu_slug);
		
			$htmlObj = new PyWebDevHtml();
			//if ($args['name'] == 'title'){				
			//Tao phan tu title
			$inputID 	= $this->create_id('pywebdev_autotag_title');
			$inputName 	= $this->create_name('pywebdev_autotag_title');
			$inputValue = @$str;
			$arr 		= array('id' => $inputID,'rows'=>4, 'cols'=>140);
			$html 		= $htmlObj->label(translate('Tag')) . $htmlObj->textarea($inputName,$inputValue,$arr);
			echo $htmlObj->pTag($html);
			
			//Tao phan tu chua choose_suggestion		
			$inputID 	= $this->create_id('choose_suggestion');
			$inputName 	= $this->create_id('choose_suggestion');
			$inputValue = '';
			$arr = array('id' => $inputID);
			$options['data'] = array(
						'google' => translate('Google'),
						'bing' => translate('Bing'),
						'stack' => translate('Stack'),
					);	
			$html 		= $htmlObj->label(translate('Filter search')) . $htmlObj->selectbox($inputName,$inputValue,$arr,$options);
			echo $htmlObj->pTag($html);
		?>
		<?php //echo do_settings_sections($this->_menu_slug);?>
	</form>
	<script type="text/javascript">
	/*jQuery(document).ready(function($){
		/*$("#title").blur(function(e){
			
			var dataObj = {
					"action": "getTitle",
					"value": $(this).val(),
					"post_title": "<?php echo $str_title;?>"
				};
			console.log(dataObj);
			
			$.ajax({
				url		: ajaxurl,//admin-ajax.php?action=getTitle
				type	: "POST",
				data	: dataObj,
				dataType: "json",
				success	: function(data, status, jsXHR){
							console.log(data);
							if(data.status == false){
								$('.could-not').remove();
								$("#pywebdev_autotag_st_ajax_choose_suggestion")
									.after('<span class="could-not">Could not get items</span>');
							}else{
								$('.get-success').remove();
								$("#pywebdev_autotag_st_ajax_pywebdev_autotag_title").val(data.str);
								$("#pywebdev_autotag_st_ajax_choose_suggestion")
									.after('<span class="get-success">OK</span>');
							}
						}
			});
			
		});
		
		$("#pywebdev_autotag_st_ajax_choose_suggestion").change(function(e){
			
			var dataObj = {
					"action": "getTitle",
					"value": $(this).val(),
					"post_title": "<?php echo $str_title;?>"
				};
			console.log(dataObj);
			
			$.ajax({
				url		: ajaxurl,//admin-ajax.php?action=getTitle
				type	: "POST",
				data	: dataObj,
				dataType: "json",
				success	: function(data, status, jsXHR){
							console.log(data);
							if(data.status == false){
								$('.could-not').remove();
								$("#pywebdev_autotag_st_ajax_choose_suggestion")
									.after('<span class="could-not">Could not get items</span>');
							}else{
								$('.get-success').remove();
								$("#pywebdev_autotag_st_ajax_pywebdev_autotag_title").val(data.str);
								$("#pywebdev_autotag_st_ajax_choose_suggestion")
									.after('<span class="get-success">OK</span>');
							}
						}
			});
			
		});
	});*/
	</script>
</div>