<?php
/*
 * Handles the database installation and configuration
 */
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	echo 'OK.';
} else {
	// Display form
	$output = view('install');
	// Send the page
	app()->respond(app('response'));
}
?>