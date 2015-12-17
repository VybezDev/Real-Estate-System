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
		//if the registration form is submitted.
		if (isset($_POST['submit']))
		{
			//if all the registration information is set.
			if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['phone'])  && isset($_POST['email']) 
				&& $_POST['username'] != "" && $_POST['password'] != "" && $_POST['firstName'] != "" && $_POST['lastName'] != "" && $_POST['phone'] != "" && $_POST['email'] != "" )	
			{
				$username = addslashes($_POST['username']);
				$password = sha1(addslashes($_POST['password']));
				$firstName = addslashes($_POST['firstName']);
				$lastName = addslashes($_POST['lastName']);
				$phone = $_POST['phone'];
				$email = addslashes($_POST['email']);
				
				$conn = db_connect();
				
				//check if there is a customer with that username
				$query = "select customerUsername from customers where customerUsername like'".$username."'";
				$result = mysqli_query($conn,$query);
				if (!$result)
					$num_result = 0;
				else
					$num_result = mysqli_num_rows($result);
				
				//check if there is an agent with that username
				$queryA = "select agentUsername from agent where agentUsername like'".$username."'";
				$resultA = mysqli_query($conn,$queryA);
				if (!$resultA)
					$num_resultA = 0;
				else
					$num_resultA = mysqli_num_rows($resultA);
				
				if ($num_result === 0 && $num_resultA === 0)	//if there isn't a user with this username then insert user into database
				{
					$addUserQuery = "INSERT INTO customers (customerUsername, firstName, lastName, phone, email, password)
										VALUES ('".$username."', '".$firstName."', '".$lastName."', '".$phone."', '".$email."', '".$password."') ";
					$addUserResult = mysqli_query($conn,$addUserQuery);
					$message = 'Registration Successful!';
					
					//set the values to null when registration is successful
					$_POST['username'] = '';
					$_POST['firstName'] = '';
					$_POST['lastName'] = '';
					$_POST['phone'] = '';
					$_POST['email'] = '';
				}
				else
				{
					$message = 'Error: Username has already used.';
				}
			}
			else
			{
				$message = 'Error: All fields needs to be filled.';
			}
			
			if (isset($message))
				alertMessage($message);	//display alert message;
		}
		
		display_loginNavbar();
		display_registerForm();
		display_footer();
		?>
		
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

<?php
function display_registerForm()
{
	?>
	<!-- prompt user for registration info -->

	<div class='jumbotron'>
		<div class='container'>
			<h1 class="page-header">Register</h1>
			<form method="post" action="register_form.php">
				<table>
					<tr>
						<td>Username:</td>
						<td><input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'])?>"/></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="password"/></td>
					</tr>
					<tr>
						<td>First Name:</td>
						<td><input type="text" name="firstName" value="<?php echo htmlspecialchars($_POST['firstName'])?>"/></td>
					</tr>
					<tr>
						<td>Last Name:</td>
						<td><input type="text" name="lastName" value="<?php echo htmlspecialchars($_POST['lastName'])?>"/></td>
					</tr>
					<tr>
						<td>Phone:</td>
						<td><input type="number" name="phone" value="<?php echo htmlspecialchars($_POST['phone'])?>"/></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><input type="text" name="email" value="<?php echo htmlspecialchars($_POST['email'])?>"/></td>
					</tr>
					<tr>	
						<td height="50" colspan="2" align="center"><input type="submit" value="Submit" name="submit"/></td>
					</tr>
				</table>
			</form>
			<p><a href="home.php">Return to Home</a></p>
		</div>
	</div>
	
	<?php
}

?>