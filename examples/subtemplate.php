<?php 
ini_set('display_errors','On');
require '../src/Autoload.php';
$tpl = new Puja;
$tpl->template_dirs = array('templates/');
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->headers = array(
	'tpl_file'=>'subtemplate.tpl',
	'php_file'=>highlight_file('subtemplate.php',true),
);
$data = array(
	'username'=>'Puja'
);
$tpl->parse('subtemplate.tpl',$data);
?>