<?php //This is the login page for registered users
require_once'secure_conn.php';
require 'includes/header(Final).php';

if (isset($_POST['send'])) {
	$errors = array();
	
	$email= filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
	if (empty($email))
		$errors['email'] = 'An email address is required:';
	else {
		//check validity
		$valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);	//returns a string or null if empty or false if not valid	
		if ($valid_email)
			$email = $valid_email;
		else
			$errors['email'] = 'A valid email is required:';
	}
	
	$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
	if (empty($password))
		$errors['psswd']= "A password is required";

	while (!$errors){ 
		require_once ('../../mysqli_connect.php'); // Connect to the db.
		//Query for email
		$sql = "SELECT fName, email, psswd  FROM FF_user WHERE email = ?";
		$stmt = mysqli_prepare($dbc, $sql);
		mysqli_stmt_bind_param($stmt, 's', $email);
		mysqli_stmt_execute($stmt);
		$result=mysqli_stmt_get_result($stmt);
		$rows = mysqli_num_rows($result);
		mysqli_free_result($stmt);
		if ($rows==0) 
			$errors['no_email'] = "That email address wasn't found";
		else { // email found, validate password
			$result=mysqli_fetch_assoc($result); //convert the result object pointer to an associative array 
			$pw_hash=$result['psswd'];
			if (password_verify($password, $pw_hash )) { //passwords match
				$firstName = $result['fName'];
				$folder = $result['folder'];
				//echo "$folder";
				$email = $email['email'];
				//your code here
				session_start();
				$_SESSION['fn'] = $firstName;
				$_SESSION['email'] = $email;
				$_SESSION['folder'] = $folder;
				
				header('Location:logged_in.php');
				
				
				
				exit;
			}
			else {
				$errors['wrong_pw'] = "That isn't the correct password";
			}
		} 
	   } // end while 	
} //end isset $_POST['send']
?>
	<main>
        <form method="post" action="login.php">
			<fieldset>
				<legend>Registered Users Login</legend>
				<?php if ($errors) 
				echo "<h2 class=\"warning\">Please fix the item(s) indicated.</h2>";

            	if ($errors['email']) echo "<h2 class=\"warning\">{$errors['email']}</h2>"; 
				if ($errors['no_email']) echo "<h2 class=\"warning\">{$errors['no_email']}</h2>"; 
				?>
			 <p>	
                <label for="email">Email: </label>
                <input name="email" id="email" type="text"
				<?php if (isset($email) &&!$errors['no_email']) {
                    echo 'value="' . htmlspecialchars($email) . '"';
                } ?>>
            </p>
				<?php if ($errors['psswd']) echo "<h2 class=\"warning\">{$errors['psswd']}</h2>";    
					if ($errors['wrong_pw']) echo "<h2 class=\"warning\">{$errors['wrong_pw']}</h2>"; 
				?>
            <p>                    
                <label for="psswd">Password: </label>
                <input name="password" id="psswd" type="password">
            </p>
            <p>
                <input name="send" type="submit" value="Login">
            </p>
		</fieldset>
        </form>
	</main>
<?php include './includes/footer(Final).php'; ?>
