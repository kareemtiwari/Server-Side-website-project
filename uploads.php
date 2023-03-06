<?php
	require './includes/header(Final).php';
	echo "<main>";
	if (isset($_SESSION['fn'])){
		// Check if the form has been submitted:
		if (isset($_POST['submit'])) {	
			// Check for an uploaded file:
			if (isset($_FILES['upload'])) {
				// Validate the type. Should be GIF, JPEG or PNG.
				$allowed = array ('image/jpeg', 'image/png', 'image/gif');
				if (in_array($_FILES['upload']['type'], $allowed)) {
					// Move the file over.
					$folder = $_SESSION['fn'];
					$image_path = $_FILES['upload']['tmp_name'];
					$image_info = getimagesize($image_path); 
					if (move_uploaded_file ($_FILES['upload']['tmp_name'], "../../uploads/$folder/{$_FILES['upload']['name']}")) {
						echo '<h2>The file '.$_FILES['upload']['name'].' has been uploaded!</h2>';	
						$name=$_FILES['upload']['name'];
						
						
						//write to database
						require_once ('../../mysqli_connect.php'); // Connect to the db.
						$sql = "INSERT into FF_UPimage (email, fileName, details,description) VALUES (?, ?, ?, ?)";
						$stmt = mysqli_prepare($dbc, $sql);
						$email = $_SESSION['email'];
						mysqli_stmt_bind_param($stmt, 'ssss', $email, $name, $details, $description);
						mysqli_stmt_execute($stmt);
						if (mysqli_stmt_affected_rows($stmt)){
							echo '<h3>And the file data has been saved.</h3>';
							//include ('create_thumb.php');
						}
						else {
							echo '<h2>We were unable to save your file data.</h2>';
						}						
						echo "</main>";
						include './includes/footer(Final).php'; 
						// Delete the file if it still exists:
						if (file_exists ($_FILES['upload']['tmp_name']) && is_file($_FILES['upload']['tmp_name'])) {
							unlink ($_FILES['upload']['tmp_name']);
						}
						exit;
					} // End of move... IF.		
				} else { // Invalid type.
					echo '<h2 class="warning">Please upload a GIF, JPEG or PNG image.</h2>';
					}			
			} // End of isset($_FILES['upload']) IF.
			// Check for an error:
			if ($_FILES['upload']['error'] > 0) {
				echo '<p class="warning">The file could not be uploaded because: <strong>';
				// Print a message based upon the error.
				switch ($_FILES['upload']['error']) {
					case 1:
						echo 'The file exceeds the upload_max_filesize setting in php.ini.';
						break;
					case 2:
						echo 'The file exceeds the MAX_FILE_SIZE setting in the HTML form.';
						break;
					case 3:
						echo 'The file was only partially uploaded.';
						break;
					case 4:
						echo 'No file was uploaded.';
						break;
					case 6:
						echo 'No temporary folder was available.';
						break;
					case 7:
						echo 'Unable to write to the disk.';
						break;
					case 8:
						echo 'File upload stopped.';
						break;
					default:
						echo 'A system error occurred.';
						break;
				} // End of switch.
				echo '</strong></p>';			
			} // End of error IF.		
		} // End of the submitted conditional.
	} //end of session IF
	else {
		echo "<h2>We are sorry, but you must be logged in as a registered user to upload images</h2>";
		echo "<h3>Use the Register link above to create an account</h2></main>";
		include ('./includes/footer.php');
		exit;
		}
?>
	<h2>Upload an image</h2>
	<form enctype="multipart/form-data" action="uploads.php" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="2097152">
		<fieldset>
			<legend>Select a GIF, JPEG or PNG image of 2M or smaller to be uploaded:</legend>
			<label for="file">
			File:<input type="file" name="upload" id="file"></label>
			<label for = "submit">And press
			<input type="submit" name="submit" value="Submit" id="submit"></label>
			
			Add a Description of the image here: <input type="text" name="description" value="None" id="description"></label>
			<label for="description" name="upload" id="description"></label>
			
		</fieldset>
	</form>
</main>
<?php include './includes/footer(final).php'; ?>