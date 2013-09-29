<?php 
ini_set('display_errors','On');
date_default_timezone_set('Asia/Bangkok');
include '../puja.php';
$tpl = new Puja;
$tpl->template_dir = 'templates/';
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->headers = array(
	'tpl_file'=>'filter.tpl',
	'php_file'=>highlight_file('filter.php',true),
);
$data = array(
	'name'=>'Puja',
	'date_join'=>date('Y-m-d H:i:s'),
);
$tpl->parse('filter.tpl',$data);
?>