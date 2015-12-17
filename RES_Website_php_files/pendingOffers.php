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

		if($_SESSION['isOn'] !== 1 || $_SESSION['userType'] !== 'a')
		{
			display_loginNavbar();
			
			echo "
				<div class='container'>
					You must be logged in as an agent to see this page.
				</div>";
		}
		else  //user is logged in.
		{
			$offerID = $_SESSION['currentOfferID']; //set the offerID to current offer view.

			//check if the decline or accept was clicked.
			if (isset($_POST['decline']))
			{
				//update single entry status = decline.
				$conn = db_connect();	//connect to database
				$updateQuery = "UPDATE offers SET offerStatus = 'Decline' WHERE offerID = ".$offerID."";
				if (mysqli_query($conn,$updateQuery))
					$message = 'Offer was Declined.';
			}
			else if (isset($_POST['accept']))
			{
				//update database, single entry status = accept and all else status = decline.
				$conn = db_connect();	//connect to database
				$updateQuery = "UPDATE offers SET offerStatus = 'Accept' WHERE offerID = ".$offerID."";
				if (mysqli_query($conn,$updateQuery))
				{
					//find all other offers on this property status = pending.
					$findPendingQuery = "select * from offers where propertyID = ".$_SESSION['currentPropertyID']." and offerStatus = 'Pending'";
					$pendingResult = mysqli_query($conn,$findPendingQuery);
					$num_Results = mysqli_num_rows($pendingResult);
					
					//update all the pending status to decline
					for($count = 1; $count<=num_Results, $row_pending = mysqli_fetch_assoc($pendingResult); $count++)
					{
						$updateQuery = "UPDATE offers SET offerStatus = 'Decline' WHERE offerID = ".$row_pending['offerID']."";
						mysqli_query($conn,$updateQuery);
					}
					$message = 'Offer was Accepted.';
				}
				else 
					$message = 'Error: Unable to change offer status.';
			}

			if (isset($message))
				alertMessage($message);	//display alert message;

			
			//display the navbar and check pending offers info.
			display_userNavbar();
			 ?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_agentMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td><?php display_checkPendingOffers_form(); ?></td>
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
function display_checkPendingOffers_form() 
{
	$conn = db_connect();	//connect to database
	
	//query to see if customer has a history of offers.
	$query = "select * from offers where agentUsername like '".$_SESSION['valid_user']."' and offerStatus = 'Pending'";
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

			$_SESSION['currentPropertyID'] = $row['propertyID'];
			$currentOffer = $row['offerPrice'];
			$status = $row['offerStatus'];
			$_SESSION['currentOfferID'] = $row['offerID'];
		}
	}
	else
	{
		echo '<script type="text/javascript">
			window.onload = function () { alert("No pending offers."); }
		</script>';
		$_SESSION['currentPropertyID'] = '';		
		$currentOffer = '';
		$status = '';
		$_SESSION['currentOfferID'] = '';		
	}
	
	//current page, so can redirect to same page.
	$currentURL = isset($_GET['view']) ? "pendingOffers.php?view=".$_GET['view'] : "pendingOffers.php";
	
	echo "<form method='post' action=".$currentURL.">";
	?>
	<table bgcolor="#99CC99">
		<tr>
			<td colspan="2"><h3>Check Pending Offers:</h3></td>
		</tr>
		<tr>
			<td colspan="2" height="25"><u><b>Current Offer</b></u></td>
		</tr>
		<tr>
			<td>Properly ID:</td>
			<td><?php echo $_SESSION['currentPropertyID']; ?></td>
		</tr>
		<tr>
			<td>Offer:</td>
			<td>$<?php echo $currentOffer; ?></td>
		</tr>
		<tr>
			<td>Status:</td>
			<td><?php echo $status; ?></td>
		</tr>
		<tr>	
			<td height="50" colspan="2" align="left"><input type="submit" value="Decline" name="decline"/></td>
			<td height="50" colspan="2" align="center"><input type="submit" value="Accept" name="accept"/></td>
		</tr>
	</table>
	</form>
	
	<?php
	//setting the appropriate prevPage and nextPage.
	$prevPage = ($_SESSION['currentPage'] - 1 < 1) ? 1 : $_SESSION['currentPage'] - 1;
	$nextPage = ($_SESSION['currentPage'] + 1 > $num_Results) ?  $_SESSION['currentPage'] : $_SESSION['currentPage'] + 1;
	echo "<p><a href=pendingOffers.php?view=".$prevPage.">Previous Offer</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a href=pendingOffers.php?view=".$nextPage.">Next Offer</a></p>";
	
	echo "<p><a href='home.php'>Return to Home</a></p>";

	mysqli_close($conn);
}