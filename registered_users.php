<?php 
session_start();
require_once'reg_conn.php';
require 'includes/header(Final).php';
?>
<main>
<?php 
$message = "You account has been  created successfully.";
$message2 = "Please proceed to  login using the menu to the left.";

echo '<h2>'.$message.'</h2>';
echo '<h3>'.$message2.'</h3>';
?>
</main>

<?php // Include the footer and quit the script:
	include ('./includes/footer(Final).php'); 
	?>