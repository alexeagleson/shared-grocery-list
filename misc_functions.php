<?php

function line_break($number_of_breaks) {
	// Displays number of HTML line breaks equal to value of argument
	for ($i = 1; $i <= $number_of_breaks; $i++) {
		?><br><?php
	}
}

?>