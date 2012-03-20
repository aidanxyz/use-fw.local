<?php
	class dispatcher
	{
		protected static $instance;
		
		private function __construct() {}
		private function __clone() {}
		private function __wakeup() {}
		
		public static function getInstance()
		{
			if(is_null(self::$instance))
			{
				self::$instance = new dispatcher;
			}
			return self::$instance;
		}
		
		/* --const vals--later change to class constants-- */
		private $_defaultObjectName;
		private $_defaultActionName = "index";
		
		private $_actionPrefix = "action";
		private $_actionSuffix = "";
		
		private $_controllerPrefix = "";
		private $_controllerSuffix = "Controller";
		
		private $_modelPrefix = "";
		private $_modelSuffix = "Model";
		
		private $_ajaxPrefix = "ajax";
		private $_ajaxSuffic = "";
		
		private $_max_args = 5;
		
		private $_indexUrl = "http://use-fw.local/index.php";
		/* -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
		
		private $_objectName;
		private $_actionName;
		
		private $_args = array();
		
		public function processUrl()
		{
			$this->parseUrl();
			$this->executeUrl();
			
			if(!isset($_POST['ajax']))
				//if not Ajax, render the whole page
				out::render();
		}
		
		public function getObjectName()
		{
			return $this->_objectName;
		}
		
		/**
		 * Explodes or splits the url according to given delimiters
		 */
		#returns: array of delimited elements
		private function multiExplode()
		{
			$addl_delimiters = array("?");
			
			foreach($addl_delimiters as $delimiter) {
				//replace "?"s by "/"s
				$url = str_replace($delimiter, "/", $_SERVER['REQUEST_URI']);
			}
			
			return explode("/", $url, 4 + $this->_max_args + 1);
		}
		
		/**
		 * parses the url into controller, action
		 * and parameter
		 */
		#returns: void
		private function parseUrl()
		{
			$attrs = $this->multiExplode();
			
			/*echo "Debug: sizeof attrs is: ".sizeof($attrs)."<br>";
			foreach($attrs as $key=>$value)
				echo $key."=>".$value."<br>";*/
				
			#object name
			if(sizeof($attrs) > 2)
			{
				if($attrs[2] != "")
					$this->_objectName = $attrs[2];
				else
					return;
			}
			
			#action name
			if(sizeof($attrs) > 3)
			{
				if($attrs[3] != "")
					$this->_actionName = $attrs[3];
				else
					return;
			}
			
			#saving the parameters
			if(sizeof($attrs) > 4)
				for($i = 4; $i < sizeof($attrs); $i++)
					array_push($this->_args, $attrs[$i]);
			
			/*echo "Debug: _args:<br>";
			for($i = 0; $i < sizeof($this->_args); ++$i)
				echo $i." -> ".$this->_args[$i]."<br>";*/
		}
		
		/**
		 * prepares and calls action executor
		 */
		#returns: void
		private function executeUrl()
		{
			/* Authorize */
			if(!$this->isAuthorized())
			{
				out::showError("404");
				return; 
			}
			
			if(is_null($this->_objectName))
			{
				if(!is_null($this->_defaultObjectName))
					$this->_objectName = $this->_defaultObjectName;
				else 
					#return in order to display the default view
					return;
			}
						
			if(is_null($this->_actionName))
			{
				$this->_actionName = $this->_defaultActionName;
			}
			
			$this->execAction();
		}
		
		/**
		 * returns prefixed and suffixed controller name
		 */
		private function fullControllerName()
		{
			if(is_null($this->_objectName)) 
			{
				$this->_objectName = $this->_defaultObjectName;
			}
			return $this->_controllerPrefix.$this->_objectName.$this->_controllerSuffix;
		}
		
		/**
		 * returns prefixed and suffixed action name
		 */
		private function fullActionName()
		{
			if(is_null($this->_actionName))
			{
				$this->_actionName = $this->_defaultActionName;
			}
			
			$action_name = "";
			
			if(isset($_POST['ajax']))
				return $this->_ajaxPrefix.$this->_actionName.$this->_ajaxSuffix;
			else
				return $this->_actionPrefix.$this->_actionName.$this->_actionSuffix;
		}
		
		/**
		 * returns prefixed and suffixed model name
		 */
		private function fullModelName()
		{
			return $this->_modelPrefix.$this->_objectName.$this->_modelSuffix;
		}
		
		/**
		 * find class containing the controller
		 * and executes the action
		 */
		public function execAction()
		{
			$controller_name = $this->fullControllerName();
			$action_name = $this->fullActionName();
			
			if(!class_exists($controller_name))
				trigger_error("Undefined object ".$controller_name);
			
			if(!method_exists($controller_name, $action_name))
				trigger_error("Call to undefined action ".$action_name);
			
			$controller_instance = new $controller_name;
			
			#processing arguments into action method
			$r = new ReflectionMethod($controller_name, $action_name);
			$params_num = $r->getNumberOfRequiredParameters();
			
			if(count($this->_args) < $params_num)
				trigger_error("not all the action arguments are supplied");
				
			#call the action ($controller_instance->$action_name();)
			call_user_func_array(array($controller_instance, $action_name), $this->_args);
		}
		
		#returns: void; saves in session the initial url a user came from
		public function saveReturnUrl()
		{
			$return_url = (isset($_GET['return_url'])) ? $this->_indexUrl.$_GET['return_url'] : $this->_indexUrl;
			$_SESSION['return_url'] = $return_url;
		}
		
		#returns: the initial url if it was set, false otherwise
		public function getReturnUrl()
		{
			if(isset($_SESSION['return_url']))
				return $_SESSION['return_url'];
			else
				return false;
		}
		
		#returns: void; redirects to previous page
		public function gotoReturnUrl()
		{
			$prev_page = $this->getReturnUrl();
			if($prev_page) {
				header("Location: ".$prev_page);
				exit;
			}
		}
		
		public function gotoIndex()
		{
			header("Location: ".$this->_indexUrl);
			exit;
		}
		
		public function gotoUrl($controller, $action)
		{
			$url = $this->_indexUrl."/".$controller."/".$action;
			$numargs = func_num_args();
			if($numargs > 2) 
			{
				for($i = 2; $i < $numargs; $i++)
				{
					$url.= "/".func_get_arg($i);
				}
			}
			header("Location: ".$url);
			exit;
		}
		
		private function isAuthorized()
		{
			if(isset($this->_objectName)) 
			{
				//echo "...authorising: ".$this->fullModelName()."->".$this->fullActionName()."<br>";
				$authorize = new authorization($this->fullModelName(), $this->_actionName);
				return $authorize->isAllowed();
			}
			return true;
		}
	}
?>
