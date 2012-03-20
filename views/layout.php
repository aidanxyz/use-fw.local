<body>
	<h1> This is body </h1>
	<div id="top-header">
		<?php 
			out::loaderLabel("top-header"); //to test 
		?>
		
		<?php if(!auth::getInstance()->signedIn()): //todo: set constant for "index.php" and make easy-to-manage home url?>
			<p> <a href="/index.php/user/signin/?return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">sign in</a> </p>
			<p> <a href="/index.php/user/signup">sign up</a> </p>
		<?php else: ?>
			<p> <a href="/index.php/user/signout">sign out</a> </p>
		<?php endif; ?>
	</div>
	<div id="content">
		<?php 
			out::loaderLabel("content");
		?>
	</div>
