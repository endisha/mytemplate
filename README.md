# MyTemplate Engine 1.3.2 
Author[Founder] : Mohamed Endisha 
Author[Develoepr] : saanina




<?php
include 'mytemplate-engine.php';

	$mytpl = new MyTemplate();

	$mytpl->app_dir    = 'http://localhost/your-app';
	$mytpl->cachetime   =  0; 
	$mytpl->php_compile = 'on'; 
	$mytpl->global_vars = 'off'; 
	$mytpl->Tempdir     = 'tpl/';
	$mytpl->cache     = 'cache/';

$names[] = [
	'name' => 'Mohamed Endisha',
	'href' => 'http://developer.ly',
];

$mytpl->assign('title', 'Application title');
$mytpl->assign_r($names);
echo $mytpl->show('index');
?>


## Copyrights 2007 - 2017
-Bruce ->  Mytemplate engine founder
-Saanina -> Mytemplate engine developers