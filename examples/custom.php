<?php 
ini_set('display_errors','On');
// custom filter
class CustomFilter{
	/* {{ {var}|url:{arg} }} */
	function filter_urlize($var, $arg = null){
		$var = strtolower($var);
		$var = str_replace(' ','-',$var);
		$var = substr($var, 0, $arg);
		return $var;
	}
	/* {{ {var}|ext }} */
	function filter_ext($var, $arg = null){
		return substr($var,-4);
	}
}
// custom tags
class CustomTags{
	/* {% css_tag {val} %} */
	function css_tag($val, $arg = null){
		return '&lt;link src="'.$val.'" /&gt;';
	}
	/* {% js_tag {val} %} */
	function js_tag($val, $arg = null){
		return '&lt;script src="'.$val.'?'.$arg.'"&gt;&lt;/script&gt;';
	}
}
require '../src/Autoload.php';
$tpl = new Puja;

$tpl->template_dirs = array('templates/');
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->debug = true;
$tpl->custom_filter = new CustomFilter;
$tpl->custom_tags = new CustomTags;
$tpl->headers = array(
	'tpl_file'=>'custom.tpl',
	'php_file'=>highlight_file('custom.php',true),
);
$data = array(
	'name'=>'puja is a template engine',
	'file_name'=>'/path/to/Autoload.php',
);
$tpl->parse('custom.tpl',$data);
?>