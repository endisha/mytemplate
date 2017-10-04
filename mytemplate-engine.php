<?php 
/*##############################################
*  Mytemplate Engine version 1.3.2
*  Authors: 
*   	1- Bruce [ founder]  http://developer.ly || E-mail:mohamed.endisha@gmail.com
*  		2- Saanina [develper] http://moffed.com ||  saanina@gmail.com 
*  
*###############################################
*   last change  reconstruct class 2017/10/04
*   last change  was : 10:16 pm 2008/2/17
*
*   last edit by : Bruce..
*	fix when print global value inside loop
*	add assets js & css files
****/
   
class MyTemplate{ 

   public $Tempdir		= '/tpl/'; 
   public $app_dir		= '';
   public $assets_dir	= 'public/assets/';
   public $cachedir		= '/cache/'; 
   public $cachetime	= 0; //by hour  
   public $php_tags   	= 'on';
   public $php_compile	= 'on';
   public $global_vars	= 'off';
   private $vars		= array();
   
   function __construct(){
  		 $this->Tempdir = __DIR__ . '/' . $this->Tempdir . '/';
  		 $this->cachedir = __DIR__ . '/' . $this->cachedir . '/'; 
   }

	/**
	* Remove any PHP tags that do not belong, these regular expressions are derived from
	* the ones that exist in zend_language_scanner.l
	* access : privat ..
	*/

	function remove_php_tags(&$code) {

				// This matches the information gathered from the internal PHP lexer
				$match = array(
					'#<([\?%])=?.*?\1>#s',
					'#<script\s+language\s*=\s*(["\']?)php\1\s*>.*?</script\s*>#s',
					'#<\?php(?:\r\n?|[ \n\t]).*?\?>#s'
				);

			$code = preg_replace($match, '', $code);
	}
	
	/**
	* compile our codes to be real code ..
	* accses : private..
	*/
	
    private function compile($content) {  //was display function here
	
			//loop 
			$content = preg_replace_callback('#{loop[^>]([a-zA-Z0-9\_\-\+\./]+)(.*?)\}(.*?){\/loop\}#is',array(&$this,'loop_bt'), $content);
			//if and her sisters
			$content = preg_replace_callback('#{(if|elseif)[^>]([a-zA-Z0-9\_\-\+\./]+)[^>]([a-zA-Z\=\!\>\%\&\|\</]{1,3})[^>](.*?)\}#i',array(&$this,'if_expr'), $content);

			//matchs ..
			$matchs	= array(
								'#{include_tpl[^>](.*?)\}#i',
								'#{include_script[^>](.*?)\}#i',
								'#{if[^>]([a-zA-Z0-9\_\-\+\./]+)\}#i',
								'#{elseif[^>]([a-zA-Z0-9\_\-\+\./]+)\}#i',
								'#{else\}#i',
								'#{\/if\}#i',
								($this->php_compile == 'on') ? '#{php\}(.*?){\/php\}#is' : '##',
								($this->global_vars == 'on') ? '#{([a-zA-Z0-9\_\-\+\./]+)}#' : '##',
								'#{assets[^>]([a-zA-Z0-9\_\-\+\./]+)}#is',
							);
			//replaces				
			$replaces = array ( 
									'<?php $this->include_tpl(\'\\1\'); ?>',
									'<?php include(\'\\1\'); ?>',
									'<?php if($this->vars[\'\\1\']){ ?>',
									'<?php } elseif(\\1){?>',
									'<?php } else { ?>',
									'<?php } ?>',
									($this->php_compile == 'on') ? '<?php \\1 ?>' : '',
									($this->global_vars == 'on') ? '<?php print $this->vars[\'\\1\'];?>' : '',
									'<?php echo $this->assets(\'\\1\'); ?>',
								);
		//show time ..
		$content = preg_replace($matchs,$replaces, $content);
		
		if($this->global_vars != 'on'){ 
			$content = preg_replace_callback('#{([a-zA-Z0-9\_\-\+\./]+)}#',array(&$this,'assign_if_global_off'), $content);
		}
		
		return $content; 
    } 
	
	/**
	* assign variables ... i mean make copy from variable to use it in our template ..
	* we have 2 type , singal or array ..
	* access : public ..
	**/
	public function assign($name, $value){
		$this->vars[$name] = $value; 
	}
	
	//for array
    public function assign_r($array){ 
    	foreach($array as $key=>$value){
    		$this->assign($key, $value); 
    	} 
    }
	
    private function assets($file){
    		return $this->app_dir.'/'.$this->assets_dir.$file;
   	}

	/**
	* we make this method to make your template more limited , just variables which you make it ..
	* access : private ..
	**/
	private function assign_if_global_off($match){

		if(isset($this->vars[trim($match[1])])){
			return '<?php print $this->vars[\'' . $match[1] . '\'];?>';
		}else{
			return "{" . $match[1] . "}";
		}
		
	}
	
	/*
	*including another template in current one ,,, its prety and helpful .. ;)
	* access : public ..
	*/
	private function include_tpl($tpl_page){
	
		print $this->show($tpl_page);
	}
	
	/*
	*this is the father of template engine ,, i dont why i love play with it , its like my little kid :)
	* access : private ..
	**/
	private function loop_bt ($match_loop) { 
	
			//matchs ..
			$matchs = array('#{odd[^>]([a-zA-Z0-9\_\-\+\./]+)\}(.*?){\/odd\}#is',
							'#{even[^>]([a-zA-Z0-9\_\-\+\./]+)\}(.*?){\/even\}#is',
							'#{\/if\}#i',
							'#{([a-zA-Z0-9\_\-\+\./]+)}#',
							'#{rand=(.*?),(.*?)}#i',
							);
			
			//replaces
			$replaces = array( '<?php if(intval($value[\'\\1\'])%2){?> \\2 <?php } ?>',
								'<?php if(intval($value[\'\\1\'] % 2) == 0){?> \\2 <?php } ?>',
								'<?php } ?>',
								'<?php print isset($value[\'\\1\'])? $value[\'\\1\'] : $this->vars[\'\\1\'];?>',
								'<?php $tpl_rand_is=($tpl_rand_is==0)?1:0; print(($tpl_rand_is==1) ?"\\1":"\\2") ;?>',
							);
			
			//show time
			$content_loop = preg_replace($matchs, $replaces, $match_loop[3]); 
			
			return ('<?php $this->assign(\''.$match_loop[1].'\', $GLOBALS[\''.$match_loop[1].'\']); foreach($this->vars[\''.$match_loop[1].'\']  as $key=>$value){?>'.$content_loop.'<?php }/*endloop*/?>');
	}

	
	/*
	
	for if / elseif experssions ,, 
	acsess : private ..
	*/
	private function if_expr(&$match) {
	

		//if it's IF .. 
		if (strtolower(trim($match[1])) == 'if') {
		
			$result .= 'if ('; 
			
		}elseif(strtolower(trim($match[1])) == 'elseif') {
		
			$result .= '} elseif('; 
			
		}
		
		///its for loop or just normal if ..  [ loop begin with __
		if (strpos($match[2], '__') !== false) {
		
			$val = str_replace('__','',$match[2]);
			$result .= '$this->value[\''.trim($val).'\']'; 
			
		}else{
		
			$result .= '$this->vars[\''.trim($match[2]).'\']'; 
		
		}
		
		//so , now experssions signs .. 
		
		if (!empty($match[3])) {
		
			$expr = trim(strtolower($match[3]));
			
				switch($expr) {
					case '==':
					case 'eq':
					$expr_is = '==';
					break;
					
					case '!=':
					case '<>':
					case 'ne':
					case 'neq':
					$expr_is = '!=';
					break;
					
					case '<':
					case 'lt':
					$expr_is = '<';
					break;
					
					case '<=':
					case 'le':
					case 'lte':
					$expr_is = '<=';
					break;
					
					case '>':
					case 'gt':
					$expr_is = '>';
					break;
					
					case '>=':
					case 'ge':
					case 'gte':
					$expr_is = '>=';
					break;
					
					case '&&':
					case 'and':
					$expr_is = '&&';
					break;
					
					case '||':
					case 'or':
					$expr_is = '||';
					break;
					
					case '!':
					case 'not':
					$expr_is = '!';
					break;
					
					case '%':
					case 'mod':
					$expr_is = '%';
					break;
				
					default:
					$expr_is = $expr;
				}
				
				$result .= ' ' . $expr_is . ' '; 
		}
		
		//whats match4 .. its something cant change it now .. i will do something good with it later .. 
		
				$result .= $match[4];
				
				//some .. additions
				$result .= '){';
		
	
		//return it 
		return '<?php '.$result. '?>';
	}
	


	
	/**
	* now , showing our template ,, its prety function
	* we have 2 type , by filename or just by contents ..
	* name_c : just if there is contents not from file ..
	* access : public ..
	**/
	
    public function show($content, $name_c='', $cache_time = false){ 
	
		//at first .. 
		$page_is	= ($name_c!='') ? $name_c :  $content;
		$cache_time	= ($cache_time !== false )? $cache_time : $this->cachetime;

		//global vars 
		if (empty($this->vars) || !is_array($this->vars)){ 
			$this->vars = array(); 
		}
		if($this->global_vars == 'on'){
			$this->vars = array_merge($this->vars, $GLOBALS);
		}

		//check step
	    if (empty($this->cachedir)) { 
	    	$this->cachedir = $_ENV["TEMP"]; 
	    } 
	    if (!is_writeable( $this->cachedir ) ) { 
	    	echo "<i>ERROR :</i> could not be write in cach folder!"; 
	    } 
		
		//delete step
	    $npage        = $page_is . '.mytpl.php'; 
	    $tpage        = @filemtime("$this->cachedir/$npage"); 

	    if($tpage >= ($tpage+(3600*$cache_time))){ 
	    	@unlink("$this->cachedir/$npage"); 
	    } 

		//including step
	    if(!file_exists("$this->cachedir/$npage")){	

				if ($name_c == ''){
				
					if(!file_exists("$this->Tempdir$npage")){ 
						echo "<i>ERROR :</i> <b>$page_is</b> Template Not Found!"; 
						exit();
					}

					ob_start();
					include("$this->Tempdir$npage"); 
					$pageTemp= ob_get_clean();
					
				}else {
					$pageTemp = $content;
				}

				if($this->php_tags != 'on'){ 
					//reomve any any php tags ,, so if you want php code use our code {php}
					$this->remove_php_tags($pageTemp);
				}
				//compile content
				$pageTemp = $this->compile($pageTemp);	
				
				//then
			    $filenum = @fopen("$this->cachedir/$npage", 'w'); 
			    flock($filenum, LOCK_EX); 
			    @fwrite($filenum, $pageTemp); 
			    fclose($filenum); 
				@chmod($filenum, 0666);
	    } 
		
		//get 
		ob_start();
		include("$this->cachedir/$npage"); 
		$pageTemp = ob_get_clean();
		//now prepare to print 
		return $pageTemp;
    } 
     
} # end of class 
