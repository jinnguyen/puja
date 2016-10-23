<?php
ini_set('display_errors','On');

function create_file($file,$content = ''){
	$fp = fopen($file,'w');
	if(!$fp){
		
		echo json_encode(array('status'=>false,'msg'=>"You must set chmod 777 for ".dirname($file) ));
		exit();
	}
	fwrite($fp,$content);
	fclose($fp);
}
function file_struct($dir, &$array_file){
	if ($dh = opendir($dir)){
		while (($file = readdir($dh)) !== false){
			if($file != '.' && $file != '..'){
				if(is_dir($dir.DIRECTORY_SEPARATOR.$file)){
					file_struct($dir.DIRECTORY_SEPARATOR.$file,$array_file);
				}else{
					$array_file[] =  $dir.DIRECTORY_SEPARATOR.$file;
				}

			}
				
		}
		closedir($dh);
	}

}

$target_folder = dirname(__FILE__);
if($_GET){
	$src = $_GET['src'];
	$folder = $target_folder.DIRECTORY_SEPARATOR.$_GET['folder'].DIRECTORY_SEPARATOR;
	$file_content = php_strip_whitespace($src);
	create_file($folder.'src'.DIRECTORY_SEPARATOR.basename($src),$file_content);
	//echo $src;
	exit();
}
if($_POST){
	$structures = array('src');
	$folder = $target_folder.DIRECTORY_SEPARATOR.$_POST['folder'].DIRECTORY_SEPARATOR;
	mkdir($target_folder.DIRECTORY_SEPARATOR.$_POST['folder'].DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR,777,true);
	
	$list_file = array();
	file_struct(dirname($target_folder).DIRECTORY_SEPARATOR.'src',$list_file);
	$list_file[] = dirname($target_folder).DIRECTORY_SEPARATOR.'src/Autoload.php';
	echo json_encode(array('status'=>true,'msg'=>'OK','list_file'=>$list_file,'folder'=>$_POST['folder']));
	//echo $folder;
	exit();
}
require '../src/Autoload.php';
$tpl = new Puja;
$tpl->template_dirs = array('templates/');
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