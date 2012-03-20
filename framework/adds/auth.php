<?php
	class auth
	{
		protected static $instance;
		
		private function __construct() {
			#starting the session
			if(!isset($_SESSION)) 
				session_start();
		}
		
		private function __clone() {}
		private function __wakeup() {}
		
		public static function getInstance()
		{
			if(is_null(self::$instance))
			{
				self::$instance = new auth;
			}
			return self::$instance;
		}
		
		public static $errors = array(
			1 => "wrong password", 
			2 => "non-existent identification"
		);
		
		const consistent_salt = "places";
		const cookie_expire = 60;
		const cookie_path = "/";
		
		#returns: 0 - on success, non-zero value on error
		public function signIn($identification, $password, $remembered = false)
		{
			$real_pw = $this->getPassword($identification);
			if(!$real_pw)
				#no such identification
				return 2;
			
			if($real_pw !== sha1($password))
				#wrong password
				return 1;
			
			#successfuly authenticated
			$_SESSION['identification'] = $identification;
			#session hijacking
			$_SESSION['useragent'] = md5($_SERVER['HTTP_USER_AGENT'].self::consistent_salt);
			#session fixation
			session_regenerate_id();
			
			if($remembered)
			{
				#generating new hash
				$uniqueid = $this->generateHash();
				#saving new hash in db along with identification
				$this->saveUniqueId($identification, $uniqueid);
				#setting the cookies
				setcookie("identification", $identification, time() + self::cookie_expire, self::cookie_path);
				setcookie("uniqueid", $uniqueid, time() + self::cookie_expire, self::cookie_path);
			}
		}
		
		#returns: bool
		public function signedIn()
		{
			#first check the cookies
			if(isset($_COOKIE['identification']) && isset($_COOKIE['uniqueid']))
			{
				if($this->getUniqueId($_COOKIE['identification']) == $_COOKIE['uniqueid'])
				{
					$_SESSION['identification'] = $_COOKIE['identification'];
				}
			}
			#check sessions
			if(isset($_SESSION['identification']) && $_SESSION['useragent'] == md5($_SERVER['HTTP_USER_AGENT'].self::consistent_salt))
			{
				/* todo:refresh the uniqueids (just repeat lines: 55, 57, 60)*/
				return true;
			}
			return false;
		}
		
		#returns: void
		public function signOut()
		{
			#first unset cookies if they exist
			if(isset($_COOKIE['identification']) && isset($_COOKIE['uniqueid']))
			{
				setcookie("identification", $identification, time() - self::cookie_expire, self::cookie_path);
				setcookie("uniqueid", $uniqueid, time() - self::cookie_expire, self::cookie_path);
			}
			#unset session
			session_destroy();
		}
		
		/**
		 * This method is empty by default;
		 * Developer must implement it;
		 */
		#returns: password for given identification, or NULL/0 otherwise
		private function getPassword($identification)
		{
			$stmt = db::connect()->execQuery(
				"select password from UserCredentials where identity = :identity", 
				array("identity" => $identification)
			);
			
			if($stmt->rowCount() == 1)
			{
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				return $result['password'];
			}
			else if($stmt->rowCount() == 0)
				#no such identification
				return NULL;
			else
				#something bad happened
				trigger_error("duplicate emails or something else went wrong");
		}
		
		/**
		 * This method is empty by default;
		 * Developer must implement it;
		 */
		#returns: bool
		private function saveUniqueId($identification, $uniqueid)
		{
			$stmt = db::connect()->execQuery(
				"select id from Salts where identity = :identity", 
				array("identity" => $identification)
			);
				
			if($stmt->rowCount() == 0)
			{
				$stmt = db::connect()->execQuery(
					"insert into Salts(identity, salt) values(:identity, :salt)",
					array("identity" => $identification, "salt" => $uniqueid)
				);
			}
			else if($stmt->rowCount() == 1)
			{
				$stmt = db::connect()->execQuery(
					"update Salts set salt = :salt where identity = :identity",
					array('identity' => $identification, 'salt' => $uniqueid)
				);
			}
		}
		
		/**
		 * This method is empty by default;
		 * Developer must implement it;
		 */
		#returns: uniqueid for given identification, or NULL/0 otherwise
		private function getUniqueId($identification)
		{
			$stmt = db::connect()->execQuery(
				"select salt from Salts where identity = :identity",
				array('identity' => $identification)
			);
				
			if($stmt->rowCount() == 1)
			{
				$result = $stmt->fetch();
				return $result['salt'];
			}
			else
				trigger_error("getUniqueId error");
		}
		
		#returns: generated random hash of given length
		private function generateHash($length = 36)
		{
			# *true - more entropy
			return substr(md5(uniqid(rand(1, 1000), true)), 0, $length);
		}
	}
?>
