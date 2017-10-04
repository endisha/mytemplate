# MyTemplate Engine 1.3.2 
PHP Template Engine 
###### last change was : 10:16 pm 2008/2/17

Authors:

- Founder : Mohamed Endisha , mohamed.endisha@gmail.com

- Developer : saanina , saanina@gmail.com 

```
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
```

###### Copyrights 2007 - 2017 Bruce & Saanina