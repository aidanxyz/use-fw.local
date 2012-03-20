<?php
	class buffer
	{
		private $_data = array();
	
		public function __set($name, $value)
		{
			$this->_data[$name] = $value;
		}
	
		public function __get($name)
		{
			if(isset($this->_data[$name]))
				return $this->_data[$name];
			else
				return;
				//trigger_error("Name is not found in buffer");
		}
		
		public function exists($key)
		{
			return array_key_exists($key, $this->_data);
		}
	}
?>
