<?php 
ini_set('display_errors','On');
require '../src/Autoload.php';
$tpl = new Puja;
$tpl->template_dirs = array('templates/');
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->headers = array(
	'tpl_file'=>'structure.tpl',
	'php_file'=>highlight_file('structure.php',true),
);


$cat_list = array(
	array(
		'name'=>'Cat 1',
		'news'=>array(array('name'=>'1.1'),array('name'=>'1.2'))
	),
	array(
		'name'=>'Cat 2',
	)
);

$data = array(
		'var'=>1,
		'array'=>array(1,2,3),
		'cat_list'=>$cat_list,
);
$tpl->parse('structure.tpl',$data);

?>