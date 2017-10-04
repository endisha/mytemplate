<?php

include 'mytemplate-engine.php';

	$mytpl = new MyTemplate();

	$mytpl->app_dir    = 'http://localhost/mytemplate/1.3.2';
	$mytpl->cachetime   =  0; 
	$mytpl->php_compile = 'on'; 
	$mytpl->global_vars = 'off'; 
	$mytpl->Tempdir     = 'tpl/';
	$mytpl->cache     = 'cache/';


$names[] = [
	'name' => 'ahmed',
	'href' => 'http://developer.ly',
];



$mytpl->assign('title', 'Application title');

$mytpl->assign_r($names);

echo $mytpl->show('index');
