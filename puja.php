<?php
class CaoBox{
	var $name = 'CaoBox';
	var $version = '1.1';
	var $docs = 'http://caobox.com/document';
	var $error_display = true;
	var $error_log = false;
	var $error_log_path = '';
	var $magic_quote_gpc = true;// = get_magic_quotes_gpc();

	function __construct(){

	}


	function error_handle($error_report = E_ALL,$error_display = 'On', $error_log= false, $error_log_path=''){
		ini_set('display_errors','On');
		//error_reporting(0);
		//error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

		if(ini_get('magic_quotes_gpc')) ini_set('magic_quotes_gpc',false);
		if(ini_get('magic_quotes_runtime')) ini_set('magic_quotes_runtime',false);

		set_error_handler(array(&$this,'error_system'));

		$this->error_display = $error_display;
		$this->error_log = $error_log;
		$this->error_log_path = $error_log_path;
	}
	/*
	 function __set($name, $value){
	$trace = debug_backtrace();
	$error_msg = "Cannot setting value for \$$name to '$value'";
	$this->logger_system('CAOBOX',$error_msg,$trace[0]['file'],$trace[0]['line']);
	$this->getError($error_msg,$trace[0]['file'],$trace[0]['line']);
	}

	function __get($name){
	$trace = debug_backtrace();
	$error_msg = "Cannot get value of varrible :\$$name";
	$this->logger_system('CAOBOX',$error_msg,$trace[0]['file'],$trace[0]['line']);
	$this->getError($error_msg,$trace[0]['file'],$trace[0]['line']);
	}
	function __call($name, $arguments) {
	$trace = debug_backtrace();
	$error_msg = "Cannot use method :$name('".implode('\',\'',$arguments)."')";
	$this->logger_system('CAOBOX',$error_msg,$trace[0]['file'],$trace[0]['line']);
	$this->getError($error_msg,$trace[1]['file'],$trace[1]['line']);
	}*/



	/**  As of PHP 5.3.0  */
	/*    function __callStatic($name, $arguments) {
	 $trace = debug_backtrace();

	print_r($trace);
	$this->getError("Cannot use method :$name('".implode('\',\'',$arguments)."')",$trace[1]['file'],$trace[1]['line']);
	}
	*/
	function help(){
		$const = get_defined_constants(true);

		ob_start();
		echo '<br />';
		echo '------------Constants: --------------------<br />';
		foreach($const['user'] as $key=>$name){
			echo "$key => $name<br />";
		}

		$method = get_class_methods($this);
		echo '<br />';
		echo '-------------Methods:      ---------------<br />';
		print_r($method);


		$include = get_included_files();
		echo '<br />';
		echo '-------------Include Files ---------------<br />';
		print_r($include);

		$header = getallheaders();
		echo '<br />';
		echo '-------------Header:       ---------------<br />';
		print_r($header);

		$msg = ob_get_contents();
		ob_end_clean();
		$this->getError($msg);
	}

	function getError($error_msg,$file = NULL,$line = NULL,$ctx =  NULL){
		//$trace = debug_print_backtrace();
		$error_file = rtrim(dirname(__FILE__),'/').'/caobox/error.html';
		if(file_exists($error_file)) $error_skin = file_get_contents($error_file);
		else $error_skin  = '<title>'.$this->name.' '.$this->version.'</title><base href="http://phpbasic.com/"></base><pre>
		***********************************************************************************************************
		<u>General by</u>   : %s
		<u>Message</u>: <strong>%s</strong>
		<u>File</u>   : <strong>%s</strong>
		<u>Line</u>   : <strong>%d</strong>
		***********************************************************************************************************
		Document: '.$this->docs.'
		</pre>%s';


		if(!$file) $file = 'Undefined';

		$err = sprintf($error_skin,$this->name.' '.$this->version,preg_replace('/href=\'(.*?)\'/','href="\\1.php"',$error_msg),$file,$line,$ctx);
		//$trace = debug_backtrace();
		//print_r($trace);
		die($err);
	}


	function getAlert($msg,$file,$line,$ctx){
		if($this->error_display) echo ' - '.$msg." in file <strong>$file</strong> line <strong>$line</strong> <br />";

	}

	function saveLog($msg,$file_log = 'caobox'){
		$fp = @fopen($this->error_log_path.$file_log.'.txt','a+');
		$time = date('Y-m-d h:i:s');
		$msg = '['.microtime()."] $msg [$time]\n";
		if($fp){
			fwrite($fp,$msg);
			fclose($fp);
		}
	}

	function logger_system($error_no,$error_msg,$file,$line,$file_log = 'caobox.txt'){
		if(!$this->error_log) return false;
		$error_code[E_WARNING] = 'E_WARNING';
		$error_code[E_NOTICE] = 'E_NOTICE';
		$code = ($error_no == E_WARNING || $error_no == E_NOTICE)?$error_code[$error_no]:$error_no;
		$msg = "[$code] $error_msg file $file line $line";
		$this->saveLog($msg,'caobox');

	}

	function getLog($msg){
		$this->saveLog($msg,'user');
	}

	function error_system($error_no,$error_msg,$file,$line,$ctx){
		$error_msg = preg_replace('/href=\'(.*?)\'/i','href="http://php.net/$1" target="_blank"',$error_msg);
		$error_code[E_WARNING] = '<strong>'.$this->name.' '.$this->version.' Warning:</strong> ';
		$error_code[E_NOTICE] = '<strong>'.$this->name.' '.$this->version.' Notice:</strong> ';
		if($error_no == E_WARNING || $error_no == E_NOTICE) $this->getAlert($error_code[$error_no].$error_msg,$file,$line,$ctx);
		else	$this->getError($error_msg,$file,$line,$ctx);
		$this->logger_system($error_no,$error_msg,$file,$line);
	}



}
$caobox = new CaoBox();
$caobox->error_handle();
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
		require_once $include_dir.'src/filter.php';
		require_once $include_dir.'src/tags.php';
		require_once $include_dir.'src/debug.php';
		require_once $include_dir.'src/cache.php';
		require_once $include_dir.'src/compiler.php';
		
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