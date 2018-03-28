<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;

class MapData {
	protected $container;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * Returns the data needed for the map in json format
	 */
	public function getData() {
		$data = [
			"map" => "worldLow",
			"lines"=> []
		];

		$lines = app('database')->query("SELECT * FROM `lines`")->fetchAll();
		for($i = 0; $i<count($lines); $i++) {
			$line_data = [
				"latitudes" => [ (float) $lines[$i]["from_lat"], (float) $lines[$i]["to_lat"] ],
				"longitudes" => [ (float) $lines[$i]["from_long"], (float) $lines[$i]["to_long"] ]
			];
			$data["lines"][] = $line_data;
			$line_data["id"] = "progress_".($i+1);
			$line_data["thickness"] = 4;
			$line_data["alpha"] = 1;
			$line_data["color"] = "#bb0000";
			$line_data["customData"] = ['distance' => $lines[$i]['distance']];
			$data["lines"][] = $line_data;
		}
		echo json_encode($data);
	}

	public function getStats() {
		$stats = app('database')->query("SELECT id, value FROM stats WHERE active = 1")->fetchAll(\PDO::FETCH_KEY_PAIR);
		$settings = app('database')->query("SELECT id, value FROM settings WHERE id = 'km_per_round' OR id = 'total_km'")->fetchAll(\PDO::FETCH_KEY_PAIR);
		echo json_encode(array_merge($stats, $settings));
	}
}