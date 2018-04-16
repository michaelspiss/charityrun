<?php
/*
 * Handles the database installation and configuration
 */
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	extract(app('request')->getParams());
	/**
	 * Saves the submitted values (except passwords) in a session.
	 * @param bool $ignore_lines deletes all lines
	 */
	function saveState($ignore_lines = false) {
		$params = app('request')->getParams();
		$_SESSION['retry'] = [
			'admin_username' => $params['admin_username'] ?? '',
			'lines' => isset($params['lines']) && !$ignore_lines ? $params['lines'] : '{}',
			'km_per_round' => $params['km_per_round'] ?? '',
			'display_total_rounds' => isset($params['display_total_rounds']),
			'display_run_km' => isset($params['display_run_km']),
			'display_left_to_go' => isset($params['display_left_to_go'])
		];
	}
	if(isset($admin_password, $admin_password_confirmation, $admin_username,
		$assistant_password, $assistant_password_confirmation, $lines, $km_per_round)) {

		$display_total_rounds = isset($display_total_rounds) ? 1 : 0;
		$display_run_km = isset($display_run_km) ? 1 : 0;
		$display_left_to_go = isset($display_left_to_go) ? 1 : 0;
		if(!preg_match('/^[0-9]+((\.|\,)[0-9]{1,2})?$/', $km_per_round)) {
			saveState();
			echo 'Km per round in wrong format. Examples: 1,5 / 1.5 / 1 / 2<br>';
			echo '<a href="/">back</a>';
			exit();
		}
		if($admin_password !== $admin_password_confirmation || $assistant_password !== $assistant_password_confirmation) {
			saveState();
			echo 'Passwords don\'t match. Please try again.<br>';
			echo '<a href="/">back</a>';
			exit();
		}
		$lines = json_decode($lines);
		if(empty($lines) || !is_array($lines)) {
			saveState(true);
			echo 'You haven\'t added any routes. Please go back and add at least one.<br>';
			echo '<a href="/">back</a>';
			exit();
		}
		// delete saved state if there is one
		unset($_SESSION['retry']);
		/** @var \PDO $db */
		$db = app('database');
		$installer_script = file_get_contents('../app/db.sql');
		$db->exec($installer_script);
		db_prepared_query("INSERT INTO `users` VALUES (1, 'Assistant', :password)",
			[':password' => app('auth')->hashPassword($assistant_password)]);
		db_prepared_query("INSERT INTO `users` VALUES (2, :username, :password)",
			[
				':username' => htmlspecialchars($admin_username),
				':password' => app('auth')->hashPassword($admin_password)
			]);
		// which stats to display
		db_prepared_query("INSERT INTO `stats` VALUES ('total_rounds', 0, :active, 3)",
			[':active' => $display_total_rounds]);
		db_prepared_query("INSERT INTO `stats` VALUES ('km_left_to_go', 0, :active, 2)",
			[':active' => $display_left_to_go]);
		db_prepared_query("INSERT INTO `stats` VALUES ('km_run', 0, :active, 1)",
			[':active' => $display_run_km]);
		// map settings
		db_prepared_query("INSERT INTO `settings` VALUES ('km_per_round', :km_per_round)",
			[':km_per_round' => $km_per_round]);
		/**
		 * Calculates the great-circle distance between two points, with
		 * the Vincenty formula.
		 * @param float $latitudeFrom Latitude of start point in [deg decimal]
		 * @param float $longitudeFrom Longitude of start point in [deg decimal]
		 * @param float $latitudeTo Latitude of target point in [deg decimal]
		 * @param float $longitudeTo Longitude of target point in [deg decimal]
		 * @param float $earthRadius Mean earth radius in [m]
		 * @return float Distance between points in [m] (same as earthRadius)
		 */
		function coordinateDistance(
			$latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
		{
			// convert from degrees to radians
			$latFrom = deg2rad($latitudeFrom);
			$lonFrom = deg2rad($longitudeFrom);
			$latTo = deg2rad($latitudeTo);
			$lonTo = deg2rad($longitudeTo);

			$lonDelta = $lonTo - $lonFrom;
			$a = pow(cos($latTo) * sin($lonDelta), 2) +
			     pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
			$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

			$angle = atan2(sqrt($a), $b);
			return $angle * $earthRadius;
		}
		$total_km = 0;
		for ($i=0; $i<count($lines); $i++) {
			$line = $lines[$i];
			$distance = round(coordinateDistance(
				$line->lat_from,
				$line->long_from,
				$line->lat_to,
				$line->long_to) / 1000); // /1000 to get km instead of meters
			$total_km = $total_km < $distance ? $distance : $total_km;
			db_prepared_query("INSERT INTO `lines` VALUES (:id, :from_lat, :to_lat, :from_long, :to_long, :distance)",
				[
					':id' => $i+1,
					':from_lat' => $line->lat_from,
					':to_lat' => $line->lat_to,
					':from_long' => $line->long_from,
					':to_long' => $line->long_to,
					':distance' => $distance
				]);
		}
		db_prepared_query("INSERT INTO `settings` VALUES ('total_km', :total_km)",
			[':total_km' => $total_km]);
		// success
		app()->respond(redirect('/'));
	} else {
		echo 'Information is missing. Please try again.<br>';
		echo '<a href="/">back</a>';
	}
} else {
	// Display form
	$output = view('install');
	// Send the page
	app()->respond(app('response'));
}
?>