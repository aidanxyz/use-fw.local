<?php if(!auth::getInstance()->signedIn()): ?>
	<form action="/index.php/user/signin" method="POST">
		<i>
			<label for="identity"> 
				<?php echo out::buffer()->identity; ?> 
			</label>
		</i>
		email: <input type="text" id="identity" name="user_auth[identity]" value="<?=out::defaultField('user_auth', 'identity'); ?>">
		
		<i>
			<label for="password"> 
				<?php echo out::buffer()->password; ?> 
			</label>
		</i>
		password: <input type="password" name="user_auth[password]">
		
		<i>
			<label for="remembered"> 
				<?php echo out::buffer()->remembered; ?> 
			</label>
		</i>
		remembered me: <input type='checkbox' name="user_auth[remembered]">
		
		<input type="submit" value="sign me in">
	</form>
<?php endif; ?>
