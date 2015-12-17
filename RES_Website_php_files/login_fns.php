<?php
function login($username,$password)
{
	$conn = db_connect();

	//check if user is agent
	$query = "select * from agents where agentUsername='".$username."' and password='".$password."'";
	$result = mysqli_query($conn,$query);
	$num_results = mysqli_num_rows($result);
	if($num_results > 0) //user is agent
	{
		$_SESSION['userType'] = 'a';
	}
	else	//user is not agent
	{
		//check if user is customer
		$query = "select * from customers where customerUsername='".$username."' and password='".$password."'";
		$result = mysqli_query($conn,$query);
		$num_results = mysqli_num_rows($result);
		if($num_results > 0) //user is customer
		{
			$_SESSION['userType'] = 'c';
		}
		else	//user is not agent or customer
		{
			return false;
		}
	}

	$_SESSION['isOn'] = 1;
	$_SESSION['valid_user'] = $username;
	return true;		
}
?>