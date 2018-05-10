<?php
  session_start();

  // If session variable is not set it will redirect to login page
  if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
    header("location: login.php");
    exit;
  }

  include($_SERVER['DOCUMENT_ROOT'].'/sql_functions.php');
  include($_SERVER['DOCUMENT_ROOT'].'/misc_functions.php');

  global $link;
  sql_connect();

  $groceryItem = $_POST['groceryItem'];
  $itemQuantity = $_POST['itemQuantity'];
  $price = $_POST['itemPrice'];
  $addRemove = $_POST['addRemove'];
  $deleteButton = $_POST['deleteForm'];
  $logOut = $_POST['logOut'];
  $submitButton = $_POST['submitForm'];

  $what_hapened = "";

  if ($deleteButton) {
  	if ($groceryItem == 'delete' or $groceryItem == 'Delete') {
  		if (run_basic_query("DELETE FROM grocery")) {
  			$what_hapened = "Shopping list cleared";
  		}
  	} else {
  		$what_hapened = "Please enter the word 'delete' into the item name and click delete button to clear the list";
  	}
  } else if ($submitButton) {
  	// If data is entered by the user, then put it into the database, otherwise do nothing
  	if ($groceryItem) {
  		if ($addRemove == 'remove') {
  			if (deleteItemFromDatabase($groceryItem)) {
  				$what_hapened = "Item removed";
  			}
  		} else {
  			if (is_numeric($price)) {
  				insertNewItemIntoDatabase($groceryItem, $itemQuantity, $price);
  				$what_hapened = "Item added";
  			} else {
  				$what_hapened = "Please enter a valid price";
  			}
  		}
  	} else {
  		$what_hapened = "Please enter a valid item name";
  	}
  } else if ($logOut) {
  	header("location: logout.php");
    exit;
  }




  $query_text = "SELECT * FROM grocery ORDER BY item ASC";
  $query = run_basic_query($query_text);

  $result = $query->get_result();
  $new_array = array();

  while ($i = $result->fetch_array(MYSQLI_BOTH)) {
  	$new_array[] = $i;
  }
?>

<!DOCTYPE HTML>
<!--
	Miniport by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Shared Shopping List</title>
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
		<li><a href="#top">Add Item</a></li>
  	<li><a href="#list">View List</a></li>
    <li><a href="#logout">Log Out</a></li>
  	<li><a href="#contact">Contact</a></li>
		</ul>
	</nav>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<!-- Home -->
		<div class="wrapper style1 first">
			<article class="container" id="top">
				<div class="row">
					<div class="4u 12u(mobile)">
						<span class="image fit"><img src="images/grocery_aisle.jpg" alt="" /></span>
					</div>
					<div class="8u 12u(mobile)">
            <h3>Add or remove an item from your list.</h3>

    					<div class="row">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                  <label>Item Name:<sup>*</sup></label>
                  <input type="text" name="groceryItem"class="form-control" placeholder="enter your grocery item">
                  <span class="help-block"><?php echo $username_err; ?></span>
                </div>
              </div>

    					<div class="row">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                  <label>Item Quantity:<sup>*</sup></label>
                  <input type="number" step="1" name="itemQuantity" class="form-control" value="1">
                  <span class="help-block"><?php echo $username_err; ?></span>
                </div>
              </div>

    					<div class="row">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                  <label>Item Price:<sup>*</sup></label>
                  <input type="number" step="0.01" name="itemPrice"class="form-control" value="0">
                  <span class="help-block"><?php echo $username_err; ?></span>
                </div>
              </div>

              <div class="row">
                <div>
                  <input type="radio" name="addRemove" value="add" checked>Add<br>
                  <input type="radio" name="addRemove" value="remove">Remove<br>
                </div>

                <div class="form-group">
                  <input type="submit" name="submitForm" class="btn btn-primary" value="Submit">
                </div>
              </div>
					</div>

          <div class="12u 12u(mobile)">
            <div class="row">
              <br>
              <h2 style="color:green">
                <?php
                if ($what_hapened) {
                  echo "*" . $what_hapened;
                }
                ?>
              </h2>
            </div>
          </div>

        </div>
      </article>
    </div>

    <!-- Shopping List -->
		<div class="wrapper style2">
			<article class="container" id="list">
				<div class="row">
					<div class="12u 12u(mobile)">
						<header>
							<h1>Our Shared Shopping List</h1>
						</header>

            <table class="pure-table pure-table-horizontal">
              <thead>
                <tr>
                  <th align="center">Item</th>
                  <th align="center">Quantity</th>
                  <th align="center">Price</th>
                </tr>
              </thead>
              <tbody>
              <?php
                foreach ($new_array as &$j) {
              ?>
              <tr>
                <td><?php echo $j[0]?></td>
                <td><?php echo $j[1]?></td>
                <td><?php echo "$" . $j[2]?></td>
              </tr>
              <?php
                }
              ?>
              </tbody>
            </table>
          </div>
        </div>
      </article>
    </div>

    <!-- Logout -->
		<div class="wrapper style3">
			<article class="container" id="logout">
				<div class="row">
					<div class="12u 12u(mobile)">
            <h5>(Type "delete" into item name at the top of page before pressing delete button to confirm)</h5>
            <input type="submit" name="deleteForm" class="btn btn-primary" value="Delete Shopping List">
            <input type="submit" name="logOut" class="btn btn-primary" value="Log Out">
          </div>
        </div>
      </article>
    </div>

  </form>

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

	<!-- Scripts -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/jquery.scrolly.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/skel-viewport.min.js"></script>
	<script src="assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="assets/js/main.js"></script>

	</body>
</html>
