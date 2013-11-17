<?php
/**
 * Puja v1.0
 * @author jinnguyen
 * @link http://github.com/jinnguyen/puja
 * @license MIT
 * @example
 * include 'path/to/puja.php';<p>
 * $tpl = new Puja;<p>
 * $tpl->cache_dir = 'path/to/cache/dir';<p>
 * $data = array(<p>
 * 		'username'=>"Jin ",<p>
 * );<p>
 * $tpl->parse($data,'home.tpl');<p>
 * 
 * home.tpl file:<p>
 * Welcome {{ username }}<p>
 */

class Puja{
	/**
	 *  Folder includes template files 
	 *  @var string
	 *  */
	var $template_dir = 'templates/';
	/**
	 * Folder includes compiled files
	 * @var string
	 */
	var $cache_dir;
	/**
	 * Cache level<p>
	 * 0: Default level. No cache<p>
	 * 1: AUTO update each user modify template. REQUIRE configure $cache_dir
	 * 2: NOT update each user modify template, only update when user delete cached file manualy. REQUIRE configure $cache_dir.
	 * @var int
	 */
	var $cache_level;
	/**
	 * Type of template compile.
	 * - eval: call eval to compile AST. 
	 * - include: Default value. Create a PHP file from AST and then include it. REQUIRE configure $cache_dir.
	 * @var string
	 */
	var $parse_executer = 'include';
	/**
	 * Custom filter class
	 * @var Class object
	 */
	var $custom_filter;
	/**
	 * Custom tags class
	 * @var Class object
	 */
	var $custom_tags;
	/**
	 * Mode debug
	 * - if mode debug = true, enable validate template's syntax [DEVELOP]
	 * - if mode debug = false, disable validate template's syntax, [PRODUCTION]
	 * @var Boolean
	 */
	var $debug = false;
	/**
	 * Set common values for template before template parse.
	 * @var Array
	 */
	var $headers = array();
	/**
	 * Consider data is only array, not include object.
	 * - if true: Puja don't run object_to_array converter (save time )
	 * - if false: Puja run object_to_array converter.
	 * @var Boolean
	 */
	var $data_only_array  = false;
	/**
	 * Consider include multi level.
	 * true: Default value. Allow include multi level.
	 * false: only include 1 level. This option will make faster.
	 * */
	var $include_multi_level = true;
	/**
	 * Consider include multi extends.
	 * true: Default value. Allow extends multi level..
	 * false: only include 1 level. This option will make faster.
	 * */
	var $extends_multi_level = true;
	
	
	function __construct(){
		
	}
	/**
	 * Parse template 
	 * @param string $template_file
	 * @param array $data
	 * @param boonlean $return_value
	 */
	function parse($template_file,$template_data=array(),$return_value=false){
		$include_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
		require $include_dir.'src/filter.php';
		require $include_dir.'src/tags.php';
		require $include_dir.'src/debug.php';
		require $include_dir.'src/cache.php';
		require $include_dir.'src/compiler.php';
		
		$tpl = new PujaCompiler;
		$tpl->template_dir = $this->template_dir;
		$tpl->cache_dir = $this->cache_dir;
		$tpl->cache_level = $this->cache_level;
		$tpl->parse_executer = $this->parse_executer;
		$tpl->custom_filter = $this->custom_filter;
		$tpl->custom_tags = $this->custom_tags;
		$tpl->debug = $this->debug;
		$tpl->headers = $this->headers;
		$tpl->data_only_array = $this->data_only_array;
		$tpl->include_multi_level = $this->include_multi_level;
		$tpl->extends_multi_level = $this->extends_multi_level;
		$context = $tpl->parse($template_file, $template_data, true);
		if ($return_value){
			return $context;
		}
		echo $context;
	}
	
}
?>