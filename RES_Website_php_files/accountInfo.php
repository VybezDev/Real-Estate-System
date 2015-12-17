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

		if($_SESSION['isOn'] !== 1)
		{
			display_loginNavbar();
			
			echo "
				<div class='container'>
					You must be logged in to see this page.
				</div>";
		}
		else  //user is logged in.
		{
			//check if the edit account info was submitted.
			if (isset($_POST['submit']))
			{	//check if these values are set and not blank.
				if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['phone']) && isset($_POST['email']) 
					&& $_POST['firstName'] != "" && $_POST['lastName'] != "" && $_POST['phone'] != "" && $_POST['email'] != "")	
				{
					$firstName = isset($_POST['firstName']) ? addslashes($_POST['firstName']) : '';
					$lastName = isset($_POST['lastName']) ? addslashes($_POST['lastName']) : '';
					$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
					$email = isset($_POST['email']) ? addslashes($_POST['email']) : '';
					
					$conn = db_connect();	//connect to database
					// save changes
					if($_SESSION['userType'] == 'a')
						$updateQuery = "UPDATE agents SET firstName ='".$firstName."', lastName ='".$lastName."', 
										phone =".$phone.", email ='".$email."' WHERE agentUsername like '".$_SESSION['valid_user']."'";
					else if($_SESSION['userType'] == 'c')
						$updateQuery = "UPDATE customers SET firstName ='".$firstName."', lastName ='".$lastName."', 
										phone =".$phone.", email ='".$email."' WHERE customerUsername like '".$_SESSION['valid_user']."'";
					if (mysqli_query($conn,$updateQuery))
						$message = 'Edit Account Info Successful!';
					else
						$message = 'Error: Unable to save changes into database.';
				}
				else
					$message = 'Error: All fields must be filled.';

				if (isset($message))
					alertMessage($message);	//display alert message;
			}

			//display the navbar and account info.
			display_userNavbar();
			if($_SESSION['userType'] == 'a')	//user is an agent.
			{ ?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_agentMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td><?php display_editAccountInfo_form(); ?></td>
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
						<td><?php display_editAccountInfo_form(); ?></td>
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
function display_editAccountInfo_form() 
{
	$conn = db_connect();	//connect to database
	//query to get the account info from the database.
	if($_SESSION['userType'] == 'a')
		$query = "select * from agents where agentUsername like '".$_SESSION['valid_user']."'";
	else if($_SESSION['userType'] == 'c')
		$query = "select * from customers where customerUsername like '".$_SESSION['valid_user']."'";
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($result);
	
	//initialize the account info variables.
	$firstName = $row['firstName'];
	$lastName = $row['lastName'];
	$phone = $row['phone'];
	$email = $row['email'];
		
	?>
	<form method="post" action="accountInfo.php">
	<table>
		<tr>
			<td colspan="2"><h3>Edit Account Info:</h3></td>
		</tr>
		<tr>
			<td>First Name:</td>
			<td><input type="text" name="firstName" value="<?php echo htmlspecialchars(stripslashes($firstName)) ?>"/></td>
		</tr>
		<tr>
			<td>Last Name:</td>
			<td><input type="text" name="lastName" value="<?php echo htmlspecialchars(stripslashes($lastName)) ?>"/></td>
		</tr>
		<tr>
			<td>Phone:</td>
			<td><input type="number" name="phone" value="<?php echo $phone ?>"/></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input type="text" name="email" value="<?php echo htmlspecialchars(stripslashes($email)) ?>"/></td>
		</tr>
		<tr>	
			<td height="50" colspan="2" align="center"><input type="submit" value="Save" name="submit"/></td>
		</tr>
	</table>
	</form>
	<p><a href="home.php">Return to Home</a></p>
	<?php
	mysqli_close($conn);
}