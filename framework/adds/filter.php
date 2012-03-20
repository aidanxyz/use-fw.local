<?php
	class filter
	{
		#<var_name => array(filters)> array
		private $_params;
		#<var_name => value> array
		private $_source;
		
		public $errors = array();
		private $_data = array();
		
		private $_filterParams;
		
		public function __construct($params, $source = NULL)
		{
			$this->_params = $params;
			
			if($source == NULL)
				return;
				
			if(is_object($source)) {
				$this->_source = get_object_vars($source);
			}
			else if(is_string($source) && isset($_POST[$source])) {
				$this->_source = & $_POST[$source];
			}
			
			$this->applyFilters();
		}
		
		#returns: copy array of _data
		public function getData()
		{
			return $this->_data;
		}
		
		public function __get($name)
		{
			if(array_key_exists($name, $this->_data))
				return ($this->_data[$name]);
			else
				trigger_error("non-existent data (".$name.") in the filter");
		}
		
		private function fixSource()
		{
			foreach($this->_params as $field => $filters)
			{
				// field without filters
				if(is_numeric($field)) {
					$field = $filters;
				}
				
				if(!isset($this->_source[$field])) {
					$this->_source[$field] = "";
				}
			}
		}
		
		private function callFilter($filter, $value, $field)
		{
			if($value == "" && $filter != "req") 
				return;
				
			$method_name = $filter."Filter";
			if(method_exists($this, $method_name)) {
				$this->$method_name($field, $value);
			}
		}
		
		public function applyFilters()
		{
			if($this->_source == NULL) {
				trigger_error("No source in the filter");
			}
			
			$this->fixSource();
			
			foreach($this->_params as $field => $filters)
			{
				// field1 => array()
				if(is_array($filters))
				{
					$filter = null;
					foreach($filters as $key => $value) {
						// field1 => array("filter1", "filter2"....)
						if(is_numeric($key)) {
							$filter = $value;
						}
						// field1 => array("filter1" => array("param1", "param2"))
						else {
							$filter = $key;
							$this->_filterParams = $value;
						}
					
						if($filter) {
							$this->callFilter($filter, $this->_source[$field], $field);
						}
					}
				}
				// field1 => "filter"
				else if(is_string($filters)) 
				{
					// field with no filters
					if(is_numeric($field)) {
						$field = $filters;
					}
					else {
						// field1 => "filter"
						$this->callFilter($filters, $this->_source[$field], $field);
					}
				}
				
				if(!array_key_exists($field, $this->errors)) {
					$this->_data[$field] = $this->htmlEncode($this->_source[$field]);
				}
			}
		}
		
		private function htmlEncode($value)
		{
			return htmlentities($value, ENT_QUOTES);
		}
		
		public function isWrong()
		{
			return count($this->errors) > 0;
		}
	
		private function reqFilter($field, $value)
		{
			if($value == "") {
				$this->errors[$field] = "can not be blank";
			}
		}
		
		private function emailFilter($field, $value)
		{
			if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				$this->errors[$field] = "it was not an email";
			}
		}
		
		private function nameFilter($field, $value)
		{
			$pattern = "/^[-\sa-zA-Z]+$/";
			if(preg_match($pattern, $value) == 0) {
				$this->errors[$field] = "only letters, '-' and spaces are allowed";
			}
		}
		
		private function numFilter($field, $value)
		{
			if(!is_numeric($value)) {
				$this->errors[$field] = "this must be a number";
			}
			else {
				settype($value, "integer");
				if(isset($this->_filterParams["min"]) and $value < $this->_filterParams["min"])
					$this->errors[$field] = "must be bigger than ".$this->_filterParams["min"];
				if(isset($this->_filterParams["max"]) and $value > $this->_filterParams["max"])
					$this->errors[$field] .= "must be smaller than ".$this->_filterParams["max"];
			}
		}
		
		private function repeatFilter($field, $value)
		{
			if(!isset( $this->_source[ $this->_filterParams[0] ] ) ) {
				$this->errors[$field] = "field to repeat can not be empty";
			}
			else {
				if($value != $this->_source[ $this->_filterParams[0] ]) {
					$this->errors[$field] = "fields do not match";
				}
			}
		}
		
		private function lenFilter($field, $value)
		{
			if(isset($this->_filterParams["min"]) and $value < $this->_filterParams["min"])
				$this->errors[$field] = "must be at least ".$this->_filterParams["min"]." chars";
			if(isset($this->_filterParams["max"]) and $value > $this->_filterParams["max"])
				$this->errors[$field] .= "must be shorter than ".$this->_filterParams["max"]." chars";
		}
	}
?>
