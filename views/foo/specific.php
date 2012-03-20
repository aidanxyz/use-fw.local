<div>
	<h2>Hey, this is specific view of object "foo"!!!</h2>
	<p><i>Some specific text of this view %)</i></p>
	<p> And now the params: </p>
	<?php
		foreach(out::$params as $key => $value)
		{
			echo $key." => ".$value."<br>";
		}
		echo out::$params['specific']['name']."<br>";
		echo __FILE__;
	?>
</div>
