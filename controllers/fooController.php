<?php
	class fooController
	{
		public function actionBar()
		{
			out::writeViewTo("specific", "content", array('name' => 'val'));
			$s = "some text";
			out::writeTo("content", $s);
		}
		
		public function actionIndex()
		{
			echo "3333<br>";
		}
	}
?>
