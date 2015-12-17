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
					You must be logged in as an agent in to see this page.
				</div>";
		}
		else  //user is logged in.
		{
			if (isset($_POST['submit']))
			{
				$file_result= ""; //image file result.
					$maxsize    = 2097152;	//max size for image file.
					$acceptable = array('image/jpeg', 'image/jpg', 'image/gif', 'image/png');	//acceptable image file.

				//checking if there are any errors with the image file.
				if ($_FILES["file"]["error"]==4)
				{
					//$error = 'Error: No File Uploaded.';
					$_FILES["file"]["name"] = 'house.jpg';	//so we upload the default image.
				}
				else if($_FILES['file']['size'] > $maxsize  || $_FILES['file']["error"] ==1) 
				{
							$error = 'Error: File too large. File must be less than 2 megabytes.';
					}
					else if(!in_array($_FILES['file']['type'], $acceptable) && (!empty($_FILES["file"]["type"]))) 
				{
							$error = 'Error: Invalid file type. Only JPEG, JPG, GIF and PNG types are accepted.';
					}
				else if ($_FILES["file"]["error"]>0)
				{
					$error = 'Error: Image upload error.';
				}
				
				//if all the addListing information is set.
				if (isset($_POST['description']) && isset($_POST['address']) && isset($_POST['city']) 
					&& isset($_POST['state']) && isset($_POST['postalCode'])  && isset($_POST['country'])
					&& isset($_POST['sqft']) && isset($_POST['lot'])  && isset($_POST['garage'])
					&& isset($_POST['askingPrice']) && isset($_POST['ownerRequest'])  && isset($_POST['agentComment'])
					&& isset($_POST['style']) && isset($_POST['bedrooms'])  && isset($_POST['bathrooms'])
					&& isset($_POST['basement']) && isset($_POST['age'])  && isset($_POST['appliances'])
					&& $_POST['description'] != "" && $_POST['address'] != "" && $_POST['city'] != "" 
					&& $_POST['state'] != "" && $_POST['postalCode'] != ""  && $_POST['country'] != ""
					&& $_POST['sqft'] != "" && $_POST['lot'] != ""  && $_POST['garage'] != ""
					&& $_POST['askingPrice'] != "" && $_POST['ownerRequest'] != ""  && $_POST['agentComment'] != ""
					&& $_POST['style'] != "" && $_POST['bedrooms'] != ""  && $_POST['bathrooms'] != "")
				{
					$description = addslashes($_POST['description']);
					$address = addslashes($_POST['address']);
					$city = addslashes($_POST['city']);
					$state = addslashes($_POST['state']);
					$postalCode = addslashes($_POST['postalCode']);
					$country = addslashes($_POST['country']);
					$sqft = addslashes($_POST['sqft']);
					$lot = addslashes($_POST['lot']);
					$status = 'Available';
					$askingPrice = addslashes($_POST['askingPrice']);
					$ownerRequest = addslashes($_POST['ownerRequest']);
					$agentComment = addslashes($_POST['agentComment']);
					$style = addslashes($_POST['style']);
					$bedrooms = addslashes($_POST['bedrooms']);
					$bathrooms = addslashes($_POST['bathrooms']);
					$appliances = addslashes($_POST['appliances']);
					$garage = addslashes($_POST['garage']);
					$basement = addslashes($_POST['basement']);
					$age = addslashes($_POST['age']);
					$agentUsername = $_SESSION['valid_user'];
					$imageFileName = $_FILES["file"]["name"];

					$conn = db_connect();
					
					//query to see if added listing is already in the table for this agent.
					$query = "select address, city, state from properties where address like'".$address."' and city like'".$city."' 
							and state like'".$state."' and agentUserName like '".$_SESSION['valid_user']."' and status like 'Avaliable'";
					$result = mysqli_query($conn,$query);
					$num_result = mysqli_num_rows($result);
					
					if ($num_result > 0)	//added listing already in the table for this agent.
					{
						$error = 'Error: Property is already added and is currently active.';
					}
				}
				else
				{
					$error = 'Error: All fields needs to be filled.';
				}
					
				//if there is no errors then upload image and insert listing into properties table.
				if (isset($error))
					alertMessage($error);	//display alert message, errors.
				else	//no errors
				{
					//insert listing into properties
					$addUserQuery = "INSERT INTO properties (propertyID, description, address, city, state, postalCode, country, sqft, lot, status, askingPrice, 
									ownerRequest, agentComment, style, bedrooms, bathrooms, appliances, garage, basement, age, agentUsername, imageFileName)
									VALUES (null, '".$description."', '".$address."', '".$city."', '".$state."', ".$postalCode.", '".$country."', 
									".$sqft.", ".$lot.", '".$status."', ".$askingPrice.", '".$ownerRequest."', '".$agentComment."', '".$style."', 
									".$bedrooms.", ".$bathrooms.", '".$appliances."', '".$garage."', '".$basement."', ".$age.", '".$agentUsername."', '".$imageFileName."') ";
					$addUserResult = mysqli_query($conn,$addUserQuery);
					if (!$addUserResult)
					{
						$message = 'Error: Couldn\'t upload into database.';
					}
					else
					{
						//upload image onto server.
						move_uploaded_file($_FILES["file"]["tmp_name"], "/home/lin2/public_html/propertyImages/".$_FILES["file"]["name"]);
					
						//set the values to null when registration is successful
						$_POST['description'] = '';
						$_POST['address'] = '';
						$_POST['city'] = '';
						$_POST['state'] = '';
						$_POST['postalCode'] = '';
						$_POST['country'] = '';
						$_POST['sqft'] = '';
						$_POST['lot'] = '';
						$_POST['askingPrice'] = '';
						$_POST['ownerRequest'] = '';
						$_POST['agentComment'] = '';
						$_POST['style'] = '';
						$_POST['bedrooms'] = '';
						$_POST['bathrooms'] = '';
						$_POST['appliances'] = '';
						$_POST['garage'] = '';
						$_POST['basement'] = '';
						$_POST['age'] = '';
						
						$message = 'Add Listing Successful!';
					}
					} 
				
				if (isset($message))
					alertMessage($message);	//display alert message;
			}

			//display the navbar and add property info.
			display_userNavbar();
			?>
				<div class='container'>
				<table>
					<tr>
						<td valign="top"><?php display_agentMenu(); ?></td>
						<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
						<td>
							<form enctype="multipart/form-data" method="post" action="addListing.php">
							<table bgcolor="#99CC99">
								<tr>
									<td colspan="2"><h3>Add Listing:</h3></td>
								</tr>
								<tr>
									<td>Description:</td>
									<td><input type="text" name="description" value="<?php echo htmlspecialchars($_POST['description'])?>"/></td>
								</tr>
								<tr>
									<td>Address:</td>
									<td><input type="text" name="address" value="<?php echo htmlspecialchars($_POST['address'])?>"/></td>
								</tr>
								<tr>
									<td>City:</td>
									<td><input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'])?>"/></td>
								</tr>
								<tr>
									<td>State:</td>
									<td><input type="text" name="state" value="<?php echo htmlspecialchars($_POST['state'])?>"/></td>
								</tr>
								<tr>
									<td>Postal Code: (number)</td>
									<td><input type="number" name="postalCode" value="<?php echo htmlspecialchars($_POST['postalCode'])?>"/></td>
								</tr>
								<tr>
									<td>Country:</td>
									<td><input type="text" name="country" value="<?php echo htmlspecialchars($_POST['country'])?>"/></td>
								</tr>
								<tr>
									<td>Square Ft: (number)</td>
									<td><input type="number" name="sqft" value="<?php echo htmlspecialchars($_POST['sqft'])?>"/></td>
								</tr>
								<tr>
									<td>Lot (sq. ft): (number)</td>
									<td><input type="number" name="lot" value="<?php echo htmlspecialchars($_POST['lot'])?>"/></td>
								</tr>
								<tr>
									<td>Asking Price: (number)</td>
									<td><input type="number" name="askingPrice" value="<?php echo htmlspecialchars($_POST['askingPrice'])?>"/></td>
								</tr>
								<tr>
									<td>Owner Request:</td>
									<td><input type="text" name="ownerRequest" value="<?php echo htmlspecialchars($_POST['ownerRequest'])?>"/></td>
								</tr>
								<tr>
									<td>Agent Comment:</td>
									<td><input type="text" name="agentComment" value="<?php echo htmlspecialchars($_POST['agentComment'])?>"/></td>
								</tr>
								<tr>
									<td>Style:</td>
									<td><input type="text" name="style" value="<?php echo htmlspecialchars($_POST['style'])?>"/></td>
								</tr>
								<tr>
									<td>Bedrooms: (number)</td>
									<td><input type="number" name="bedrooms" value="<?php echo htmlspecialchars($_POST['bedrooms'])?>"/></td>
								</tr>
								<tr>
									<td>Bathrooms: (number)</td>
									<td><input type="number" name="bathrooms" value="<?php echo htmlspecialchars($_POST['bathrooms'])?>"/></td>
								</tr>
								<tr>
									<td>Appliances:</td>
									<td><input type="text" name="appliances" value="<?php echo htmlspecialchars($_POST['appliances'])?>"/></td>
								</tr>
								<tr>
									<td>Garage:</td>
									<td><input type="text" name="garage" value="<?php echo htmlspecialchars($_POST['garage'])?>"/></td>
								</tr>
								<tr>
									<td>Basement:</td>
									<td><input type="text" name="basement" value="<?php echo htmlspecialchars($_POST['basement'])?>"/></td>
								</tr>
								<tr>
									<td>Age: (number)</td>
									<td><input type="number" name="age" value="<?php echo htmlspecialchars($_POST['age'])?>"/></td>
								</tr>
								<tr>
									<td>Browse for File to Upload:</td>
									<td><input name="file" type="file" multiple accept='image/*' id="file" size="file"></td>
								</tr>
								<tr>	
									<td height="50" colspan="2" align="center"><input type="submit" value="Submit" name="submit"/></td>
								</tr>
							</table>
							</form>
							<p><a href="home.php">Return to Home</a></p>
						</td>
					</tr>
				</table>
				</div> 
			<?php

		}
		display_footer();
		?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

