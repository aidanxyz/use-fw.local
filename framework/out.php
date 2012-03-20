<?php
	class out
	{
		private static $_buffer;
		
		public static function buffer()
		{
			if(is_null(self::$_buffer)) {
				self::$_buffer = new buffer;
			}
			return self::$_buffer;
		}
		
		/**
		 * default label => contents(view files); 
		 * all labels in the layout must be specified here
		 */
		private static $_loaderLabels = array(
			"content" => "views/default.php", 
			"top-header" => ""//"views/user/signin_form.php"
		);
		
		/**
		 * puts view path into specified label
		 */
		#returns: void; 
		public static function writeViewTo($view_name, $label, $params = null)
		{
			if(isset(self::$_loaderLabels[$label]))
			{
				self::$_loaderLabels[$label] = "views/".dispatcher::getInstance()->getObjectName()."/".$view_name.".php";
				
				if($params) {
					foreach($params as $key => $value) {
						self::buffer()->$key = $value;
					}
				}
			}
			else 
				trigger_error("Label ".$label." is not defined");
		}
		
		/**
		 * puts string variable into specified label
		 */
		#returns: void; 
		public static function writeTo($label, & $str)
		{
			return;
		}
		
		/**
		 * puts error view path into error labels
		 */
		#returns: void;
		public static function showError($error, $def_err_label = "content")
		{
			self::$_loaderLabels[$def_err_label] = "views/errors/".$error.".php";
		}
		
		/**
		 * loads view/string, used in layout
		 */
		#returns: void; 
		public static function loaderLabel($label)
		{
			if(!isset(self::$_loaderLabels[$label]))
				trigger_error("Label ".$label." is not defined");
				
			else if(self::$_loaderLabels[$label] !== "")
			{
				if(!file_exists(self::$_loaderLabels[$label]))
					trigger_error("Could not find view file: ".$_loaderLabels[$label]);
				else
					require_once(self::$_loaderLabels[$label]);
			}
			
			//writeTo output goes here
		}
		
		private static $_layout = "views/layout.php";
		
		public static function render()
		{
			require_once(self::$_layout);
		}
		
		#returns: variable stored in the $_POST or nulls
		public static function defaultField($form_name, $field_name)
		{
			if(isset($_POST[$form_name][$field_name]))
				return $_POST[$form_name][$field_name];
			else
				return;
		}
	}
?>
