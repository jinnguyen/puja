User's guide
========
<pre>
&lt;?php
  
  class Puja{
    /**
	 *  Folder includes template files 
	 *  @var string
	 *  */
	<strong>var $template_dir = 'templates/';</strong>
	/**
	 * Folder includes compiled files
	 * @var string
	 */
	<strong>var $cache_dir;</strong>
	/**
	 * Cache level
	 * 0: Default level. No cache
	 * 1: AUTO update each user modify template. REQUIRE configure $cache_dir
	 * 2: NOT update each user modify template, only update when user delete cached file manualy. REQUIRE configure $cache_dir.
	 * @var int
	 */
	<strong>var $cache_level;</strong>
	/**
	 * Type of template compile.
	 * - eval: call eval to compile AST. 
	 * - include: Default value. Create a PHP file from AST and then include it. REQUIRE configure $cache_dir.
	 * @var string
	 */
	<strong>var $parse_executer = 'include';</strong>
	/**
	 * Custom filter class
	 * @var Class object
	 */
	<strong>var $custom_filter;</strong>
	/**
	 * Custom tags class
	 * @var Class object
	 */
	<strong>var $custom_tags;</strong>
	/**
	 * Mode debug
	 * - if mode debug = true, enable validate template's syntax [DEVELOP]
	 * - if mode debug = false, disable validate template's syntax, [PRODUCTION]
	 * @var Boolean
	 */
	<strong>var $debug = false;</strong>
	/**
	 * Set common values for template before template parse.
	 * @var Array
	 */
	<strong>var $headers = array();</strong>
	/**
	 * Consider data is only array, not include object.
	 * - if true: Puja don't run object_to_array converter (save time )
	 * - if false: Puja run object_to_array converter.
	 * @var Boolean
	 */
	<strong>var $data_only_array  = false;</strong>
	/**
	 * Consider include multi level.
	 * true: Default value. Allow include multi level.
	 * false: only include 1 level. This option will make faster.
	 * */
	<strong>var $include_multi_level = true;</strong>
	/**
	 * Consider include multi extends.
	 * true: Default value. Allow extends multi level..
	 * false: only include 1 level. This option will make faster.
	 * */
	<strong>var $extends_multi_level = true;</strong>
    
    	/**
	 * Parse template 
	 * @param string $template_file
	 * @param array $data
	 * @param boonlean $return_value, display to browswer if $return_value = false, else return template string.
	 */
    	<strong>function parse($template_file,$data=array(),$return_value=false)</strong>{}
  }
  
</pre>
