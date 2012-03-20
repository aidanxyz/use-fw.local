<?php
	class authorization
	{
		private $_accessRulesVarName = "accessRules";
		
		private $_isAllowed = true;
		
		private $_modelName;
		private $_actionName;
		
		private $_rulePrefix = "is";
		
		public function __construct($model_name = "", $action_name = "")
		{
			$this->_modelName = $model_name;
			$this->_actionName = $action_name;
			//authorize
			$this->authorize();
		}
		
		public function isAllowed()
		{
			return $this->_isAllowed;
		}
		
		private function authorize()
		{
			$rules = $this->getAccessRules();
			if($rules)
			{
				if(is_string($rules))
				{
					if($result = $this->verifyRule($rules) !== true) {
						$this->handleAccessError($result);
						$this->_isAllowed = false;
					}
				}
				else if(is_array($rules))
				{
					foreach($rules as $rule)
					{
						if($result = $this->verifyRule($rule) !== true) {
							$this->handleAccessError($result);
							$this->_isAllowed = false;
						}
					}
				}
			}
		}
		
		private function getAccessRules()
		{
			if(class_exists($this->_modelName))
			{
				if(property_exists($this->_modelName, $this->_accessRulesVarName))
				{
					$model_instance = new $this->_modelName;
					$acces_var = $this->_accessRulesVarName;
					$access_rules = $model_instance->$acces_var;
					
					if(isset($access_rules[$this->_actionName]))
						return $access_rules[$this->_actionName];
				}
				else 
					return false;
			}
			else
				trigger_error("Undefined model: ".$this->_modelName);
		}
		
		private function verifyRule($rule)
		{
			$rule = $this->_rulePrefix.$rule;
			
			if(method_exists($this, $rule))
				return $this->$rule();
			else if(method_exists($this->_modelName, $rule)) {
				$model_instance = new $this->_modelName;
				return $model_instance->$rule();
			}
			else
				trigger_error("the rule ".$rule." is not defined/found");
		}
		
		private function handleAccessError($error)
		{
			return;
		}
		
		#returns: true on successful rule verification, error message otherwise
		private function isAuthenticated()
		{
			if(auth::getInstance()->signedIn())
				return true;
			else
				return "must be authenticated to perform this operation: ".$this->_modelName."->".$this->_actionName;
		}
		
		#returns: true on successful rule verification, error message otherwise
		private function isGuest()
		{
			if(!auth::getInstance()->signedIn())
				return true;
			else
				return "must be un-authenticated to perform this operation: ".$this->_modelName."->".$this->_actionName;
		}
	}
?>
