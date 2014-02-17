<?php

class PujaException extends Exception{}
class PujaCompiler{
	var $template_dir = 'templates/';
	var $cache_dir;
	var $cache_level;
	var $parse_executer = 'include';
	var $custom_filter;
	var $custom_tags;
	var $debug = false;
	var $headers = array();
	var $data_only_array  = false;
	var $include_multi_level = true;
	var $extends_multi_level = true;
	
	var $_custom_filter;
	var $_custom_tags;
	var $_filter;
	var $_tags;
	var $_core_matches;
	var $_operators = array(' and ',' or ', ' not ',' in ',' is ', '%','!==','!=','>=','<=','===','==','<>','>','<','&&','||','!','+','-','*','/','=',';','__seperate__','__array_split__');
	var $_cache;
	var $_include_content = array();
	var $_mtime = 0; 
	var $_data = array();
	
	function __construct(){
		if(ini_get('magic_quotes_gpc')) ini_set('magic_quotes_gpc',false);
		if(ini_get('magic_quotes_runtime')) ini_set('magic_quotes_runtime',false);
	}
	/**
	 * Get template callback
	 * @param Array $matches
	 */
	function get_template_content_callback($matches){
		return isset($this->_data[$matches[1]])?$this->_data[$matches[1]]:null;
	}
	/**
	 * Get template content
	 * @param string $tpl_file
	 * @throws Exception
	 * @return string template content
	 * @todo: add instant variable {{ $skin }}
	 */
	function get_template_content($tpl_file){
		$tpl_file = $this->remove_quote($tpl_file);
		if(!file_exists($this->template_dir.$tpl_file)){
			throw new PujaException('Template <strong>'.$this->template_dir.$tpl_file.'</strong> doesn\'t exists!');
		}
		
		if($this->cache_level == 1){
			$mtime = filemtime($this->template_dir.$tpl_file);
			if($mtime > $this->_mtime) $this->_mtime = $mtime;
		}
		$content =  file_get_contents($this->template_dir.$tpl_file);
		$content = str_replace(array('\{#','\{$','\{{','\{%'),array('[:lpuja_comment:]','[:lpuja_specialvar:]','[:lpuja_variable:]','[:lpuja_percent:]'),$content);
		//remove template comment
		$content = preg_replace('/\{\#\s?(.*?)\s?\#\}/','',$content);
		// parse instant variable
		$content = preg_replace_callback('/\{\$\s*([a-z0-9\_]*?)\s*\$\}/i',array($this,"get_template_content_callback"),$content);
		
		if($this->debug){
			$template_debug = new TemplateDebug;
			$template_debug->operators = $this->_operators;
			$template_debug->content = $content;
			$template_debug->tpl_file = $this->template_dir.$tpl_file;
			$template_debug->valid_syntax();
		}
		return $content;
	}
	/**
	 * Parse extends block
	 * @param string $content
	 * @throws Exception
	 */
	function get_block_extends($content){
		preg_match('/\{\%\s*extends\s?(.*?)\s?\%\}/is', $content, $matches);
		if(count($matches) == 0) return $this->remove_remain_block($content);
		
		$extends_content =  $this->get_template_content($matches[1]);
		preg_match_all('/\{\%\s*block\s?([a-z0-9\_]*?)\s?\%\}(.*?)\{\%\s?endblock\s?\1?\s?\%\}/is',$content,$content_blocks);
		$content = $extends_content;
		if(count($content_blocks[1])){
			preg_match_all('/\{\%\s*block\s?('.implode('|',$content_blocks[1]).')\s?\%\}(.*?)\{\%\s?endblock\s?\1?\s?\%\}/is',$extends_content,$extends_content_blocks);
			$block_names = array();
			$block_names = array_flip($content_blocks[1]);
			if(count($extends_content_blocks[1])) foreach($extends_content_blocks[1] as $key=>$block_name){
				if(in_array($block_name,$content_blocks[1])){
					$extends_content_blocks[2][$key] = $content_blocks[0][$block_names[$block_name]];
				}
			}
			$content = str_replace($extends_content_blocks[0],$extends_content_blocks[2],$content);
		}
		if($this->extends_multi_level){
			preg_match_all('/\{\%\s*block\s?([a-z0-9\_]*?)\s?\%\}(.*?)\{\%\s?endblock\s?\1?\s?\%\}/is',$content,$content_blocks);
			if(count($content_blocks[1])) $content = $this->get_block_extends($content);
		}
		return $content;
	}
	
	function remove_remain_block($content){
		$content = preg_replace('/\{\%\s?block\s?(.*?)\s?\%\}/i','',$content);
		$content = preg_replace('/\{\%\s?endblock\s?(.*?)\s?\%\}/i','',$content);
		return $content;
	}
	
	/**
	 * Convert a object to array
	 * @param object $object
	 */
	function object2array(&$object){
		foreach($object as $key=>$arr){
			$is_object = is_object($arr);
			if($is_object || is_array($arr)){
				if($is_object) $object[$key] = (array)$arr;
				$this->object2array($object[$key]);
			}
		}
	}
	/**
	 * Compile normal variable
	 * @param string $var
	 * @param string $var_prefix
	 * @return string: php variable name
	 */
	function compile_variable($var,$var_prefix = '$',$default_value = 'null'){
		$var = stripcslashes($var);
		$var = str_replace(array(',','__puja_squote__','__puja_dquote__'),array(',$',"'","'"),trim($var));
		
		$isset = true;
		if(substr($var,0,14) == 'puja_no_isset_'){
			$var = substr($var,14);
			$isset = false;
		}
		//if(substr($var,0,14) == '__puja_simple_') return '$'.substr($var,14);
		
		if($var==='' || $var === null || is_numeric($var)) return $var;
		if(substr($var,0,1) == '"' || substr($var,0,1) == "'"){
			return "'".str_replace('__template_engine_dot_','.',substr($var,1,-1))."'";
		}
		
		$explode = explode('__template_engine_dot_',$var);
		$var = $explode[0];
		unset($explode[0]);
		if(count($explode)){
			$var .= '[\''.implode('\'][\'',$explode).'\']';
		}
		$var = $var_prefix.$var;
		return $isset?"(isset({$var})?{$var}:".$default_value.")":$var;
	}
	/**
	 * Parse variable
	 * @param string $var
	 * @param string $check_isset
	 * if $check_isset is set, it will add isset() into template 
	 * @throws Exception
	 */
	function compile_variable_filter($var){
		$var = trim($var);
		if(!$var) return $var;
		$prefix = '';
		$subfix = '';
		$default_value = 'null';
		if(substr($var,0,17) == '__operator_index_' && substr($var,-1)=='_'){
			$oparator_search = $this->_operators;
			$oparator_search[0] = '&&';
			$oparator_search[1] = '||';
			$oparator_search[2] = '!';
			$oparator_search[3] = '__in_array__';
			$oparator_search[4] = '===';
			return $oparator_search[intval(substr($var,17))];
		}
		
		if(substr($var,0,22) == '__template_engine_arg_' && substr($var,-1) == '_'){
			return "'".addslashes($this->_core_matches[4][intval(substr($var,22))])."'";
		}
		
		if(substr($var,0,18) == '__start_in_array__'){
			$prefix  = 'in_array(';
			$var = substr($var,18);
		}
		if(substr($var,0,12) == '__in_array__'){
			return ',';
		}
		
		if(substr($var,0,16) == '__end_in_array__'){
			$prefix = '';
			$var = substr($var,16);
			$subfix = ')';
			$default_value = 'array()';
		}
		
		$var = str_replace(array(' ','.','|',':'),array('','__template_engine_dot_', '&','='),$var);
		parse_str($var, $var_info);
		$arr_keys = array_keys($var_info);
		$var = $arr_keys[0];
		unset($var_info[$var]);
		
		$var = $prefix.$this->compile_variable(stripslashes($var),'$',$default_value).$subfix;
		
		if(count($var_info)) foreach($var_info as $filter=>$arg){
			if(substr($arg,0,22) == '__template_engine_arg_' && substr($arg,-1) == '_'){
				$arg = $this->_core_matches[4][intval(substr($arg,22))];
			}
			
			if($this->_custom_filter && in_array('filter_'.$filter, $this->_custom_filter['methods'])){
				$var = '$pujaCustomFilter->filter_'.$filter.'('.$var.',"'.$arg.'")';
			}elseif(in_array('filter_'.$filter,$this->_filter['methods'])){
				$var = '$pujaFilter->filter_'.$filter.'('.$var.',"'.$arg.'")';
			}else{
				throw new PujaException('Filter <strong>'.$filter.'</strong> was not defined');
			}
		}
		return $var;
	}
	
	/**
	 * Start compile template
	 * @param string $content
	 */
	function compile_start($content){
		$content = str_replace(array('\\','\''),array('\\\\','\\\''), $content);
		return $content;
	}
	
	/**
	 * Compile end 
	 * @param string $content
	 */
	function compile_end($content){
		$content = str_replace(array('[:lpuja_variable:]','[:lpuja_percent:]','[:lpuja_specialvar:]','[:lpuja_comment:]'),array('{{','{%','{$','{#'),$content);
		return $content;
	
	}
	
	/**
	 * Include tags
	 * incldue a template to a template
	 * @param string $content
	 */
	
	function get_block_include($content){
		preg_match_all('/\{\%\s?include\s+(.*?)\s+(.*?)\s?\%\}/i', $content, $include_matches);
		$include_replace = array();
		if(count($include_matches[0])){
			foreach($include_matches[1] as $key=>$val){
				//preg_replace
				if(!isset($this->_include_content[$val])){
					$this->_include_content[$val] = $this->get_template_content($val);
				}
				$check_set_key = trim($include_matches[2][$key]);
				$include_replace[$key] = $check_set_key?'{% before_include '.$include_matches[2][$key].' %}{% set '.$include_matches[2][$key].' %}':'';
				$include_replace[$key] .= $this->_include_content[$val];
				$include_replace[$key] .= $check_set_key?'{% after_include '.$include_matches[2][$key].' %}':'';
			}
			$content = str_replace($include_matches[0],$include_replace,$content);
			
			if($this->include_multi_level){
				preg_match_all('/\{\%\s?include\s+(.*?)\s+(.*?)\s?\%\}/i', $content, $include_matches);
				if(count($include_matches[0])) $content = $this->get_block_include($content);
			}
			
		}
		return $content;
	}
	
	
	/**
	 * Remove template comment {# .... #}
	 * @param string $content
	 * @return string template content without comment block
	 */
	function remove_template_comment($content){
		return preg_replace('/\{\#\s?(.*?)\s?\#\}/','',$content);
	}
	
	/**
	 * Compile in_array
	 * @param String $var
	 * @return unknown|string
	 */
	
	function compile_in_array($var){
		if(!strpos($var,'__in_array__')) return $var;
		$explode = explode('__in_array__',$var);
		return 'in_array('.$explode[0].','.str_replace(':null',':array()',$explode[1]).')';
	}
	
	/**
	 * Remove quote ( " or ' ) from template name
	 * @param string $string
	 * @return string: template name without quote
	 */
	function remove_quote($string){
		return str_replace(array('\'','"'),array('',''),trim($string));
	}
	
	/**
	 * Overite build_query
	 * @param array $formdata
	 * @param string $numeric_prefix
	 * @param string $arg_separtor
	 * @return string
	 */
	function build_query($formdata, $numeric_prefix=null,$arg_separtor='&'){
		if(!function_exists('http_build_query')){
			throw new PujaException('Puja requires http_build_query()');
		}
		return http_build_query($formdata,$numeric_prefix,$arg_separtor);
		
	}
	
	/**
	 * Compile variable before/after include.
	 * @param string $var_str
	 * @param string $type_include
	 * @return string
	 */
	function compile_include_variable($var_str, $type_include){
		$var_str = trim($var_str);
		//if(!$var_str) return null;
		
		if ($type_include == 'for'){
			return 'puja_no_isset_'.$var_str;
		}
		$var_str = str_replace(' ','&',$var_str);
		parse_str($var_str,$arr);
		$values = array_keys($arr);
		$keys = explode(' ','puja_before_include_'.implode(' puja_before_include_',$values));
		
		if($type_include == 'before_include'){
			$isset_keys = explode(' ','puja_no_isset_'.implode(' puja_no_isset_',$keys));
			$combine = array_combine($isset_keys, $values);
		}elseif($type_include == 'after_include'){
			$isset_keys = explode(' ','puja_no_isset_'.implode(' puja_no_isset_',$values));
			$combine = array_combine($isset_keys , $keys);
		}elseif($type_include == 'set'){
			$isset_keys = explode(' ','puja_no_isset_'.implode(' puja_no_isset_',$values));
			$combine = array_combine($isset_keys, $arr);
		}
		return $this->build_query($combine,null,';').'; ';
	}
	
	/**
	 * Parse template 
	 * Always call this function to execute template
	 * @param array $data
	 * @param string $tpl_file
	 * @param boolean $return_value
	 * @throws Exception
	 */
	function parse($tpl_file, $data,$return_value = false){
		
		if(!is_string($tpl_file)){
			throw new PujaException('Template file must be a string,given '.gettype($tpl_file));
		}
		
		if(!is_array($data)){
			throw new PujaException('Template data must be array,given '.gettype($data));
		}
		
		if(($this->cache_level || $this->parse_executer == 'include')){
			if(!$this->cache_dir){
				throw new PujaException('You must configure Puja::cache_dir to process');
			}
			if(!is_writable($this->cache_dir)){
				throw new PujaException('Require permission  to write  on folder '.$this->cache_dir.'');
			}	
		}
		
		$this->_cache = new TemplateCache;
		$this->_cache->dircache = $this->cache_dir;
		$this->_cache->level = $this->cache_level;
		
		if($this->headers && is_array($this->headers) && is_array($data)){
			$data = array_merge($this->headers, $data);
		}
		$this->_data = $data;
		if($this->data_only_array === false) $this->object2array($data);
		if($this->cache_level == 2){
			$cache = $this->_cache->get($tpl_file,0);//$this->_mtime = 0
			if($cache['valid']){
				extract($data);
				include $cache['file'];
				echo $ast_puja_template;
				return;
			}
		}
		
		$content = $this->get_template_content( $tpl_file);
		$content = $this->get_block_extends($content);
		$content = $this->get_block_include($content);

		$cache = $this->_cache->get($tpl_file,$this->_mtime);
		if($cache['valid']){
			extract($data);
			include $cache['file'];
			echo $ast_puja_template;
			return;
		}
		
		$this->_filter = array('name'=>'TemplateFilter', 'methods'=>get_class_methods('TemplateFilter'));
		$this->_tags = array('name'=>'TemplateTags', 'methods'=>get_class_methods('TemplateTags'));
		if($this->custom_filter){
			$this->_custom_filter = array('name'=> get_class($this->custom_filter), 'methods'=>get_class_methods($this->custom_filter));
		}
		if($this->custom_tags){
			$this->_custom_tags = array('name'=>get_class($this->custom_tags), 'methods'=>get_class_methods($this->custom_tags));
		}
		
		$content = $this->compile_start($content);
		$builtin_tags = array('before_include|after_include|empty|endfor|if|elseif|else|endif|set|print');
		preg_match_all('/\{\%\s*('.implode('|',$builtin_tags).')\s+(.*?)\s*\%\}/',$content, $matches);
		preg_match_all('/\{\%\s*for\s*([a-z0-9\_\,\s]*?)\s+in\s+([a-z0-9\.\_]*?)\s*\%\}/is',$content,$for_matches);
		preg_match_all('/\{\%\s*(get_file'.(isset($this->_custom_tags['methods'])?'|'.implode('|',$this->_custom_tags['methods']):'').')\s+(.*?)\s+(.*?)\s*\%\}/',$content, $include_matches);
		preg_match_all('/\{\{\s*([^\{\}]*?)\s*\}\}/',$content, $variable_matches);
		
		if(count($matches[2]) || count($variable_matches[1]) || count($include_matches[3]) || count($for_matches[1])){
			
			$seperate_array = array('__array_split__');
			$empty_array = array('__array_empty__');
			$structure_arr = array_merge(count($matches[2])?$matches[2]:$empty_array,$seperate_array,
										count($variable_matches[1])?$variable_matches[1]:$empty_array,$seperate_array,
										count($include_matches[3])?$include_matches[3]:$empty_array,$seperate_array,
										count($for_matches[1])?$for_matches[1]:$empty_array,$seperate_array,
										count($for_matches[2])?$for_matches[2]:$empty_array,$seperate_array);
			
			$structure_str = implode(' __seperate__ ', $structure_arr);
			$structure_str = str_replace(array('"','\\\''),array('__puja_dquote__','__puja_squote__'),$structure_str);
			preg_match_all('/([a-z0-9\_\.]+)([\=\:])(__puja_dquote__|__puja_squote__)(.*?)\3/i',$structure_str, $structure_matches);
			if(count($structure_matches[0])){
				$struct_quote_arg_replace = array();
				foreach($structure_matches[0] as $key=>$val){
					if($structure_matches[3][$key] == '__puja_squote__'){
						$structure_matches[4][$key] = str_replace(array('\\\\__puja_squote__','__puja_squote__','__puja_dquote__'), array("'","'",'"') ,$structure_matches[4][$key]);
					}elseif($structure_matches[3][$key] == '__puja_dquote__'){
						$structure_matches[4][$key] = str_replace(array('\\\\__puja_dquote__','__puja_squote__','__puja_dquote__'), array('\\"',"'",'\\"') ,$structure_matches[4][$key]);
					}
					$struct_quote_arg_replace[$key] = $structure_matches[1][$key].$structure_matches[2][$key].'__template_engine_arg_'.$key.'_';
				}
				$structure_str = str_replace($structure_matches[0],$struct_quote_arg_replace,$structure_str);
			}
			$structure_str = preg_replace('/\s+/', ' ', $structure_str);
			$structure_str = preg_replace('/\s*\=\s*/', '=', $structure_str);
			
			$_arr = explode(' __seperate__ __array_split__ __seperate__ ',$structure_str);
			$_arr_matches = array();
			
			//set,before_include,after_include
			$_arr_matches[0] = explode('__seperate__',$_arr[0]);
			$include_plus_arr = array();
			if(count($matches[1])) foreach($matches[1] as $key=>$val){
				if($val=='before_include' || $val == 'after_include' || $val == 'set'){
					$_arr_matches[0][$key] = $this->compile_include_variable($_arr_matches[0][$key],$val);
				}
			}
			$_arr[0] = implode(' __seperate__ ',$_arr_matches[0]);
			
			$_arr_matches[3] = explode('__seperate__',$_arr[3]);
			if(count($for_matches[1])) foreach($for_matches[1] as $key=>$val){
				$_arr_matches[3][$key] = $this->compile_include_variable($_arr_matches[3][$key],'for');
			}
			$_arr[3] = implode(' __seperate__ ',$_arr_matches[3]);
			
			if(isset($_arr[4])){
				$_arr_matches[4] = explode('__seperate__',$_arr[4]);
				if(count($for_matches[2])) foreach($for_matches[2] as $key=>$val){
					$_arr_matches[4][$key] = $this->compile_include_variable($_arr_matches[4][$key],'for');
				}
				$_arr[4] = implode(' __seperate__ ',$_arr_matches[4]);
			}
			
			$structure_str = implode(' __seperate__ __array_split__ __seperate__ ',$_arr);
			$this->_core_matches = $structure_matches;
			
			//preg_match_all('/([a-z0-9\_\.]+)[\=\:](.*?)\s/i',$structure_str, $argument_matches);
			
			$oparator_support_replace = array();
			foreach($this->_operators as $key=>$v){
				//$oparator_support_replace[$key] = '__xxx____operator_index_'.$key.'___xxx__';
				
				if($v == ' in '){
					$oparator_support_replace[$key] = '__xxx____in_array____xxx__';
				}else{
					$oparator_support_replace[$key] = '__xxx____operator_index_'.$key.'___xxx__';
				}
				
			}
			$structure_str = str_replace($this->_operators,$oparator_support_replace,$structure_str);
			$structure_split = explode('__xxx__', $structure_str);
			
			foreach($structure_split as  $key=>$var){
				if($var == '__in_array__'){
					$structure_split[$key-1] = '__start_in_array__'.$structure_split[$key-1];
					$structure_split[$key+1] = '__end_in_array__'.$structure_split[$key+1];
				}
			}
			foreach($structure_split as  $key=>$var){
				$structure_split[$key] = $this->compile_variable_filter($var);
			}
			$structure_str = implode('',$structure_split);
			$structure_by_tag = explode('__seperate____array_split____seperate__',$structure_str);
			
			foreach($structure_by_tag as $key=>$string){
				$structure_by_tag[$key] = explode('__seperate__',$string);
			}
			$matches[2] = $structure_by_tag[0];
			$matches_replace = array();
			
			if(count($matches[1])) foreach($matches[1] as $key=>$tag){
				switch($tag){
					case 'endfor':$matches_replace[$key] = '\';}} $ast_puja_template .= \''; break;
					case 'if':$matches_replace[$key] = '\'; if('.$matches[2][$key].'){ $ast_puja_template .= \'';break;
					case 'elseif':$matches_replace[$key] = '\'; }elseif('.$matches[2][$key].'){ $ast_puja_template .= \'';break;
					case 'else':$matches_replace[$key] = '\';} else { $ast_puja_template .= \''; break;
					case 'endif':$matches_replace[$key] = '\';} $ast_puja_template .= \''; break;
					case 'empty': $matches_replace[$key] = '\';}}else{if(true){ $ast_puja_template .= \'';break;
					case 'print':$matches_replace[$key] = '\';print_r('.$matches[2][$key].'); $ast_puja_template .= \'';break;
					case 'set': 
					case 'before_include': 
					case 'after_include': $matches_replace[$key] = '\';'.$matches[2][$key].' $ast_puja_template .= \'';break;
				}
			}
			
			$content = str_replace($matches[0],$matches_replace,$content);
			// variable
			$variable_matches[1] = $structure_by_tag[1];
			$variable_replace = array();
			if(count($variable_matches[1])) foreach($variable_matches[1] as $key=>$val){
				$variable_replace[$key] = '\'; $ast_puja_template .= '.$val.'; $ast_puja_template .= \'';
			}
			$content = str_replace($variable_matches[0],$variable_replace,$content);
			
			// for 
			
			$for_matches[1] = $structure_by_tag[3];
			$for_matches[2] = $structure_by_tag[4];
			
			$for_replace = array();
			if(count($for_matches[0])) foreach($for_matches[0] as $key=>$tag){
				$for_replace[$key] = '\'; if(isset('.$for_matches[2][$key].') && count('.$for_matches[2][$key].')){ foreach('.$for_matches[2][$key].' as '.str_replace(',','=>',$for_matches[1][$key]).'){ $ast_puja_template .= \'';
			}
			$content = str_replace($for_matches[0],$for_replace,$content);
			
		}
		
		if(count($include_matches[1])) foreach($include_matches[1] as $key=>$tag){
			if($tag == 'get_file'){
				$var = 'file_get_contents(\''.$this->template_dir.$include_matches[2][$key].'\')';
				if(trim($include_matches[3][$key]) == 'escape') $var = 'htmlentities('.$var.')';
			}elseif($this->_custom_tags && in_array($tag, $this->_custom_tags['methods'])){
				$var = '$pujaCustomTags->'.$tag.'("'.$include_matches[2][$key].'","'.$include_matches[3][$key].'")';
			}elseif($this->_tags && in_array($tag,$this->_tags['methods'])){
				$var = '$pujaTags->'.$tag.'("'.$include_matches[2][$key].'","'.$include_matches[3][$key].'")';
			}else{
				throw new PujaException('Tag <strong>'.$tag.'</strong> was not defined');
			}
			$include_matches[1][$key] = '\'; $ast_puja_template .= '.$var.';$ast_puja_template .= \'';
		}
		
		$content = str_replace($include_matches[0],$include_matches[1],$content);
		$content = $this->compile_end($content);
		
		extract($data);
		$new_class = '$pujaFilter = new '.$this->_filter['name'].';$pujaTags = new '.$this->_tags['name'].';';
		if($this->_custom_filter['name']) $new_class .= '$pujaCustomFilter = new '.$this->_custom_filter['name'].';';
		if($this->_custom_tags['name']) $new_class .= '$pujaCustomTags = new '.$this->_custom_tags['name'].';';
		
		$cache_file_content = '<?php '.$new_class.'  $ast_puja_template = \''.$content.'\';';
		if($this->parse_executer == 'eval'){
			$parse_error = true;
			@eval($new_class.'$ast_puja_template = \''.$content.'\';$parse_error=false;');
			if($parse_error){
				highlight_string($cache_file_content);
			}
			if($this->cache_level){ // > 0
				$this->_cache->set($cache['file'], $cache_file_content);
			}
		}else{
			$this->_cache->set($cache['file'], $cache_file_content);
			require  $cache['file'];
		}
		if($return_value) return $ast_puja_template;
		echo $ast_puja_template;
	}
}
?>
