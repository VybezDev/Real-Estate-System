<?php
session_start();
require_once('main_fns.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Real Estate System</title>

    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
		<?php 
		do_html_header('');	//padding
		
		checkLogout();	//check if logout button was clicked.
		validateLogin();	//check if username and password is submitted correctly.
		
		if($_SESSION['isOn'] === 1)  //user is logged in.
		{
			display_userNavbar();
			if($_SESSION['userType'] == 'a')	//user is an agent.
			{ ?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_agentMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td><?php display_agentScreen(); ?></td>
					</tr>
				</table>
				</div> <?php
			}
			else if($_SESSION['userType'] == 'c')	//user is a customer.
			{ ?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_customerMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td><?php display_customerScreen(); ?></td>
					</tr>
				</table>
				<div> <?php
			}
		}
		else  //user not logged in.
		{
			display_loginNavbar();
			display_preLoginScreen();
		}
		display_footer();
		?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

<?php

//functions section
function validateLogin()	//check if the login info was submitted.
{
	if (isset($_POST['submitLogin']))
	{	//check if these values are set and not blank.
		if (isset($_POST['username']) && isset($_POST['password']) && $_POST['username'] != "" && $_POST['password'] != "")	
		{
			$username = isset($_POST['username']) ? addslashes($_POST['username']) : '';
			$password = isset($_POST['password']) ? sha1(addslashes($_POST['password'])) : '';
			$loginSuccessful = login($username,$password);	//login function will set the session === 1
			if($loginSuccessful == false)	//login is unsuccessful
			{
				$message = 'Error: Invalid username or password.';
			}
		}
		else
		{
			$message = 'Error: Invalid username or password.';
		}
		
		if (isset($message))
			alertMessage($message);	//display alert message;
	}
}

function checkLogout()	//set session to 0 if user clicked log out.
{
	if (isset($_POST['submitLogout']))
	{
		$_SESSION['isOn'] = 0;
	}
}

?>