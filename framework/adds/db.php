<?php
	class db{
		protected static $instance;
		
		/**
		 * Establishes connection and
		 * initializes the database handler.
		 */
		#returns: void
		private function __construct()
		{
			try 
			{
				$this->dbHandler = new PDO(
					"mysql:host=".$this->_sqlHost.";dbname=".$this->_sqlDatabase, 
					$this->_sqlUsername, 
					$this->_sqlPassword
				);
				$this->dbHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
				trigger_error($e->getMessage());
			}
		}
		
		private function __clone() {}
		private function __wakeup() {}
		
		/**
		 * This is the singletone getInstance() method.
		 */
		#returns: unique instance of db class. 
		public static function connect()
		{
			if(is_null(self::$instance))
			{
				self::$instance = new db;
			}
			return self::$instance;
		}
		
		private $_sqlHost = "127.0.0.1";
		private $_sqlUsername = "root";
		private $_sqlPassword = "aidanmysql";
		private $_sqlDatabase = "reviews";
		
		private $_dataTypes = array(
			's' => PDO::PARAM_STR,
			'i' => PDO::PARAM_INT,
			'b' => PDO::PARAM_BOOL
		);
		
		public $dbHandler;
		
		#returns: PDO Statement object
		public function execQuery($query, $params, $types = NULL)
		{
			if(!is_array($params))
				trigger_error("params is not an array in db::execQuery()");
			
			$stmt = $this->dbHandler->prepare($query);
			if(!$stmt)
				trigger_error("invalid query in db::execQuery()");
			
			#data types are given - we must use bindParam() method
			if($types !== NULL)
			{
				if(count($params) != strlen($types))
					trigger_error("db::execQuery params lenght error");
					
				$i = 0;
				foreach($params as $key => & $value)
				{
					$stmt->bindParam($key, $value, $this->getDataType($types[$i]));
					$i++;
				}
				#execution
				$stmt->execute();
			}
			#no need to use bindParam() method
			else
				$stmt->execute($params);
			
			#check for stmt errors
			if($stmt->errorCode() !== '00000')
			{
				$error = $stmt->errorInfo();
				trigger_error("db error: ".$error[0]." --- ".$error[1]." --- ".$error[2]);
			}
			return $stmt;
		}
		
		#returns: PDO data type constant
		private function getDataType($ch)
		{
			return $this->_dataTypes[$ch];
		}
		
		public function getLastId()
		{
			return $this->dbHandler->lastInsertId();
		}
	}
	
	/*
	ya s kajdym razom ubejdayus', chto v Islame brat, na vse est' konkretnoe logicheskoe ob'yasnenie, eto ne prosto kakaya to religiya, eto koroche ppc jest'. 
V Islame OBYAZATEL'NO NUJNO brat' horoshie znaniya, dvigat'sya imenno so znaniyami, a esli tupo budesh' hodit bez znaniy chto ty delaesh', to eto privedet k fanatizmu.
	*/
?>
