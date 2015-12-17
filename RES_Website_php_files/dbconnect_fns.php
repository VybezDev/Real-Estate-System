<?php
function db_connect() 
{
	$servername = "us-cdbr-azure-east-a.cloudapp.net";
	$username = "b109c844e8a711";
	$password = "9f123dc1";
	$database = "acsm_c59b0da8c3d55e8";
	
	$result = mysqli_connect($servername, $username, $password, $database);
	if (!$result) 
	{
		throw new Exception('Could not connect to database server');
	} 
	else 
	{
		return $result;
	}
}
?>
