<?php
require_once PYWEBDEV_AUTOTAG_DIR . '/includes/support.php';
require_once PYWEBDEV_AUTOTAG_DIR . '/includes/GoogleCustomSearch.php';

class PyWebDevAutotagAdmin{
	private $_menu_slug = 'pywebdev-autotag-st-ajax';
	
	private $_option_name = 'pywebdev_autotag_st_ajax';
	
	private $_setting_options;
	
	private $_google;
	
	private $_rake;
	private $xyz;
	
	public function __construct(){		
		//
		$this->_setting_options = get_option($this->_option_name,array());
		
		//if ($this->_setting_options['SEARCH_ENGINE_ID'] == '') {
			//$this->_setting_options['SEARCH_ENGINE_ID'] = '015015782586980661673:icff3hth4_i';
		//}
		//if ($this->_setting_options['API_KEY'] == '') {
			//$this->_setting_options['API_KEY'] = 'AIzaSyAKt-gu-NP2xwFKJ22y-HiNEJxd58q_V3I';
		//}
		
		$this->stopwords_path = PYWEBDEV_AUTOTAG_DIR . 'stoplist_smart.txt';
		$this->stopwords_pattern = $this->build_stopwords_regex();
		
		//$this->_google = new iMarc\GoogleCustomSearch($this->_setting_options['SEARCH_ENGINE_ID'], $this->_setting_options['API_KEY']);
		
		add_action('admin_init', array($this,'admin_init'));
	}
	public function admin_init(){
		add_action('admin_enqueue_scripts', array($this,'add_js_file'));

		add_action('wp_ajax_getTitle', array($this,'getTitle'));
		add_action('wp_ajax_my_ajax', array($this,'my_ajax'));
		add_action('wp_ajax_my_ajax_content', array($this,'my_ajax_content'));
		
		add_filter('sanitize_title', array($this,'my_sanitize_title'), 10, 3);
	}
	
	public function add_js_file(){
		wp_register_script($this->_menu_slug, PYWEBDEV_AUTOTAG_JS_URL . '/ajax.js', array('jquery'),'1.0');
		wp_enqueue_script($this->_menu_slug);
		
		$array = array(
			'choose'	=> $this->_setting_options['search_engine'],
			'second'	=> $this->_setting_options['second']
		);
		wp_localize_script( $this->_menu_slug, 'scriptParams', $array );
	}
	
	private function create_id($val){
		return $this->_option_name . '_' . $val;
	}
	
	private function create_name($val){
		return $this->_option_name . '[' . $val . ']';
	}
	public function strip_tags_content($text, $tags = '', $invert = FALSE) {
		preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
		$tags = array_unique($tags[1]);

		if(is_array($tags) AND count($tags) > 0) {
			if($invert == FALSE) {
				return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
			}
			else {
			return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
			}
		} elseif($invert == FALSE) {
			return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
		}
		return $text;
	}
	public function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u><br>')
    {
        mb_regex_encoding('UTF-8');
        //replace MS special characters first
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $text = preg_replace($search, $replace, $text);
        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if(mb_stripos($text, '/*') !== FALSE){
            $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
        }
        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
        $text = strip_tags($text, $allowed_tags);
        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
        //strip out inline css and simplify style tags
        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        $text = preg_replace($search, $replace, $text);
        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/\<!--/u", $text, $matches);
        if($num_matches){
              $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
        }
        return $text;
    }
	public function my_ajax_content() {
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
		
		
		/*$title = $postVal['value'];
		
		$choose = $this->_setting_options['search_engine'];
		if ($choose == '')
		$choose = 'google';
		
		if ($title) {
			if ($choose == 'google') {
				$array = array_merge($array,self::googleSearch($postVal['value']));
			}
			if ($choose == 'bing') {
				$array = array_merge($array,self::bingSearch($postVal['value']));
			}
			if ($choose == 'stack') {
				$array = array_merge($array,self::stackOverflow($postVal['value']));
			}
		}*/
		
		
		
		//$array = $this->getNewContent($array,$title);
		
		$array = array_merge($array,self::getTextBetweenTags($content,'h1'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h2'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h3'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h4'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h5'));
		$array = array_merge($array,self::getTextBetweenTags($content,'h6'));
		$array = array_merge($array,self::getTextFromAnchor($content));
		$array = array_unique($array);
		
		$str = $str_hide = '';
		$span = '';
		$var = '';
		if (count($array)) {
			$var = '<div id="tagsdiv-post_tag2" class="postbox" style="overflow: hidden;">';
				$var .= "<script type='text/javascript'>
					jQuery(document).ready(function($){
						$('.tagadd2').click(function(){
							var str = new Array();
							$('#post_tag2 .tagchecklist span.tag-content tagname').each(function(){
								str.push($(this).text());
							});
							if (str.length) {
								str = str.join(', ');
								$('#new-tag-post_tag').val(str);
								
								$('.tagadd').click();
							}
						});
						$('#post_tag2 .tagchecklist span.tag-content').click(function(){
							$(this).remove();
						});
					});
				</script>";
				$var .= '<h2 class="hndle ui-sortable-handle"><span>Tags</span></h2>';
				$var .= '<div class="inside">';
				$var .= '<div class="tagsdiv" id="post_tag2">';
				$var .= '<div class="tagchecklist">';
				foreach ($array as $k => $ele) {
					$var .= '<span class="tag-content"><a id="post_tag-check-num-'.$k.'" class="ntdelbutton" tabindex="0">X</a>&nbsp;<tagname>'.$ele.'</tagname></span>';
				}
				$var .= '</div>';
				$var .= '<p>Add these tag to the box "tags" on the right column : <input class="button tagadd2" value="Add" type="button"></p>';
				$var .= '</div>';
				$var .= '</div>';
			$var .= '</div>';
			
			$span .= "<script type='text/javascript'>
				jQuery(document).ready(function($){
					$('.tagadd2').click(function(){
						var str = new Array();
						$('#post_tag2 .tagchecklist span.tag-content tagname').each(function(){
							str.push($(this).text());
						});
						if (str.length) {
							str = str.join(', ');
							$('#new-tag-post_tag').val(str);
							
							$('.tagadd').click();
						}
					});
					$('#post_tag2 .tagchecklist span.tag-content').click(function(){
						$(this).remove();
					});
				});
			</script>";
			foreach ($array as $key => $ele) {
				$span .= '<span class="tag-content"><a id="post_tag-check-num-'.$key.'" class="ntdelbutton" tabindex="0">X</a>&nbsp;<tagname>'.$ele.'</tagname></span>';
			}
			
			$str = $var;
		}
		
		//$str_hide = implode(', ',$array);
		
		$msg = array();
		if(count($array)){
			$msg['str'] = (count($array)?trim($str):'');
			$msg['span'] = (count($array)?trim($span):'');
			$msg['title'] = $postVal['value'];
			//$msg['myvar'] = $myvar;
			$msg['status'] = true;
		}else{
			$msg['str'] = '';
			$msg['span'] = '';
			$msg['status'] = false;
			$msg['title'] = 'sbc';
		}
		
		echo json_encode($msg);
		
		die();
	}
	public function split_phrase($phrase)
	{
		$words_temp = str_word_count($phrase, 1, '0123456789');
		$words = array();
		foreach ($words_temp as $w)
		{
			if ($w != '' and !(is_numeric($w)))
			{
				array_push($words, $w);
			}
		}
		return $words;
	}
	private function match_my_string($needle = 'to', $haystack = 'I go to school') {
		if (strpos($haystack, $needle) !== false) return true;
		else return false;
	}
	private function getNewContent($sentences,$title)
	{
		$phrases_arr = array();
		if (count($sentences)) {
			foreach ($sentences as $k => $s)
			{
				$words = self::split_phrase($s);
				foreach ($words as $e => $p)
				{
					$p = strtolower(trim($p));
					if ($p != '' && $this->match_my_string($p,$title) == true) {
						$phrases_arr[$k][] = $p;
					}
				}
				if (count($phrases_arr[$k])) {
					$phrases_arr[$k] = implode(' ',$phrases_arr[$k]);
				}
			}
		}
		return $phrases_arr;
	}
	public function getTitle(){
		$postVal = $_POST;
		$stack = array();//echo 'abc';
		
		$title = $postVal['value'];
		
		//$choose = $this->_setting_options['search_engine'];
		$choose = $postVal['search_engine'];
		if ($choose == '') {
			$choose = 'google';
		}
		
		$str = array();
		if ($choose == 'google') {
			$stack = array_merge($stack,self::googleSearch($postVal['post_title']));
		}
		if ($choose == 'bing') {
			$stack = array_merge($stack,self::bingSearch($postVal['post_title']));
		}
		if ($choose == 'stack') {
			$stack = array_merge($stack,self::stackOverflow($postVal['post_title']));
		}
		
		$msg = array();
		if(count($stack)>0){
			$str = array_unique($stack);
			$msg['str'] = implode(', ',$str);
			$msg['status'] = true;
			$msg['choose'] = $choose;
		}else{
			$msg['str'] = '';
			$msg['status'] = false;
		}
		
		echo json_encode($msg);
		
		die();
	}
	public function my_sanitize_title($title, $raw_title, $context) {
		//echo '<pre>';print_r($this->get_phrasek($raw_title));echo '</pre>';
		return $title;
	}
	public function my_ajax() {
		$postVal = $_POST;
		$str = $postVal['value'];
		
		$choose = $this->_setting_options['search_engine'];
		//$choose = $postVal['search_engine'];
		if ($choose == '') {
			$choose = 'google';
		}
		
		$stack = array();
		if ($choose == 'google') {
			$stack = array_merge($stack,self::googleSearch($postVal['value'], $postVal['limit']));
		}
		if ($choose == 'bing') {
			$stack = array_merge($stack,self::bingSearch($postVal['value'], $postVal['limit']));
		}
		if ($choose == 'stack') {
			$stack = array_merge($stack,self::stackOverflow($postVal['value'], $postVal['limit']));
		}
		//$stack = self::googleSearch($postVal['value'], $postVal['limit']);
		//$stack = array_merge($stack,self::bingSearch($title));
		//$stack = array_merge($stack,self::stackOverflow($title));
		$arr = array_unique($stack);
		
		$var = $span = '';
		if (count($arr)) {			
			$var = '<div id="tagsdiv-post_tag2" class="postbox" style="overflow: hidden;">';
				$var .= "<script type='text/javascript'>
					jQuery(document).ready(function($){
						$('.tagadd2').click(function(){
							var str = new Array();
							$('#post_tag2 .tagchecklist span.tag-title tagname').each(function(){
								str.push($(this).text());
							});
							if (str.length) {
								str = str.join(', ');
								$('#new-tag-post_tag').val(str);
								
								$('.tagadd').click();
							}
						});
						$('#post_tag2 .tagchecklist span.tag-title').click(function(){
							$(this).remove();
						});
					});
				</script>";
				$var .= '<h2 class="hndle ui-sortable-handle"><span>Tags</span></h2>';
				$var .= '<div class="inside">';
				$var .= '<div class="tagsdiv" id="post_tag2">';
				$var .= '<div class="tagchecklist">';
				foreach ($arr as $k => $ele) {
					$var .= '<span class="tag-title"><a id="post_tag-check-num-'.$k.'" class="ntdelbutton" tabindex="0">X</a>&nbsp;<tagname>'.$ele.'</tagname></span>';
				}
				$var .= '</div>';
				$var .= '<p>Add these tag to the box "tags" on the right column : <input class="button tagadd2" value="Add" type="button"></p>';
				$var .= '</div>';
				$var .= '</div>';
			$var .= '</div>';
			
			
			$span .= "<script type='text/javascript'>
				jQuery(document).ready(function($){
					$('.tagadd2').click(function(){
						var str = new Array();
						$('#post_tag2 .tagchecklist span.tag-title tagname').each(function(){
							str.push($(this).text());
						});
						if (str.length) {
							str = str.join(', ');
							$('#new-tag-post_tag').val(str);
							
							$('.tagadd').click();
						}
					});
					$('#post_tag2 .tagchecklist span.tag-title').click(function(){
						$(this).remove();
					});
				});
			</script>";
			foreach ($arr as $key => $ele) {
				$span .= '<span class="tag-title"><a id="post_tag-check-num-'.$key.'" class="ntdelbutton" tabindex="0">X</a>&nbsp;<tagname>'.$ele.'</tagname></span>';
			}
		}
		
		$str = implode(', ',$arr);
		
		//ob_start();
		//require_once PYWEBDEV_AUTOTAG_VIEWS_DIR . '/suggestion.php';
		//$myvar = ob_get_clean();
		
		$msg = array();
		if(count($arr)){
			$msg['str'] = (count($arr)?trim($var):'');
			$msg['span'] = (count($arr)?trim($span):'');
			$msg['title'] = $postVal['value'];
			//$msg['myvar'] = $myvar;
			$msg['status'] = true;
		}else{
			$msg['str'] = '';
			$msg['span'] = '';
			$msg['status'] = false;
			$msg['title'] = 'sbc';
		}
		
		echo json_encode($msg);
		
		die();
	}
	function googleSearch($title, $limit = 0) {
		$xyz = $this->get_phrasek($title);
		$count = count($xyz);
		$xyz = implode(' ',$xyz);
		$new = $this->limit_text($xyz,$limit);
		//$this->xyz = $new;
		if ($limit == 0) {
			$html = file_get_html('http://www.google.com/search?q='.urlencode($title));
		} else {
			if ($new === $xyz && $limit > $count) {return array();}
			$html = file_get_html('http://www.google.com/search?q='.urlencode($new));
		}
		$str = array();
		//if (isset($html->find('#center_col',0)) || isset($html->find('#brs',0))) {
		if ($html->find('#center_col',0)) {
			$html->find('#center_col #resultStats',0)->outertext = '';
			$html->find('#center_col #res',0)->outertext = '';
			$abc = $html->find('#center_col',0);
			if ($abc->find('._Bmc a')) {
				foreach ( $abc->find('._Bmc a') as $list){
					$text = $list->innertext;
					$text = strip_tags($text);
					$str[] = $text;
				}
				if (count($str)) {
					//return implode(', ',$str);
					return $str;
				}
			}

			//if ($this->match_my_string($new,$xyz)) {
				//$str = array_merge($str,self::googleSearch($title, $limit++));
			//}
		}

		return $str;
	}
	function googleSearchx($title, $limit = 3) {
		$xyz = $this->get_phrasek($title);
		$xyz = implode(' ',$xyz);
		$new = $this->limit_text($xyz,$limit);
		if ($limit == 3) {
			$html = file_get_html('http://www.google.com/search?q='.urlencode($title));
		} else {
			$html = file_get_html('http://www.google.com/search?q='.urlencode($new));
		}
		$str = array();
		//if (isset($html->find('#center_col',0)) || isset($html->find('#brs',0))) {
		if ($html->find('#center_col',0)) {
			$html->find('#center_col #resultStats',0)->outertext = '';
			$html->find('#center_col #res',0)->outertext = '';
			$abc = $html->find('#center_col',0);
			if ($abc->find('._Bmc a')) {
				foreach ( $abc->find('._Bmc a') as $list){
					$text = $list->innertext;
					$text = strip_tags($text);
					$str[] = $text;
				}
			}

			//if ($this->match_my_string($new,$xyz)) {
				//$str = array_merge($str,self::googleSearch($title, $limit++));
			//}
		}
		if (count($str)) {
			//return implode(', ',$str);
			return $str;
		}
		return $str;
	}
	function getTextBetweenTags($string, $tagname) {
		// Create DOM from string
		$html = str_get_html($string);

		$titles = array();
		// Find all tags 
		if ($html->find($tagname)) {
			foreach($html->find($tagname) as $element) {
				$titles[] = $element->plaintext;
			}
		}
		
		if (count($titles)) {
			//$titles = implode(', ',$titles);
			
			return $titles;
		} else {
			return array();
		}
	}
	function getTextFromAnchor($string) {
		// Create DOM from string
		$html = str_get_html($string);

		$titles = array();
		// Find all tags 
		if ($html->find('a')) {
			foreach($html->find('a') as $element) {
				if ($element->title) {
					$phrase = preg_replace("/[^a-zA-Z 0-9]+/", "", $element->title );
					$titles[] = $phrase;
				}
				if ($element->alt) {
					$alt = preg_replace("/[^a-zA-Z 0-9]+/", "", $element->alt );
					$titles[] = $alt;
				}
			}
		}
		
		if (count($titles)) {
			//$titles = implode(', ',$titles);
			
			return $titles;
		} else {
			return $array;
		}
	}
	
	function stackOverflow($title, $limit = 0) {
		$xyz = $this->get_phrasek($title);
		$count = count($xyz);
		$xyz = implode(' ',$xyz);
		$new = $this->limit_text($xyz,$limit);
		if ($limit == 0) {
			$html = file_get_contents('https://api.stackexchange.com/2.2/search/excerpts?q='.urlencode($title).'&site=stackoverflow');
		} else {
			if ($new === $xyz && $limit > $count) {return array();}
			$html = file_get_contents('https://api.stackexchange.com/2.2/search/excerpts?q='.urlencode($new).'&site=stackoverflow');
		}
		//$new = $this->limit_text($title,$limit);//echo $title;die;
		//$html = file_get_contents('https://api.stackexchange.com/2.2/search/excerpts?q='.urlencode($new).'&site=stackoverflow');
		$json = gzdecode($html);
		$json = json_decode($json,true);
		$str = array();
		if (isset($json['items'])) {
			foreach ( $json['items'] as $ele){
				$tags = $ele['tags'];
				$abc = array();
				foreach ($tags as $tag) {
					$abc[] = trim($tag);
				}
				$str = array_merge($str,$abc);
			}
			if (count($str)) {
				$str = array_unique($str);
				//return implode(', ',$str);
			}
			//if ($this->match_my_string($new,$title)) {
				//$str = array_merge($str,self::stackOverflow($title, $limit++));
			//}
		}

		return $str;
	}
	
	function bingSearch($title, $limit = 0) {
		$xyz = $this->get_phrasek($title);
		$count = count($xyz);
		$xyz = implode(' ',$xyz);
		$new = $this->limit_text($xyz,$limit);
		if ($limit == 0) {
			$html = file_get_html('http://www.bing.com/search?q='.urlencode($title));
		} else {
			if ($new === $xyz && $limit > $count) {return array();}
			$html = file_get_html('http://www.bing.com/search?q='.urlencode($new));
		}
		//$new = $this->limit_text($title,$limit);//echo $title;die;
		//$html = file_get_html('http://www.bing.com/search?q='.urlencode($new));
		$str = array();
		if ($html->find('#b_context .b_vList li')) {
			foreach ( $html->find('#b_context .b_vList li') as $list){
				$text = $list->find('a',0)->innertext;
				$text = strip_tags($text);
				$str[] = $text;
			}
			if (count($str)) {
				//return implode(', ',$str);
				return $str;
			}
			//if ($this->match_my_string($new,$title)) {
				//$str = array_merge($str,self::bingSearch($title, $limit++));
			//}
		}
		return $str;
	}
	private function get_phrasek($sentence)
	{
		$phrases_arr = array();
		$phrases_temp = preg_replace($this->stopwords_pattern, '', $sentence);
		$phrases = explode(' ', $phrases_temp);
		foreach ($phrases as $p)
		{
			$p = strtolower(trim($p));
			if ($p != '') array_push($phrases_arr, $p);
		}
		return $phrases_arr;
	}
	private function build_stopwords_regex()
	{
		$stopwords_arr = $this->load_stopwords();
		$stopwords_regex_arr = array();
		foreach ($stopwords_arr as $word)
		{
			array_push($stopwords_regex_arr, '\b'. $word. '\b');
		}
		return '/'. implode('|', $stopwords_regex_arr). '/i';
	}
	/**
	 * Load stop words from an input file
	 */
	private function load_stopwords()
	{
		$stopwords = array();
		if ($h = @fopen($this->stopwords_path, 'r'))
		{
			while (($line = fgets($h)) !== false)
			{
				$line = trim($line);
				if ($line[0] != '#')
				{
					array_push($stopwords, $line);
				}
			}
			return $stopwords;
		}
		else
		{
			echo 'Error: could not read file "'. $this->stopwords_path. '".';
			return false;
		}
	}
	public function limit_text($text, $limit = 3) {
		if (str_word_count($text, 0) > $limit) {
			$words = str_word_count($text, 2);
			$pos = array_keys($words);
			$text = substr($text, 0, $pos[$limit]);
		}
		return $text;
    }
	public function countChars($general_title, $chars) {
		$frequencies = array();
		$generals = $this->split_phrase($general_title);
		$words = $this->split_phrase($chars);
		$generals_count = count($generals);
		$words_count = count($words);
		foreach ($words as $word) {
			$frequencies[$word] = (isset($frequencies[$word]))? $frequencies[$word] : 0;
			$frequencies[$word] += 1;//tìm từ $word trong mảng $title
		}
		$count = 0;
		foreach ($frequencies as $word => $freq)
		{
			$count += $freq;		
		}
		$score = $count/$generals_count;
		
		if ($score)
		return $score;
		return false;
	}
}