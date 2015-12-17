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

		if($_SESSION['isOn'] !== 1) //user is not logged in.
		{
			display_loginNavbar();
			
			echo "
				<div class='container'>
					You must be logged in to see this page.
				</div>";
		}
		else  //user is logged in.
		{
			//check if the change password was submitted.
			if (isset($_POST['submit']))
			{	//check if these values are set and not blank.
				if (isset($_POST['oldPassword']) && isset($_POST['newPassword']) && isset($_POST['verifyNewPassword']) 
					&& $_POST['oldPassword'] != "" && $_POST['newPassword'] != "" && $_POST['verifyNewPassword'] != "" )	
				{
					$oldPassword = isset($_POST['oldPassword']) ? sha1(addslashes($_POST['oldPassword'])) : '';
					$newPassword = isset($_POST['newPassword']) ? sha1(addslashes($_POST['newPassword'])) : '';
					$verifyNewPassword = isset($_POST['verifyNewPassword']) ? sha1(addslashes($_POST['verifyNewPassword'])) : '';
					
					//query to get the password in order to compare.
					$conn = db_connect();
					if($_SESSION['userType'] == 'a')
						$query = "select password from agents where agentUsername like '".$_SESSION['valid_user']."'";
					else if($_SESSION['userType'] == 'c')
						$query = "select password from customers where customerUsername like '".$_SESSION['valid_user']."'";
					$result = mysqli_query($conn,$query);
					$row = mysqli_fetch_assoc($result);
					
					if($row['password'] !== $oldPassword)	//wrong old password		
						$message = 'Error: Incorrect old password.';
					else if($newPassword !== $verifyNewPassword)	//new password and verify don't match.
						$message = 'Error: New password don\'t match verify password.';
					else	//login is successful
					{
						// set new password
						if($_SESSION['userType'] == 'a')
							$updateQuery = "UPDATE agents SET password ='".$newPassword."' WHERE agentUsername like '".$_SESSION['valid_user']."'";
						else if($_SESSION['userType'] == 'c')
							$updateQuery = "UPDATE customers SET password ='".$newPassword."' WHERE customerUsername like '".$_SESSION['valid_user']."'";
						if (mysqli_query($conn,$updateQuery))
							echo '<script type="text/javascript">
									window.onload = function () { alert("Change Password Successful!"); }
							</script>';
						else
							$message = 'Error: Unable to save changed password into database.';
					}
				}
				else
					$message = 'Error: All fields must be filled.';
				
				if (isset($message))
					alertMessage($message);	//display alert message;
			}
		
			//display the navbar and password info.
			display_userNavbar();
			if($_SESSION['userType'] == 'a')	//user is an agent.
			{ ?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_agentMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td><?php display_changePassword_form(); ?></td>
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
						<td><?php display_changePassword_form(); ?></td>
					</tr>
				</table>
				<div> <?php
			}
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
function display_changePassword_form() 
{
	?>
	<form method="post" action="changePassword.php">
	<table bgcolor="#99CC99">
		<tr>
			<td colspan="2"><h3>Change Password:</h3></td>
		</tr>
		<tr>
			<td>Old Password:</td>
			<td><input type="password" name="oldPassword"/></td>
		</tr>
		<tr>
			<td>New Password:</td>
			<td><input type="password" name="newPassword"/></td>
		</tr>
		<tr>
			<td>Verify New Password:</td>
			<td><input type="password" name="verifyNewPassword"/></td>
		</tr>
		<tr>	
			<td height="50" colspan="2" align="center"><input type="submit" value="Submit" name="submit"/></td>
		</tr>
	</table>
	</form>
	<p><a href="home.php">Return to Home</a></p>
	<?php
}