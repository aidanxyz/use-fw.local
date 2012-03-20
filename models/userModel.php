<?php
	class userModel
	{
		public $firstName;
		public $lastName;
		
		public $email;
		public $password;
		
		public $accountId;
		
		public $validationRules = array(
			"firstName" => array("name", "len"=>array("max"=>15)),
			"lastName" => array("name", "len"=>array("max"=>15)),
			"email" => array("req", "email"),
			"password" => array("req", "len"=>array("min"=>9, "max"=>15)),
			# pw2 belongs to form
			"password2" => array("req", "repeat" => array("password"))
		);
		
		/**
		 * Only access <barriers> are listed here. 
		 * If some action is not listed in here, or it is empty,
		 * it means it is available for everyone.
		 */
		public $accessRules = array(
			"signin" => "guest",
			"signup" => "guest",
		);
	}
?>
