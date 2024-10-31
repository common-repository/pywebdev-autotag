jQuery(document).ready(function($){
	var chooses = scriptParams.choose;
	
	$("input[name=post_title]").blur(function() {
		var title = $(this).val();
		
		var interval = 4000;
		function doAjax(limit = 0) {
			$.ajax({
				url		: ajaxurl,//admin-ajax.php?action=my_ajax
				type	: "POST",
				data	: {
							"action": "my_ajax",
							"limit": limit,
							"value": title
						},
				dataType: "json",
				success	: function(data, status, jsXHR){
							console.log(data);
							if(data.status == false){
							}else{
								if (limit == 0) {
									//$('#new-tag-post_tag').val(data.str);
								} else {
									$('#new-tag-post_tag').val(function(index, val) {
										//return val + data.str;
									});
								}
								if ($('#tagsdiv-post_tag2').length > 0) {
									if (limit == 0) {
										$('#post_tag2 .tagchecklist span.tag-title').remove();
									}
									$('#post_tag2 .tagchecklist').append(data.span);
								} else {
									$('#normal-sortables').before(data.str);
								}
								//$('.tagadd').click();
								if (data.str !== '' && limit < 40) {
									limit++;
									setTimeout(doAjax, interval, limit);
								} else {
									//
								}
							}
						}
			});
		}
		
		doAjax();
	});
	
	function get_tinymce_content(id) {
		var content;
		var inputid = id;
		var editor = tinyMCE.get(inputid);
		var textArea = jQuery('textarea#' + inputid);    
		if (textArea.length>0 && textArea.is(':visible')) {
			content = textArea.val();        
		} else {
			content = editor.getContent();
		}    
		return content;
	}
	function get_tinymce_contentx(id) {

		if (jQuery("#wp-"+id+"-wrap").hasClass("tmce-active")){
			return tinyMCE.get(id).getContent();
		}else{
			return jQuery("#"+id).val();
		}

	}
	
	function checkChange() {
		for (var i = 0; i < tinymce.editors.length; i++) {
			tinymce.editors[i].onChange.add(function (ed, e) {
				var title = $("#title").val();
				var value = tinyMCE.activeEditor.getContent();
				
				$.ajax({
					url		: ajaxurl,//admin-ajax.php?action=my_ajax
					type	: "POST",
					data	: {
								"action": "my_ajax_content",
								"value": title,
								"content": value
							},
					dataType: "json",
					success	: function(data, status, jsXHR){
								console.log(data);
								if(data.status == false){
									//
								}else{
									//$('#tagsdiv-post_tag2').remove();
									//$('#post-body-content').after(data.str);
									if ($('#tagsdiv-post_tag2').length > 0) {
										$('#post_tag2 .tagchecklist span.tag-content').remove();
										$('#post_tag2 .tagchecklist').append(data.span);
									} else {
										$('#normal-sortables').before(data.str);
									}
									
									//$('#new-tag-post_tag').val(function(index, val) {
										//return data.str;
									//});
								}
							}
				});
			});
		}		
	}
	
	var seconds = scriptParams.second;

	// Was needed a timeout since RTE is not initialized when this code run.
	setTimeout(function () {
		checkChange();
	}, seconds);
	
	//choose
	$("#pywebdev_autotag_st_ajax_choose_suggestion").change(function(e){
		
		var dataObj = {
				"action": "getTitle",
				"value": $(this).val(),
				"post_title": $("#title").val()
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
							$("#pywebdev_autotag_st_ajax_choose_suggestion")
								.after('<span>Could not get items</span>');
						}else{
							$("#pywebdev_autotag_st_ajax_pywebdev_autotag_title").val(data.str);
							$("#pywebdev_autotag_st_ajax_choose_suggestion")
								.after('<span>OK</span>');
						}
					}
		});
		
	});
});