<?php
	class sys
	{
		protected static $instance;
		
		private function __construct() {}
		
		private function __clone() {}
		
		private function __wakeup() {}
		
		public static function getInstance(){
			if(is_null(self::$instance)){
				self::$instance = new sys;
			}
			return self::$instance;
		}
		
		private $_directoriesToLook = array("framework/", "models/", "controllers/", "framework/adds/");
		
		public function start()
		{
			$this->startAutoloader();
			$this->startHandlingErrors();
			dispatcher::getInstance()->processUrl();
		}
		
		private function startAutoloader()
		{
			spl_autoload_register(array($this, "customAutoloader"));
		}
		
		private function startHandlingErrors()
		{
			set_error_handler(array("sys", "customErrorHandler"));
		}
		
		public function customErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
		{
			echo "<i>Aidancheg, you've got an error:</i><br>
			      <b>".$errno.": ".$errstr."</b><br>
			      <u>In</u>: ".$errfile."<br>
			      <u>At line:</u> ".$errline." ".$errcontext."<br>";
			//die();
		}
		
		private function customAutoloader($class_name)
		{
			if(class_exists($class_name))
				return true;
					
			foreach($this->_directoriesToLook as $directory)
				if($this->isClassIn($directory, $class_name))
				{
					ob_start();
						require_once($directory.$class_name.".php");
					ob_end_clean();
					return true;
				}

			return false;
		}

		private function isClassIn($path, $class_name)
		{
			$files = scandir($path);
			return in_array($class_name.".php", $files);
		}
		
		/*
		public static function altAutoloader()
		{
			$paths = array('classes/', 'models/');
			set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, $paths));
			spl_autoload_extensions(".php");
			spl_autoload_register();
		}
		*/
	}
?>
