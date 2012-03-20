<?php
	class itemModel
	{
		public $validationRules = array(
			"name"=>array("len"=>array("max"=>30)),
			"price"=>array("num"=>array("min"=>1, "max"=>1000000000)),
			"specs"=>array("len"=>array("max"=>128)),
		);
		
		public $accessRules = array(
			"create"=>"authenticated",
			"delete"=>array("authenticated", "author"),
			"edit"=>array("authenticated", "author"),
		);
		
		
	}
?>
