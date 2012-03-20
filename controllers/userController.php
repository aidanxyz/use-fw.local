<?php
	class UserController
	{	
		#Sign in form fields parameters
		private $_signinFields = array(
			"identity" => array("req", "email"), 
			"password" => "req", 
			"remembered"
		);
		
		public function actionSignin()
		{
			if(isset($_POST['user_auth'])) 
			{
				$filter = new filter($this->_signinFields, "user_auth");
				if($filter->isWrong()) {
					out::writeViewTo("signin_form", "content", $filter->errors);
				}
				else
				{
					$result = auth::getInstance()->signIn($filter->identity, $filter->password, $filter->remembered);
					$this->handleSignin($result);
				}
			}
			else 
			{
				dispatcher::getInstance()->saveReturnUrl();
				out::writeViewTo("signin_form", "content");
			}
		}
		
		public function actionSignout()
		{	
			auth::getInstance()->signOut();
			
			dispatcher::getInstance()->gotoIndex();
		}
		
		private function handleSignin($result)
		{
			if($result == 1) {
				out::writeViewTo("signin_form", "content", array("password" => "Wrong password"));
			}	
			else if($result == 2) {
				out::writeViewTo("signin_form", "content", array("identity" => "Non-existent account"));
			}	
			else
				dispatcher::getInstance()->gotoReturnUrl();
		}
		
		public function actionSignup()
		{
			if(isset($_POST['user']))
			{
				$user = new userModel;
				$filter = new filter($user->validationRules, "user");
				
				if($filter->isWrong()) {
					out::writeViewTo("signup_form", "content", $filter->errors);
				}
				else 
				{
					$stmt = db::connect()->execQuery(
						"insert into UserCredentials(identity, password) values (:email, :password)",
						array("email" => $filter->email, "password" => sha1($filter->password))
					);
					//todo: automatically signin
					dispatcher::getInstance()->gotoUrl("http://auca-connect.local/index.php/user/signin");
				}
			}
			else {
				dispatcher::getInstance()->saveReturnUrl();
				out::writeViewTo("signup_form", "content");
			}
		}
		
		public function actionProfile()
		{
			//don't forget to include in the UsersInfo table the id of UserCredentials
			return;
		}
	}
?>
