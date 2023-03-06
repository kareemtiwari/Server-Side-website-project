<!DOCTYPE html>
<html lang="en">
<?php session_start();
?>

<head>
<div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="index.php">Home</a></li>                    
                <li class="active"><a href="user_reg.php">Create Account</a></li>
                <li class="active"><a href="uploads.php">Upload Pix</a></li>
                <li  class="active"><a href = "login.php">Login</a></li>
                <?php if (isset($_SESSION['email'])) { //create menu for registered users ?>
				<li><a href="uploads.php" <?php if ($currentPage == 'uploads.php') {echo 'id="here"'; } ?>>Upload Pix</a>
				<li><a href="logged_out.php" <?php if ($currentPage == 'logout.php') {echo 'id="here"'; } ?>>Logout</a>
		<?php } ?>
            </ul>
        </div>
</head>
<body>
<header>
<h1>Family Frame</h1>
</header>
