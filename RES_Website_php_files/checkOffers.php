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
			//check if the button was clicked.
			if (isset($_POST['submit']))
			{	//check if these values are set and not blank.
				if ($_SESSION['currentOfferStatus'] == 'Decline' || $_SESSION['currentOfferStatus'] == 'Accept') //current status is not pending
					$message = 'Error: Can\'t modify offer that has already been Accepted or Declined.';
				else if (isset($_POST['updatedOffer']) && $_POST['updatedOffer'] != "" && $_POST['updatedOffer'] != "0")	
				{
					$updatedOffer = isset($_POST['updatedOffer']) ? $_POST['updatedOffer'] : '';
					$offerID = $_SESSION['currentOfferID'];
					
					$conn = db_connect();	//connect to database
					//update the offer in database.
					if ($offerID == '' )	//if there are no previous offers made, then don't allow update.
					{
						$message = 'No offers made.';
					}
					else	//update the previous offer.
					{
						$updateQuery = "UPDATE offers SET offerPrice =".$updatedOffer." WHERE offerID = ".$offerID."";
					}
					if (mysqli_query($conn,$updateQuery))
						$message ='Update Offer Successful!';
					else
						$message = 'Unable to save offer into database.';
				}
				else //update offer is not set
				{
					if ($offerID == '' )	//if there are no previous offers made, then don't allow update.
						$message = 'No offers made.';
					else
						$message = 'Error: Updated offer must be filled.';
				}
				
				if (isset($message))
					alertMessage($message);	//display alert message;
			}

			
			//display the navbar and check offers info.
			display_userNavbar();
			 ?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_customerMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td><?php display_checkOffers_form(); ?></td>
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
function display_checkOffers_form() 
{
	$conn = db_connect();	//connect to database
	
	//query to see if customer has a history of offers.
	$query = "select * from offers where customerUsername like '".$_SESSION['valid_user']."'";
	$result = mysqli_query($conn,$query);
	$num_Results = mysqli_num_rows($result);
	
	$_SESSION['currentPage'] = (isset($_GET['view']))? $_GET['view'] : 1 ; // to set the starting page.
	if ($num_Results > 0)	//if there is a result, previous offer found.
	{
		for($count = 1; $count<=num_Results, $row = mysqli_fetch_assoc($result); $count++)
		{
			//so that only the desired single property is rendered.
			if ($count < $_SESSION['currentPage'])
				continue;	//to iterate to the desired post
			else if ($count > $_SESSION['currentPage'])
				break;	//stop when passed the desired post.

			$currentProperty = $row['propertyID'];
			$currentOffer = $row['offerPrice'];
			$_SESSION['currentOfferStatus'] = $row['offerStatus'];
			$_SESSION['currentOfferID'] = $row['offerID'];
		}
	}
	else
	{
		$currentProperty = '';		
		$currentOffer = '';
		$_SESSION['currentOfferStatus'] = '';
		$_SESSION['currentOfferID'] = '';		
	}
	
	//current page, so can redirect to same page.
	$currentURL = isset($_GET['view']) ? "checkOffers.php?view=".$_GET['view'] : "checkOffers.php";
	
	echo "<form method='post' action=".$currentURL.">";
	?>
	<table bgcolor="#99CC99">
		<tr>
			<td colspan="2"><h3>Check Offers:</h3></td>
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
	
	
	<?php
	//setting the appropriate prevPage and nextPage.
	$prevPage = ($_SESSION['currentPage'] - 1 < 1) ? 1 : $_SESSION['currentPage'] - 1;
	$nextPage = ($_SESSION['currentPage'] + 1 > $num_Results) ?  $_SESSION['currentPage'] : $_SESSION['currentPage'] + 1;
	echo "<p><a href=checkOffers.php?view=".$prevPage.">Previous Offer</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a href=checkOffers.php?view=".$nextPage.">Next Offer</a></p>";
	
	echo "<p><a href='home.php'>Return to Home</a></p>";

	mysqli_close($conn);
}