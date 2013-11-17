<?php 
/**
 * Puja Debug Mode<p>
 * Should anable on dev and disable in production<p>
 * 
 * 
 * */
class TemplateDebug{
	
	var $content;
	var $tpl_file;
	var $operators;
	var $nodelist = array();
	
	var $debug_html;
	var $error = array(
		// should change to code: missing_enblock, unexpect_endblock,....
		'undefined'=>'undefined',
		'multi_extends'=>"'extends' cannot appear more than once in the same template",
		'extends_multi_file'=>"'extends' takes one argument",
		'wrong_syntax'=>'wrong syntax',
		'missing_end_block'=>'Missing end block %}',
		'missing_end_variable'=>'missing end block }}',
		'missing_enblock'=>'missing {% endblock %} for %s',
		'unexpect_endblock'=>'unexpect {% endblock %} for %s',
		'missing_endfor'=>'missing {% endfor %}',
		'unexpect_endfor'=>'unexpect {% endfor %}',
		'missing_endif'=>'missing {% endif %}',
		'missing_endif'=>'unexpect {% endif %}',
		'wrong_php_variable'=>'Wrong PHP variable name',
		'wrong_operator'=>'Wrong operator in begin',
		//'wrong_operator_in_end'=>'Wrong operator end'
		
	);
	function __construct(){
		
	}
	function valid_operator(){
		preg_match_all('/\{\%\s*(if|elseif|set)\s+(.*?)\s*\%\}/',$this->content, $matches);
		preg_match_all('/\{\{\s*([^\{\}]*?)\s*\}\}/',$this->content, $variable_matches);
		
		if(count($matches[2]) || count($variable_matches[1])){
			$seperate_array = array('__array_split__');
			$empty_array = array('__array_empty__');
			$structure_arr = array_merge(count($matches[2])?$matches[2]:$empty_array,$seperate_array,
					count($variable_matches[1])?$variable_matches[1]:$empty_array);
			$structure_str = implode(' __seperate__ ', $structure_arr);
			$structure_str = ' '.str_replace(array('"','\''),array('__puja_dquote__','__puja_squote__'),$structure_str).' ';
			
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
			
			$oparator_support_replace = array();
			foreach($this->operators as $key=>$v){
				//$oparator_support_replace[$key] = '__xxx____operator_index_'.$key.'___xxx__';
			
				if($v == ' in '){
					$oparator_support_replace[$key] = '__xxx____in_array____xxx__';
				}else{
					$oparator_support_replace[$key] = '__xxx____operator_index_'.$key.'___xxx__';
				}
			
			}
			
			$structure_str = str_replace($this->operators,$oparator_support_replace,$structure_str);
			
			$structure_split = explode('__xxx__', $structure_str);
			//print_r($structure_split);
			foreach($structure_split as  $key=>$var){
				if($var == '__in_array__'){
					if(!trim($structure_split[$key-1])){
						$this->trace_bug_operator('wrong_operator', $structure_split[$key-1].' in '.$structure_split[$key+1]);
					}
					if(!trim($structure_split[$key+1])){
						$this->trace_bug_operator('wrong_operator', $structure_split[$key-1].' in '.$structure_split[$key+1]);
					}
					$structure_split[$key-1] = '__start_in_array__'.$structure_split[$key-1];
					$structure_split[$key+1] = '__end_in_array__'.$structure_split[$key+1];
				}
			}
			
		}
	}
	function valid_syntax(){
		$this->content = preg_replace('/\{\#\s*(.*?)\s*\#\}/','',$this->content);
		preg_match_all('/\{\%\s*([a-z0-9]*?)\s(.*?)(\{\%|\n)/', $this->content, $matches);
		
		if($matches[0]){
			$index_all = 0;
			foreach($matches[0] as $index=>$tag){
				$this->nodelist['all']['tag'][$index_all] = array('name'=>$tag,'index'=>$index_all,'arg'=>$matches[2][$index]);
				$this->nodelist['all']['tag_full'][$index_all] = $matches[0][$index];
				$index_all++;
				$tag = str_replace("\n","",trim($tag));
				if(!strpos($tag, '%}')){
					$this->trace_bug('all',$index,'missing_end_block');
				}
			}
		}
		
		preg_match_all('/\{\{\s([a-z0-9\|\.]*?)\s(.*?)(\{\{|\n)/', $this->content, $matches);
		//print_r($matches);
		if($matches[0]){
			$index_all = 0;
			foreach($matches[0] as $index=>$tag){
				$this->nodelist['variable_end_tag']['tag'][$index_all] = array('name'=>$tag,'index'=>$index_all,'arg'=>$matches[2][$index]);
				$this->nodelist['variable_end_tag']['tag_full'][$index_all] = $matches[0][$index];
				$index_all++;
				$tag = str_replace("\n","",$tag);
				if(!strpos($tag, '}}')){
					$this->trace_bug('variable_end_tag',$index,'missing_end_variable');
				}
			}
		}
		
		preg_match_all('/\{\{\s*(.*?)\s*\}\}/',$this->content, $matches);
		if($matches[0]){
			$index_all = 0;
			foreach($matches[0] as $index=>$tag){
				$this->nodelist['variable']['tag'][$index_all] = array('name'=>$tag,'index'=>$index_all,'arg'=>$matches[1][$index]);
				$this->nodelist['variable']['tag_full'][$index_all] = $matches[0][$index];
				$index_all++;
			}
		}
		
		
		preg_match_all('/\{\%\s*([a-z0-9]*?)\s(.*?)\%\}/i', $this->content, $matches);
		if(count($matches[1])){
			$index_extends = 0;
			$index_if = 0;
			$index_for = 0;
			$index_block = 0;
			foreach($matches[1] as $index=>$tag){
				$tag = trim($tag);
				
				// extends
				if(in_array($tag, array('extends'))){
					$this->nodelist['extends']['tag'][$index_extends] = array('name'=>$tag,'index'=>$index_extends,'arg'=>$matches[2][$index]);
					$this->nodelist['extends']['tag_full'][$index_extends] = $matches[0][$index];
					//$this->nodelist['extends']['arg'][$index_extends] = $matches[2][$index];
					$index_extends++;
				}
				// for
				if(in_array($tag, array('for','endfor','empty'))){
					$this->nodelist['for']['tag'][$index_for] = array('name'=>$tag,'index'=>$index_for,'arg'=>$matches[2][$index]);
					$this->nodelist['for']['tag_full'][$index_for] = $matches[0][$index];
					//$this->nodelist['for']['arg'][$index_for] = $matches[2][$index];
					$index_for++;
				}
				
				// if
				if(in_array($tag, array('if','elseif','else','endif'))){
					$this->nodelist['if']['tag'][$index_if] = array('name'=>$tag,'index'=>$index_if,'arg'=>$matches[2][$index]);
					$this->nodelist['if']['tag_full'][$index_if] = $matches[0][$index];
					//$this->nodelist['if']['arg'][$index_if] = $matches[2][$index];
					$index_if++;
				}
				
				// block
				if(in_array($tag, array('block','endblock'))){
					$this->nodelist['block']['tag'][$index_block] = array('name'=>$tag,'index'=>$index_block,'arg'=>$matches[2][$index]);
					$this->nodelist['block']['tag_full'][$index_block] = $matches[0][$index];
					//$this->nodelist['block']['arg'][$index_block] = $matches[2][$index];
					$index_block++;
				}
				
				
				
			}
		}
		$this->validate_extends();
		$this->validate_block();
		$this->validate_variable();
		$this->validate_if();
		$this->validate_for();
		$this->valid_operator();
	}
	
	function validate_variable(){
		if(!isset($this->nodelist['variable'])) return;
		foreach($this->nodelist['variable']['tag'] as $index=>$tag){
			$first_char = ord(substr($tag['arg'],0,1));
			$first_cond = $first_char == 95 || ($first_char >= 65 && $first_char <= 90) ||  ($first_char >= 97 && $first_char <= 122);
			if(!$first_cond){
				$this->trace_bug('variable',$index,'wrong_php_variable');
			}
			
			$last_char = substr($tag['arg'],-1);
			if($last_char == '.'){
				$this->trace_bug('variable',$index,'wrong_php_variable');
			}
			
			if(strpos($tag['arg'],'..')){
				$this->trace_bug('variable',$index,'wrong_php_variable');
			}
			
		}
	}
	//function trace_bug_operator()
	function trace_bug($block, $index = 0, $error_code = 0){
		$data_replace = array();
		$data_search = array();
		foreach($this->nodelist[$block]['tag_full'] as $key=>$val){
			$val = str_replace("\n","",$val);
			$data_search[$key] = $val;
			$data_replace[$key] = '<strong '.($index == $key?'class="__template__engine_current_line__ current_bug"':'').'>'.$val.'</strong>';
		}
		
		$this->print_trace_bug($data_search,$data_replace,$error_code,$this->nodelist[$block]['tag'][$index]['arg']);
		
	}
	
	function trace_bug_operator($error_code, $operator_string){
		$data_replace = array('<strong class="__template__engine_current_line__ current_bug">'.$operator_string.'</strong>');
		$data_search = array($operator_string);
		$this->print_trace_bug($data_search, $data_replace, $error_code);
		//echo $error_code.' '.$operator_string;
	}
	function print_trace_bug($data_search,$data_replace,$error_code,$tag_name){
		
		if(!$this->debug_html){
			$this->debug_html = file_get_contents('debug.html',true);
		}
		
		$content = htmlentities($this->content,NULL,'utf-8');
		$content = str_replace($data_search, $data_replace, $content);
		$lines = explode("\n",$content);
		$bug_content = '';
		$current_line = 1;
		foreach($lines as $line=>$row){
			if($current_line == 1 && strpos($row, '__template__engine_current_line__')) $current_line = $line+1;
			$bug_content .= '<li class="line_'.($line+1).'">'.$row."</li>";
		}
		
		if(!in_array($error_code,array_keys($this->error))) $error_code = 'undefined';
		
		
		if(strpos($this->error[$error_code],'%s')){
			$error_message = str_replace('%s',$tag_name,$this->error[$error_code]);
			
		}else{
			$error_message = $this->error[$error_code];
		}
		echo str_replace(array('{{tpl_file}}','{{error_body}}','{{line}}','{{error_message}}'),array($this->tpl_file,$bug_content,$current_line,$error_message),$this->debug_html);
		exit();
	}
	
	function validate_extends(){
		if(!isset($this->nodelist['extends'])) return true;
		$len = count($this->nodelist['extends']['tag']);
		if($len > 1){
			$this->trace_bug('extends',$len-1,'multi_extends');
		}
		
		foreach($this->nodelist['extends']['tag'] as $index=>$tag){
			$arg = trim($tag['arg']);
			if(strpos($arg,' ')) $this->trace_bug('extends',$index,'extends_multi_file');
		}
	}
	
	
	function validate_if_start_end($array_if){
		$len = count($array_if);
		$first_syntax = $array_if[0]['name'] == 'endif' || $array_if[0]['name'] == 'else'|| $array_if[0]['name'] == 'elseif';
		$last_syntax = $array_if[$len-1]['name'] == 'if' || $array_if[$len-1]['name'] == 'elseif' || $array_if[$len-1]['name'] == 'else';
		
		if($first_syntax || $last_syntax){
			if($first_syntax) $this->trace_bug('if',0,'unexpect_endif');
			if($last_syntax) $this->trace_bug('if',$array_if[$len - 1]['index'],'missing_endif');
		}
	}
	/**
	 * Check template syntax: IF ELSEIF ELSE ENDIF
	 */
	function validate_if(){
		//print_r($this->nodelist);
		if(!isset($this->nodelist['if'])) return;
		if(count($this->nodelist['if']['tag']) == 0) return true;
		$this->validate_if_start_end($this->nodelist['if']['tag']);
		$if_levels = array();
		foreach($this->nodelist['if']['tag'] as $index=>$tag){
			$len = count($if_levels);
			if($tag['name'] == 'if'){
				if(trim($tag['arg'])=='') $this->trace_bug('if',$index,'wrong_syntax');
				$if_levels[] = array($tag);
			}elseif($tag['name'] == 'elseif' || $tag['name'] == 'else'){
				if($tag['name'] == 'elseif' && trim($tag['arg'])=='') $this->trace_bug('if',$index,'wrong_syntax');
				if($tag['name'] == 'else' && trim($tag['arg'])) $this->trace_bug('if',$index,'wrong_syntax');
				$sub_len = count($if_levels[$len-1]);
				if($if_levels[$len-1][$sub_len - 1]['name'] == 'else'){
					$this->trace_bug('if',$index);
				}else{
					$if_levels[$len-1][] = $tag;
				}			
			}elseif($tag['name'] == 'endif'){
				if(trim($tag['arg'])) $this->trace_bug('if',$index,'wrong_syntax');
				$if_levels[$len - 1][] = $tag;
				unset($if_levels[$len - 1]);
				if(count($if_levels) == 0) $if_levels = array();
			}
		}
		//print_r($if_levels);
		if(count($if_levels)){
			foreach($if_levels as $k=>$array_if) $this->validate_if_start_end($array_if);
		}
	}
	function validate_for_start_end($array){
		$len = count($array);
		$first_syntax = $array[0]['name'] == 'endfor' || $array[0] == 'empty';
		$last_syntax = $array[$len-1]['name'] == 'for' || $array[$len-1]['name'] == 'empty';
		if($first_syntax || $last_syntax){
			//echo '---';
			if($first_syntax) $this->trace_bug('for',0,'unexpect_endfor');
			if($last_syntax) $this->trace_bug('for',$array[$len - 1]['index'],'missing_endfor');
		}
	}
	function validate_for_string($str,$index = 0){
		if(!preg_match('/([a-z0-9\_\,]+?)\sin\s?([a-z0-9\.\_]+?)/', $str)){
			$this->trace_bug('for',$index,'wrong_syntax');
		}
	}
	function validate_for(){
		if(!isset($this->nodelist['for'])) return true;
		$len = count($this->nodelist['for']['tag']);
		if(!$len) return true;
		//print_r($this->nodelist['for']['tag']);
		$this->validate_for_start_end($this->nodelist['for']['tag']);
		$for_levels = array();
		$current_level = -1;
		foreach($this->nodelist['for']['tag'] as $index=>$tag){
			$len = count($for_levels);
			if($tag['name'] == 'for'){
				$current_level += 1;
				if(trim($tag['arg'])=='') $this->trace_bug('for',$index,'wrong_syntax');
				$this->validate_for_string($tag['arg']);
				$for_levels[$current_level] = array($tag);
			}elseif($tag['name'] == 'empty'){
				if(trim($tag['arg'])) $this->trace_bug('for',$index,'wrong_syntax');
				$sub_len = count($for_levels[$current_level]);
				if($for_levels[$current_level][$sub_len - 1]['name'] == 'empty'){
					$this->trace_bug('for',$index);
				}else{
					$for_levels[$current_level][] = $tag;
				}
			}elseif($tag['name'] == 'endfor'){
				if(trim($tag['arg'])) $this->trace_bug('for',$index,'wrong_syntax');
				$for_levels[$current_level][] = $tag;
				unset($for_levels[$current_level]);
				$current_level -= 1;
				//if(count($for_levels) == 0) $for_levels = array();
			}
		}
		//print_r($for_levels);
		if(count($for_levels)){
			foreach($for_levels as $k=>$array) $this->validate_for_start_end($array);
		}
	}
	
	
	function validate_block_start_end($array){
		$len = count($array);
		$first_syntax = $array[0]['name'] == 'endblock';
		$last_syntax = $array[$len-1]['name'] == 'block';
		if($first_syntax || $last_syntax){
			if($first_syntax) $this->trace_bug('block',0, 'unexpect_endblock');
			if($last_syntax) $this->trace_bug('block',$array[$len - 1]['index'], 'missing_enblock');
		}
	}
	function validate_block(){
		if(!isset($this->nodelist['block'])) return true;
		$len = count($this->nodelist['block']['tag']);
		if(!$len) return true;
		$this->validate_block_start_end($this->nodelist['block']['tag']);
		$levels = array();
		$current_level = -1;
		foreach($this->nodelist['block']['tag'] as $index=>$tag){
			
			if($tag['name'] == 'block'){
				$current_level += 1;
				if(trim($tag['arg'])=='') $this->trace_bug('block',$index,'wrong_syntax');
				if(strpos(trim($tag['arg']),' ')) $this->trace_bug('block',$index,'wrong_syntax');
				$levels[$current_level] = $tag['arg'];
			}elseif($tag['name'] == 'endblock'){
				if(trim($tag['arg']) && trim($tag['arg'] != $levels[$current_level])) $this->trace_bug('block',$index, 'wrong_syntax');
				
				if(trim($tag['arg']) == '' || $tag['arg'] == $levels[$current_level]){
					unset($levels[$current_level]);
					$current_level -= 1;
				}else{
					$this->trace_bug('block',$index,'unexpect_endblock');
				}
			}
		}
		//print_r($levels);
		if(count($levels)){
			foreach($levels as $k=>$array) $this->validate_block_start_end($array);
		}
	}
	
}

?>