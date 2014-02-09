<?php 
ini_set('display_errors','On');
include '../puja.php';
$tpl = new Puja;
$tpl->template_dir = 'templates/';
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->debug = true;
$tpl->headers = array(
	
);
$tpl->parse('template-syntax-error.tpl');
?>