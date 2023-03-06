<!DOCTYPE html>
<html lang="en">
<?php //This page checks for required content, errors, and provides sticky output
	require_once'secure_conn.php';
	require_once '../../mysqli_connect.php'; 
	
	
	if (isset($_POST['send'])) {
	$missing = array();
	
	$firstname = filter_var(trim($_POST['fname']), FILTER_SANITIZE_STRING); //returns a string
	if (empty($firstname)) 
		$missing['fname']="First name is required";
	
	$lastname = filter_var(trim($_POST['lname']), FILTER_SANITIZE_STRING); //returns a string
	if (empty($lastname)) 
		$missing['lname']="Last name is required";
	
	$email= filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
	if (empty($email))
		$missing['email'] = 'An email address is required:';
	else {
		//check validity
		$valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);	//returns a string or null if empty or false if not valid	
		if ($valid_email)
			$email = $valid_email;
		else
			$missing['email'] = 'A valid email is required:';
			
	}
	require_once '../../mysqli_connect.php';  //$dbc is the connection string set upon successful connection
	$sql="SELECT email from FF_user where email = ?";
	$stmt=mysqli_prepare($dbc, $sql);
	mysqli_stmt_bind_param($stmt, 's', $email);
	mysqli_stmt_execute($stmt);
	$result=mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($result) >=1)
		$missing['exists'] = 'That email already exists in the database. Please log in or enter a different email';
	mysqli_free_result($result);
	
	$password1 = filter_var(trim($_POST['password1']), FILTER_SANITIZE_STRING);
	$password2 = filter_var(trim($_POST['password2']), FILTER_SANITIZE_STRING);
	if (empty($password1) || empty($password2)) 
		$missing['psswd']= "Please enter the password twice";
	elseif ($password1 !== $password2) 
			$missing['pwmatch'] = "The passwords don't match";
	else $password = $password1;
	
	$accepted = filter_var($_POST['terms']);
	if (empty($accepted) || $accepted !='accepted')
		$missing['accepted'] = "You must accept the terms";
	
		if (!$missing) {	
		//Folder name is email stripped of non-alphanumeric characters
		$folder = preg_replace("/[^a-zA-Z0-9]/", "", $email);
		// make lowercase
		$folder = strtolower($folder);
		$sql2 = "INSERT into FF_user (fname, lname, email, psswd) VALUES (?, ?, ?, ?)";
		$stmt2 = mysqli_prepare($dbc, $sql2);
		$pw_hash= password_hash($password, PASSWORD_DEFAULT);
		mysqli_stmt_bind_param($stmt2, 'ssss', $firstname, $lastname, $email, $pw_hash);
		mysqli_stmt_execute($stmt2);
		if (mysqli_stmt_affected_rows($stmt2)){
		header('Location:registered_users.php');
			echo "<main><h2>Thank you for registering $firstname</h2><h3>We have saved your information</h3></main>";
			mysqli_free_result($stmt2);
			header('Location:registered_users.php');
		//create the directory in the uploads folder
		$dirPath = "../../uploads/".$folder;
		mkdir($dirPath,0777);
		}
		else {
			echo "<main><h2>We're sorry. We are unable to add your account at this time.</h2><h3>Please try again later</h3></main>";
		 }
		include 'includes/footer(Final).php'; 
		exit; 	
		}// no errors
	} 
	?>
		<main>
        <h2>Family Frame</h2>
        <li class="active"><a href="index.php">Home</a></li>   
          <form method="post" action="user_reg.php">
  		
			<fieldset>
				<legend>Become a Registered User:</legend>
				<?php if ($missing) { ?>
				<h2 class="warning">Please fix the item(s) indicated.</h2>
				<?php } ?>
           
			<?php if ($missing['fname']) echo "<h2 class=\"warning\">{$missing['fname']}</h2>"; ?>
            <p> 
               
				<label for="fn">First Name: </label>
                <input name="fname" id="fn" type="text"
				 <?php if (isset($firstname)) {
                    echo 'value="' . htmlspecialchars($firstname) . '"';
                } ?>>
				
            </p>
				<?php if ($missing['lname']) echo "<h2 class=\"warning\">{$missing['lname']}</h2>"; ?>
            <p>   
				<label for="ln">Last Name: </label>
                <input name="lname" id="ln" type="text"
				 <?php if (isset($lastname)) {
                    echo 'value="' . htmlspecialchars($lastname) . '"';
                } ?>>
				
            </p>
				<?php 
				if ($missing['email']) echo "<h2 class=\"warning\">{$missing['email']}</h2>"; 
				if ($missing['exists']) echo "<h2 class=\"warning\">{$missing['exists']}</h2>"; 
				?>
			 <p>	
                <label for="email">Email: </label>
                <input name="email" id="email" type="text"
				<?php if (isset($email) && !$errors['email'] && !$errors['exists']) {
                    echo 'value="' . htmlspecialchars($email) . '"';
                } ?>>
            </p>
				<?php if ($missing['psswd']) echo "<h2 class=\"warning\">{$missing['psswd']}</h2>";    
					if ($missing['pwmatch']) echo "<h2 class=\"warning\">{$missing['pwmatch']}</h2>"; 
				?>
            <p>                    
                <label for="pw1">Password: </label>
                <input name="password1" id="pw1" type="password">
            </p>
			<p>
                <label for="pw2">Confirm Password: </label>
                <input name="password2" id="pw2" type="password">
            </p>
         
           	<?php if ($missing['accepted']) echo "<h2 class=\"warning\">{$missing['accepted']}</h2>"; ?>
             <p>			  
                <input type="checkbox" name="terms" value="accepted" id="terms"
				     <?php if ($accepted) {
                                echo 'checked';
                            } ?>>
				
                <label for="terms">I accept the terms of using this website</label>
            </p>
            <p>
                <input name="send" type="submit" value="Register">
            </p>
		</fieldset>
        </form>
	</main>
	<footer>
	  <?php include './includes/footer(Final).php'; ?>?>
	</footer>
	