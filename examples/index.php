<?php 
ini_set('display_errors','On');
require '../src/Autoload.php';
$tpl = new Puja;
$tpl->template_dirs = array('templates/');
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->debug = true;
$tpl->headers = array(
	'tpl_file'=>'index.tpl',
	'php_file'=>highlight_file('index.php',true),
);
$tpl->parse('index.tpl');
?>