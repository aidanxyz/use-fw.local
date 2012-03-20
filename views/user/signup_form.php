<?php if(!auth::getInstance()->signedIn()): ?>
	<form action="/index.php/user/signup" method="POST">
		<i>
			<label for="email"> 
				<?php echo out::buffer()->email; ?> 
			</label>
		</i>
		email: <input type="text" id="email" name="user[email]" value="<?=out::defaultField('user', 'email'); ?>">
		
		<i>
			<label for="password"> 
				<?php echo out::buffer()->password; ?> 
			</label>
		</i>
		password: <input type="password" name="user[password]">
		
		<i>
			<label for="password2"> 
				<?php echo out::buffer()->password2; ?> 
			</label>
		</i>
		re-password: <input type='password' name="user[password2]">
		
		<input type="submit" value="sign me up">
	</form>
<?php endif; ?>
