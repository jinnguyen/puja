<?php 
ini_set('display_errors','On');
require '../src/Autoload.php';
$tpl = new Puja;
$tpl->template_dirs = array('templates/');
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->headers = array(
	'tpl_file'=>'variable.tpl',
	'php_file'=>highlight_file('variable.php',true),
);
$user = array(
	'name'=>'Puja',
	'age'=>20,
);
$data = array(
	'username'=>'Puja',
	'special_var'=>'age',
	'user'=>$user,
);
$tpl->parse('variable.tpl',$data);
?>