<?php 
ini_set('display_errors','On');
date_default_timezone_set('Asia/Bangkok');
require '../src/Autoload.php';
$tpl = new Puja;
$tpl->template_dirs = array('templates/');
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->headers = array(
	'tpl_file'=>'filter.tpl',
	'php_file'=>highlight_file('filter.php',true),
);
$data = array(
	'name'=>'Puja',
	'date_join'=>'2016-10-23 14:08:51',
);
$tpl->parse('filter.tpl',$data);
?>