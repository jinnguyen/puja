<?php
ini_set('display_errors','On');

function create_file($file,$content = ''){
	$fp = @fopen($file,'w');
	if(!$fp){
		
		echo json_encode(array('status'=>false,'msg'=>"You must set chmod 777 for ".dirname($file) ));
		exit();
	}
	fwrite($fp,$content);
	fclose($fp);
}
$target_folder = dirname(__FILE__);
if($_POST){
	$structures = array('src');
	$folder = $target_folder.DIRECTORY_SEPARATOR.$_POST['folder'].DIRECTORY_SEPARATOR;
	create_file($folder.'puja.php');
	$list_file = array(
		'puja.php',
		'src/compiler.php',
		'debug.html',
	);
	echo json_encode(array('status'=>true,'msg'=>'OK','list_file'=>$list_file));
	//echo $folder;
	exit();
}
include '../puja.php';
$tpl = new Puja;
$tpl->template_dir = 'templates/';
$tpl->cache_dir = 'cache/';
$tpl->parse_executer = 'eval';
$tpl->headers = array(
	'tpl_file'=>'build_compress.tpl',
	'php_file'=>highlight_file('build_compress.php',true),
);
$data =  array(
	'target'=>$target_folder,
	'directory_separator'=>DIRECTORY_SEPARATOR,
);
$tpl->parse('build_compress.tpl',$data);
?>