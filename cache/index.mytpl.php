<!DOCTYPE html>
<html>
<head>
	<title><?php print $this->vars['title'];?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->assets('js/style.css'); ?>">
</head>
<body>
	
	<?php $this->assign('names', $GLOBALS['names']); foreach($this->vars['names']  as $key=>$value){?>
		<a href="<?php print isset($value['href'])? $value['href'] : $this->vars['href'];?>"><?php print isset($value['name'])? $value['name'] : $this->vars['name'];?></a>
	<?php }/*endloop*/?>

</body>
</html>




