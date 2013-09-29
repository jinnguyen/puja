<?php 
class TemplateFilter{
	/* Conver a string to upper
	 * */
	
	function filter_main($val){
		return addslashes($val);
	}
	/**
	 * Filter abs
	 * {{ val|abs }}
	 * @param mixed $val
	 * @return abs number
	 */
	function filter_abs($val){
		if(!$val) return $val;
		if(is_numeric($val)){
			return abs($val);
		}
		return 0;
	}
	/**
	 * Filter capfirst
	 * {{ val|capfirst }}
	 * @param mixed $val
	 * @return string 
	 */
	function filter_capfirst($val,$arg = null){
		if(!$val) return $val;
		return ucfirst($val);
	}
	function filter_date($val,$arg='Y-m-d h:i:s'){
		if(is_numeric($val)) return date($arg, $val);
		return date($arg, strtotime($val));
	}
	function filter_default($val,$arg=null){
		if(!$val) return $arg;
		return $val;
	}
	function filter_escape($val){
		$replace_table = array(
			'&'=>'&amp;',
			'"'=>'&quot;',
			'\''=>'&#39;',
			'>'=>'&gt;',
			'<'=>'&lt;'		
		);
		return str_replace(array_keys($replace_table),$replace_table,$val);
	}
	function filter_escapejs($val, $arg = null){
		return str_replace("\n","\\\n",$val);
	}
	function filter_join($val,$arg = ''){
		if(is_array($val)) return implode($arg,$val);
		return $val;
	}
	function filter_keys($val,$arg = null){
		if(is_array($val)) return array_keys($val);
		return null;
	}
	function filter_length($val){
		if(is_array($val)) return count($val);
		return strlen($val);
	}
	function filter_lower($val,$arg = null){
		if(function_exists('mb_strtolower')) return mb_strtolower($val);
		return strtolower($val);
	}
	function filter_nl2br($val,$arg = null){
		if($arg === "") $arg = true;
		return nl2br($val,$arg);
	}
	function filter_pluralize($val,$arg=null){
		if(!$arg) $plualize_arr  = array('','s');
		else $plualize_arr = explode(',',$arg);
		if(!is_numeric($val)) return $plualize_arr[0];
		if(abs($val) <= 1) return $plualize_arr[0];
		return $plualize_arr[1];
	}
	//function filter_replace($val){}
	function filter_striptags($val,$arg=null){
		return strip_tags($val, $arg);
	}
	function filter_trim($val,$arg=null){
		if(!$arg) $arg = ' ';
		return trim($val, $arg);
	}
	function filter_truncatechars($val,$length = null){
		if(!$length || strlen($val) < $length) return $val;
		if(function_exists('mb_substr')){
			return mb_substr($val, 0, $length).'...';
		}
		$val = substr($val, 0, $length + 1);
		return substr($val, 0, strrpos($val, ' ')).'...';
	}
	function filter_truncatewords($val,$length= null){
		//str_word_count($string)
		$arr = str_word_count($val,1);
		if(!$length || count($arr) < $length) return $val;
		return implode(' ',array_slice($arr, 0, $length)).'...';
		//$arr = explode()
	}
	function filter_upper($val,$arg = null){
		if(function_exists('mb_strtoupper')) return mb_strtoupper($val);
		return strtoupper($val);
	}
	
	function filter_urlencode($val,$arg = null){
		return urlencode($val);
	}
	
	function filter_urldecode($val,$arg = null){
		return urldecode($val);
	}
	
	function filter_urltrunc($val, $length = null){
		if(!$length && $length < 2) return $val;
		$half_first = ceil($length/2);
		$half_last = $length - $half_first;
		return substr($val,0,$half_first).'...'.substr($val,-1 * $half_last);
	}
	function filter_wordwrap($val, $width = null){
		return wordwrap($val, $width);
	}
	function filter_yesno($val,$arg = ''){
		if(!$arg) $arr = array('yes','no');
		else $arr = explode(',',$arg);
		if($val) return $arr[0];
		return $arr[1];
	}
}
?>