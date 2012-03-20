<?php
	require_once("framework/db.php");
	
	/*$stmt = db::connect()->dbHandler->prepare("insert into UserCredentials(identity, password) values(:identity, :password)");
	
	$identity = "test3@gmail.com";
	$password = md5("somepasswordoftest2");
	
	$stmt->bindParam(":identity", $identity, PDO::PARAM_STR);
	$stmt->bindParam(":password", $password, PDO::PARAM_STR);
	$stmt->execute();*/
	
	#traditional method
	$query = "select password from UserCredentials where identity = :identity";
	$stmt = db::connect()->dbHandler->prepare($query);
	$identity = "test200@gmail.com";
	$stmt->bindParam(":identity", $identity, PDO::PARAM_STR);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	if($stmt->rowCount() == 0)
		echo "no such user";
	else if($stmt->rowCount() == 1)
	{
		$result = $stmt->fetch();
		echo "password is: ".$result['password']."<br>";
	}
	else
		echo "ups...";
		
	#execQuery() method with types
	$stmt = db::connect()->execQuery("select password from UserCredentials where identity = :identity", array("identity" => "aidanxyz@gmail.com"), "s");
	if($stmt->rowCount() == 0)
		echo "NO SUCH USER";
	else if($stmt->rowCount() == 1)
	{
		$result = $stmt->fetch();
		echo "PASSWORD IS: ".$result['password']."<br>";
	}
	else
		echo "UPS...";
		
	#execQuery() method without types
	$stmt = db::connect()->execQuery("select password from UserCredentials where identity = :identity", array("identity" => "test@gmail.com"));
	if($stmt->rowCount() == 0)
		echo "NO SUCH USER";
	else if($stmt->rowCount() == 1)
	{
		$result = $stmt->fetch();
		echo "PASSWORD IS: ".$result['password']."<br>";
	}
	else
		echo "UPS...";
?>
