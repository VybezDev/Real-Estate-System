<?php

function do_html_header($title) 	//website header
{
	echo "
	<br/><br/><br/><br/>";

}

function display_agentMenu()  //display agent menu 
{
	echo "
	<div class='panel panel-primary'>
		<div class='panel-heading'>
			<h3 class='panel-title'>Welcome ".$_SESSION['valid_user']."!</h3>
		</div>
		<div class='panel-body'>
			<div class='list-group'>
				<a href='accountInfo.php' class='list-group-item'>Account Info</a>
				<a href='changePassword.php' class='list-group-item'>Change Password</a>
				<a href='pendingOffers.php' class='list-group-item'>Check Pending Offers</a>
				<a href='addListing.php' class='list-group-item'>Add Listing</a>
			</div>
		</div>
	</div>
	";
}

function display_customerMenu()  //display customer menu 
{
	echo "
	<div class='panel panel-primary'>
		<div class='panel-heading'>
			<h3 class='panel-title'>Welcome ".$_SESSION['valid_user']."!</h3>
		</div>
		<div class='panel-body'>
			<div class='list-group'>
				<a href='accountInfo.php' class='list-group-item'>Account Info</a>
				<a href='changePassword.php' class='list-group-item'>Change Password</a>
				<a href='checkOffers.php' class='list-group-item'>Check Offer History</a>
			</div>
		</div>
	</div>
	";
}

function display_loginNavbar() 
{
	echo "
	<nav class='navbar navbar-inverse navbar-fixed-top'>
		<div class='container'>
			<div class='navbar-header'>
				<a href='home.php' class='navbar-brand'>Real Estate System</a>
			</div>
			<div id='navbar' class='navbar-collapse'>
				<form class='navbar-form navbar-right' method='post' action='home.php'>
					<div class='form-group'>
						<input type='text' name='username' placeholder='Username' class='form-control'>
					</div>
					<div class='form-group'>
						<input type='password' name='password' placeholder='Password' class='form-control'>
					</div>
					<button type='submit' name='submitLogin' class='btn btn-success'>Log in</button>
				</form>
			</div>
		</div>
	</nav> ";
}

function display_userNavbar() 
{
	echo "
	<nav class='navbar navbar-inverse navbar-fixed-top'>
      <div class='container'>
        <div class='navbar-header'>
          <a href='home.php' class='navbar-brand'>Real Estate System</a>
        </div>
        <div id='navbar' class='navbar-collapse'>
					<form class='navbar-form navbar-right' method='post' action='home.php'>
						<button type='submit' name='submitLogout' class='btn btn-success'>Log out</button>
					</form>
        </div>
      </div>
    </nav> ";
}

function display_preLoginScreen()
{
	echo "
	<div class='jumbotron'>
		<div class='container'>
			<h1>Find a property!</h1>
			<p>There are many properties to search from.  Log in to be able to view the full list of properties and make offers.  New users can register for an account.</p>
			<p><a class='btn btn-primary btn-lg' href='register_form.php' data-toggle='modal' role='button'>Register &raquo;</a></p>
		</div>
	</div>";

	/*
	<div class='container'>".display_customerScreen()."</div> ";	//display_customerScreen() will show properties but not make offer button for users that aren't logged or not customer type.
	*/
}

function display_agentScreen() 
{
	$conn = db_connect();
	$query = "select * from properties where agentUsername like '".$_SESSION['valid_user']."' order by propertyID desc";
	$result = mysqli_query($conn,$query);
	$num_agentPropertyResults = mysqli_num_rows($result);
	
	$_SESSION['currentPage'] = (isset($_GET['view']))? $_GET['view'] : 1 ; // to set the starting page.
	for($count = 1; $count<=num_agentPropertyResults, $row = mysqli_fetch_assoc($result); $count++)
	{
		//so that only the desired single property is rendered.
		if ($count < $_SESSION['currentPage'])
			continue;	//to iterate to the desired post
		else if ($count > $_SESSION['currentPage'])
			break;	//stop when passed the desired post.
		
		$_SESSION['currentAgentViewProperty'] = $row['propertyID']; //currentProperty
		
		//query to get agentName
		$queryAgentName = "select * from agents where agentUsername = '".$row['agentUsername']."'";
		$resultAgentName = mysqli_query($conn,$queryAgentName);
		$rowAgentName = mysqli_fetch_assoc($resultAgentName);
		$agentName = $rowAgentName['firstName']." ".$rowAgentName['lastName'];
				
		//query to get agentPhone
		$queryPhone = "select * from agents where agentUsername = '".$row['agentUsername']."'";
		$resultAgentPhone = mysqli_query($conn,$queryPhone);
		$rowAgentPhone = mysqli_fetch_assoc($resultAgentPhone);
		$agentPhone = $rowAgentPhone['phone'];
		
		//display image here
		if ($row['imageFileName'] != '' || $row['imageFileName'] != null)
			echo "<img src='../propertyImages/".$row['imageFileName']."' border='0' align='left' valign='bottom' height='250' width='350' /> <br/><br/>";
		
		//display property info
		echo "
		<table>
			<tr>
				<td><strong>Property ID:</strong></td>
				<td>".$row['propertyID']."</td>
			</tr>
			<tr>
				<td><strong>Description:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['description']))."</td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['address']))."</td>
			</tr>
			<tr>
				<td><strong>City:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['city']))."</td>
			</tr>
			<tr>
				<td><strong>State:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['state']))."</td>
			</tr>
			<tr>
				<td><strong>Postal Code:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['postalCode']))."</td>
			</tr>
			<tr>
				<td><strong>Country:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['country']))."</td>
			</tr>
			<tr>
				<td><strong>Square Ft:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['sqft']))."</td>
			</tr>
			<tr>
				<td><strong>Lot (sq. ft):</strong></td>
				<td>".htmlspecialchars(stripslashes($row['lot']))."</td>
			</tr>
			<tr>
				<td><strong>Agent Name:</strong></td>
				<td>".htmlspecialchars(stripslashes($agentName))."</td>
			</tr>
			<tr>
				<td><strong>Agent Phone:</strong></td>
				<td>".$agentPhone."</td>
			</tr>
			<tr>
				<td><strong>Status:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['status']))."</td>
			</tr>
			<tr>
				<td><strong>Asking Price:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['askingPrice']))."</td>
			</tr>
			<tr>
				<td><strong>Owner Requests:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['ownerRequest']))."</td>
			</tr>
			<tr>
				<td><strong>Style:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['style']))."</td>
			</tr>
			<tr>
				<td><strong>Bedrooms:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['bedrooms']))."</td>
			</tr>
			<tr>
				<td><strong>Bathrooms:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['bathrooms']))."</td>
			</tr>
			<tr>
				<td><strong>Appliances:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['appliances']))."</td>
			</tr>
			<tr>
				<td><strong>Garage:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['garage']))."</td>
			</tr>
			<tr>
				<td><strong>Basement:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['basement']))."</td>
			</tr>
			<tr>
				<td><strong>Age:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['age']))."</td>
			</tr>
		</table>
		";
		echo "<br/><br/>";
	}
	
	//display propertyOffer button and editProperty button.
	echo "<a href='propertyOffers.php'><button>All Offers on Current Property</button></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='editProperty.php'><button>Edit Property</button></a>";
	
	echo "<br/><br/>";
	
	//setting the appropriate prevPage and nextPage.
	$prevPage = ($_SESSION['currentPage'] - 1 < 1) ? 1 : $_SESSION['currentPage'] - 1;
	$nextPage = ($_SESSION['currentPage'] + 1 > $num_agentPropertyResults) ?  $_SESSION['currentPage'] : $_SESSION['currentPage'] + 1;
	echo "<p><a href=home.php?view=".$prevPage.">Previous Property</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;
	<a href=home.php?view=".$nextPage.">Next Property</a></p>";
	mysqli_close($conn);
}

function display_customerScreen() 
{
	$conn = db_connect();
		
	//query all properties starting from the lastest listing.
	$query = "select * from properties order by propertyID desc";
	$result = mysqli_query($conn,$query);
	$num_propertyResults = mysqli_num_rows($result);
	
	$_SESSION['currentPage'] = (isset($_GET['view']))? $_GET['view'] : 1 ; // to set the starting page.
	for($count = 1; $count<=num_propertyResults, $row = mysqli_fetch_assoc($result); $count++)
	{
		//so that only the desired single property is rendered.
		if ($count < $_SESSION['currentPage'])
			continue;	//to iterate to the desired post
		else if ($count > $_SESSION['currentPage'])
			break;	//stop when passed the desired post.
		
		$_SESSION['currentCustomerViewProperty'] = $row['propertyID'];
		$_SESSION['currentCustomerViewPropertyAgentUsername'] = $row['agentUsername'];
		
		//query to get agentName
		$queryAgentName = "select * from agents where agentUsername = '".$row['agentUsername']."'";
		$resultAgentName = mysqli_query($conn,$queryAgentName);
		$rowAgentName = mysqli_fetch_assoc($resultAgentName);
		$agentName = $rowAgentName['firstName']." ".$rowAgentName['lastName'];
				
		//query to get agentPhone
		$queryPhone = "select * from agents where agentUsername = '".$row['agentUsername']."'";
		$resultAgentPhone = mysqli_query($conn,$queryPhone);
		$rowAgentPhone = mysqli_fetch_assoc($resultAgentPhone);
		$agentPhone = $rowAgentPhone['phone'];

		//display property image
		if ($row['imageFileName'] != '' || $row['imageFileName'] != null) 
			echo "<img src='../propertyImages/".$row['imageFileName']."' border='0' align='left' valign='bottom' height='250' width='350' /> <br/><br/>";  
		
		//display property info
		echo "
		<table>
			<tr>
				<td><strong>Property ID:</strong></td>
				<td>".$row['propertyID']."</td>
			</tr>
			<tr>
				<td><strong>Description:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['description']))."</td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['address']))."</td>
			</tr>
			<tr>
				<td><strong>City:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['city']))."</td>
			</tr>
			<tr>
				<td><strong>State:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['state']))."</td>
			</tr>
			<tr>
				<td><strong>Postal Code:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['postalCode']))."</td>
			</tr>
			<tr>
				<td><strong>Country:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['country']))."</td>
			</tr>
			<tr>
				<td><strong>Square Ft:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['sqft']))."</td>
			</tr>
			<tr>
				<td><strong>Lot (sq. ft):</strong></td>
				<td>".htmlspecialchars(stripslashes($row['lot']))."</td>
			</tr>
			<tr>
				<td><strong>Agent Name:</strong></td>
				<td>".htmlspecialchars(stripslashes($agentName))."</td>
			</tr>
			<tr>
				<td><strong>Agent Phone:</strong></td>
				<td>".$agentPhone."</td>
			</tr>
			<tr>
				<td><strong>Status:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['status']))."</td>
			</tr>
			<tr>
				<td><strong>Asking Price:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['askingPrice']))."</td>
			</tr>
			<tr>
				<td><strong>Owner Requests:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['ownerRequest']))."</td>
			</tr>
			<tr>
				<td><strong>Style:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['style']))."</td>
			</tr>
			<tr>
				<td><strong>Bedrooms:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['bedrooms']))."</td>
			</tr>
			<tr>
				<td><strong>Bathrooms:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['bathrooms']))."</td>
			</tr>
			<tr>
				<td><strong>Appliances:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['appliances']))."</td>
			</tr>
			<tr>
				<td><strong>Garage:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['garage']))."</td>
			</tr>
			<tr>
				<td><strong>Basement:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['basement']))."</td>
			</tr>
			<tr>
				<td><strong>Age:</strong></td>
				<td>".htmlspecialchars(stripslashes($row['age']))."</td>
			</tr>
		</table>
		";
		echo "<br/><br/>";
	}
	if ($_SESSION['isOn'] === 1)  //only logged in customers can see this button and make an offer.
		echo "<a href='makeOffers.php'><button>Make Offer</button></a>";
	echo "<br/><br/>";
	
	//setting the appropriate prevPage and nextPage.
	$prevPage = ($_SESSION['currentPage'] - 1 < 1) ? 1 : $_SESSION['currentPage'] - 1;
	$nextPage = ($_SESSION['currentPage'] + 1 > $num_propertyResults) ?  $_SESSION['currentPage'] : $_SESSION['currentPage'] + 1;
	echo "<p><a href=home.php?view=".$prevPage.">Previous Property</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;
	<a href=home.php?view=".$nextPage.">Next Property</a></p>"; 
	
	mysqli_close($conn);
}

function display_footer()
{
	echo "
	<div class='container'>
		<hr>
		<footer>
			<p>&copy; Fall 2015 Csc 430 - Danny, Helen, Matt, and Steven.</p>
		</footer> 
	</div> ";
}
?>