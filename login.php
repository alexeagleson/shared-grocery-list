<?php

include($_SERVER['DOCUMENT_ROOT'].'/sql_functions.php');
include($_SERVER['DOCUMENT_ROOT'].'/misc_functions.php');

global $link;
sql_connect();

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = 'Please enter username.';
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            /* Password is correct, so start a new session and
                            save the username to the session */
                            session_start();
                            $_SESSION['username'] = $username;      
                            header("location: index.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = 'No account found with that username.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
  <link rel="stylesheet" href="assets/css/main.css" />
  <!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
  <!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
  <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
</head>

<body>

  <!-- Nav -->
	<nav id="nav">
  	<ul class="container">
  		<li><a href="#top">Login</a></li>
    	<li><a href="#contact">Contact</a></li>
		</ul>
	</nav>

  <div class="wrapper style1 first">
    <article class="container" id="top">
      <div class="12u 12u(mobile)">
        <h2>Login</h2>
      <p>Please fill in your credentials to login.</p>
      <h6 style="color:green">This is an example of an simple shared grocery list conneted to an SQL database.  If you don't wish to make an account simply use "testaccount" for both user and password to access it.</h6>
      <br>
      </div>
      <div class="row">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

          <div class="12u 12u(mobile) <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <label>Username:<sup>*</sup></label>
            <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
            <span class="help-block"><?php echo $username_err; ?></span>
          </div>    

          <div class="12u 12u(mobile) <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Password:<sup>*</sup></label>
            <input type="password" name="password" class="form-control">
            <span class="help-block"><?php echo $password_err; ?></span>
          </div>
        </div>

        <div class="row">
          <div class="12u 12u(mobile)">
              <input type="submit" class="btn btn-primary" value="Submit">
          </div>

          <div class="12u 12u(mobile)">
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
          </div>
        </div>
      </form>
    </article>
  </div>

  <!-- Contact -->
  <div class="wrapper style4">
    <article class="container" id="contact">
      <div class="row">
        <div class="12u 12u(mobile)">
          <header>
            <h1>Contact</h1>
          </header>
        </div>
      </div>
      <div class="12u 12u(mobile)">
        Site developed by Alex Eagleson
      </div>
      <div class="12u 12u(mobile)">
        Template by <a href="https://html5up.net/">HTML5 UP</a>, CSS from <a href="https://purecss.io/">Pure CSS</a>
      </div>
      <div class="12u 12u(mobile)">
        Get in touch with me @ <a href="http://alexeagleson.ca">www.alexeagleson.ca</a>
      </div>
    </article>
  </div>

</body>

</html>
