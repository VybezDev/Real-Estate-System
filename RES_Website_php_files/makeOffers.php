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

		if($_SESSION['isOn'] !== 1 || $_SESSION['userType'] !== 'c')
		{
			display_loginNavbar();
			
			echo "
				<div class='container'>
					You must be logged in as a customer to see this page.
				</div>";
		}
		else  //user is logged in.
		{
			//check if the login info was submitted.
			if (isset($_POST['submit']))
			{	//check if these values are set and not blank.
				if ($_SESSION['currentOfferStatus'] == 'Decline' || $_SESSION['currentOfferStatus'] == 'Accept') //current status is not pending
					$message = 'Error: Can\'t modify offer that has already been Accepted or Declined.';
				else if (isset($_POST['updatedOffer']) && $_POST['updatedOffer'] != "" && $_POST['updatedOffer'] != "0")	
				{
					$conn = db_connect(); //connect to database
					//check if there is an accepted offer for this property.
					$acceptanceQuery = "select * from offers where propertyID = ".$_SESSION['currentCustomerViewProperty']." and offerStatus like 'Accept'";
					$acceptanceResult = mysqli_query($conn,$acceptanceQuery);
					$num_acceptanceResults = mysqli_num_rows($acceptanceResult);

					if ($num_acceptanceResults > 0) //there is an accepted offers
					{
						$message = 'Error: Cannot make an offer on a property that has already accepted another offer';
					}
					else //there are no accepted offer.
					{
						$updatedOffer = isset($_POST['updatedOffer']) ? $_POST['updatedOffer'] : '';
						$offerID = $_SESSION['currentOfferID'];
						
						
						//update the offer in database.
						if ($offerID == '' )	//if there wasn't a previous offer by this cutomer in the database, then add one.
						{
							$updateQuery = "INSERT INTO offers (offerID, customerUsername, agentUsername, propertyID, offerPrice, offerStatus)
										VALUES (null, '".$_SESSION['valid_user']."', '".$_SESSION['currentCustomerViewPropertyAgentUsername']."',
												'".$_SESSION['currentCustomerViewProperty']."', '".$updatedOffer."', 'Pending')";
						}
						else	//update the previous offer.
						{
							$updateQuery = "UPDATE offers SET offerPrice =".$updatedOffer." WHERE offerID = ".$offerID."";
						}
						if (mysqli_query($conn,$updateQuery))
							$message = 'Make Offer Successful!';
						else
							$message = 'Unable to save offer into database.';
					}
				}
				else
					$message = 'Error: Updated offer must be filled.';
				
				if (isset($message))
					alertMessage($message);	//display alert message;
			}
			
			//display the navbar and make offers info.
			display_userNavbar();
			 ?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_customerMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td><?php display_makeOffers_form(); ?></td>
					</tr>
				</table>
				<div> <?php
			
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
function display_makeOffers_form() 
{
	$currentProperty = $_SESSION['currentCustomerViewProperty'];
	$conn = db_connect();	//connect to database
	
	//query to see if there was a previous offer on this property.
	$query = "select * from offers where customerUsername like '".$_SESSION['valid_user']."' and propertyID like '".$currentProperty."'";
	$result = mysqli_query($conn,$query);
	$num_Results = mysqli_num_rows($result);
	if ($num_Results > 0)	//if there is a result, previous offer found.
	{
		$row = mysqli_fetch_assoc($result);	
		$currentOffer = $row['offerPrice'];
		$_SESSION['currentOfferStatus'] = $row['offerStatus'];
		$_SESSION['currentOfferID'] = $row['offerID'];
	}
	else
	{
		$currentOffer = '';
		$_SESSION['currentOfferStatus'] = '';
		$_SESSION['currentOfferID'] = '';
	}
	
	?>
	<form method="post" action="makeOffers.php">
	<table bgcolor="#99CC99">
		<tr>
			<td colspan="2"><h3>Make Offer:</h3></td>
		</tr>
		<tr>
			<td colspan="2" height="25"><u><b>Current Offer</b></u></td>
		</tr>
		<tr>
			<td>Properly ID:</td>
			<td><?php echo $currentProperty; ?></td>
		</tr>
		<tr>
			<td>Offer:</td>
			<td>$<?php echo $currentOffer; ?></td>
		</tr>
		<tr>
			<td>Status:</td>
			<td><?php echo $_SESSION['currentOfferStatus']; ?></td>
		</tr>
		<tr>
			<td colspan="2" height="25"><b><u>Update Offer</b></u></td>
		</tr>
		<tr>
			<td>Offer: $</td>
			<td><input type="number" name="updatedOffer"/></td>
		</tr>
		<tr>	
			<td height="50" colspan="2" align="center"><input type="submit" value="Update" name="submit"/></td>
		</tr>
	</table>
	</form>
	<p><a href="home.php">Return to Home</a></p>
	<?php
	mysqli_close($conn);
}