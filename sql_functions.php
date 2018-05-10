<?php 


function create_users_table() {
	global $link;
	$query_text = "CREATE TABLE users (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)";
	$query = $link->prepare($query_text);
	if(!$query->execute()) {
		query_error($query_text); return False;
	}
	return True;
}


function sql_connect() {
	global $link;
	
	// Connect to database
	$hostname="localhost";
	$username="aeagleso";
	$password="Superwhocares8?";
	$dbname="kalospace";
	
	//Where the connection is made in the link variable. mysqli is standard (same as mysql but more secure)
	$link = mysqli_connect($hostname, $username, $password, $dbname);

	// check connections
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
}

function query_error($query) {
	// Displays the query to the screen that didn't work
	line_break(2);
	if (is_string($query)) {
		echo "THIS QUERY FAILED: " . $query;
	} else {
		echo "PROBLEM WITH SQL QUERY in sql_connect - can't print it out!";
	}
	
	line_break(2);
}



function run_basic_query($query_text) {
	global $link;
	//Prepare statement takes whatever query entered, cleans up unauthorized characters/functions (i.e. Jimmy Droptables)
	$query = $link->prepare($query_text);
	if(!$query->execute()) {
		query_error($query_text); return False;
	}
	return $query;
}


function insertNewItemIntoDatabase($item_name, $quantity, $price) {
	global $link;
	// Get all relevant data about this object
	$query_text = "INSERT INTO grocery (item, quantity, price) VALUES (?, ?, ?)";
	$query = $link->prepare($query_text);
	$query->bind_param("sdd", $item_name, $quantity, $price);
	if(!$query->execute()) {
		query_error($query_text); return False;
	}
	return True;
}

function deleteItemFromDatabase($item_name) {
	global $link;
	//Removes item once user indicates purchased
	$query_text = "DELETE FROM grocery WHERE item = ?";
	$query = $link->prepare($query_text);
	if (!$query) {
		echo "BAD SQL: " . $query_text;
		return False;
	}
	$query->bind_param("s", $item_name);
	if(!$query->execute()) {
		query_error($query_text); return False;
	}
	return True;
}


// Simple shortcut function for selecting data from a table with a condition
function select_x_from_y_where_a_b_c($field_name, $table, $field_names_array, $operator_array, $value_array, $value_formats, $fetch_method = 'num') {
	global $link;
	$debug = False;
	
	// Compares the inpout field names and tables to a list of acceptable ones to prevent SQL injections
	if (!acceptable_field_name($field_name)) { echo "FIELD NAME REJECTED: " . $field_name; return False; }
	if (!acceptable_table_name($table)) { echo "TABLE NAME REJECTED: " . $table; return False; }
	foreach($field_names_array as $test_name) { if (!acceptable_field_name($test_name)) { echo "FIELD NAME REJECTED"; return False; } }
	foreach($operator_array as $test_operator) { if (!acceptable_operator($test_operator)) { echo "OPERATOR REJECTED"; return False; } }
	
	$query_text = "SELECT $field_name FROM $table";
	
	// If the user passes an array of conditions, it appends a "WHERE" clause to the SQL statement
	$number_of_arguments = count($field_names_array);
	if ($number_of_arguments > 0) { $query_text = $query_text . " WHERE"; }
	
	// Concatenates each of the conditions to the query string
	for ($i = 0; $i < $number_of_arguments; $i++) {
		$query_text = $query_text . " " . $field_names_array[$i] . " " . $operator_array[$i] . " ?";
		if (($i + 1) < $number_of_arguments) {
			$query_text = $query_text . " AND";
		}
	}
	
	// Debug text
	if ($debug) {
		echo "--------------------- select_x_from_y_where_a_b_c --------------------"; line_break(1);
		echo "Here is the query:"; line_break (2);
		echo $query_text; line_break(2);
		echo $value_formats; line_break(2);
		echo "First value:" . $value_array[0]; line_break(1);
		if ($number_of_arguments > 1) { echo "Second value:" . $value_array[1]; line_break(1); }
		echo "------------------------- end -----------------------"; line_break(3);
	}

	$query = $link->prepare($query_text);
	
	// Binds the variable values to parameters depending on how many there are.  In this case, maximum of four conditions.
	if ($number_of_arguments == 1) { $query->bind_param($value_formats, $value_array[0]);
	} else if ($number_of_arguments == 2) { $query->bind_param($value_formats, $value_array[0], $value_array[1]);
	} else if ($number_of_arguments == 3) { $query->bind_param($value_formats, $value_array[0], $value_array[1], $value_array[2]);
	} else if ($number_of_arguments == 4) { $query->bind_param($value_formats, $value_array[0], $value_array[1], $value_array[2], $value_array[3]);
	} else if ($number_of_arguments > 4) { echo "TOO MANY SQL CONDITIONS"; return False;
	}
		
	if(!$query->execute()) {
		query_error($query_text); return False;
	}
	$query_results = get_all_results_2d_array($query, $fetch_method);
	
	// Returns the results of the query if it is successful
	if ($query_results) {
		return $query_results;
	}
	
	// Otherwise return nothing
	return False;
}

?>