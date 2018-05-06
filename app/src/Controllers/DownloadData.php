<?php

namespace App\Controllers;

use Slim\Http\Response;

class DownloadData {

	private function setHeaders(Response $response, $filename) {
		// output headers so that the file is downloaded rather than displayed
		$response = $response->withHeader('Content-type','text/csv');
		$response = $response->withHeader('Content-Disposition', 'attachment; filename="'.$filename.'.csv"');

		// do not cache the file
		$response = $response->withHeader('Pragma', 'no-cache');
		$response = $response->withHeader('Expires', '0');
		return $response;
	}

	private function createCSV() {
		// create a file pointer connected to the output stream
		$file = fopen('php://output', 'w');
		fwrite($file, chr(255) . chr(254));
		fwrite($file, mb_convert_encoding("sep=;" . "\n", 'UTF-16LE'));

		return $file;
	}

	private function addCSV($file, $data) {
		$sep = ";";
		$eol = "\n";
		$csv = implode($sep, $data).$eol;
		$csv = mb_convert_encoding($csv, 'UTF-16LE');
		fwrite($file, $csv);
	}

	public function downloadRunnerData($request, Response $response){
		requires_permission('downloadData');
		$response = $this->setHeaders($response, 'runner_data');
		$groups = app('database')->query("SELECT id, name FROM groups ORDER BY name")->fetchAll();
		$file = $this->createCSV();
		foreach ($groups as $group) {
			$total_rounds = 0;
			$runner_data = db_prepared_query("SELECT name, total_rounds FROM runners WHERE class = :class ORDER BY name", [
				':class' => $group['id']
			])->fetchAll();
			$this->addCSV($file, [trans('dynamic.class_x', ['class' => $group['name']])]);
			$this->addCSV($file, [trans('static.name'), trans('static.total_rounds')]);
			foreach ($runner_data as $runner) {
				$total_rounds += $runner['total_rounds'];
				$this->addCSV($file, [$runner['name'], $runner['total_rounds']]);
			}
			$this->addCSV($file, ['', $total_rounds]);
			$this->addCSV($file, ['', '']);
		}
		fclose($file);
		return $response;
	}

	public function downloadDonorData($request, Response $response){
		requires_permission('downloadData');
		$response = $this->setHeaders($response, 'donor_data');
		$groups = app('database')->query("SELECT id, name FROM groups ORDER BY name")->fetchAll();
		$file = $this->createCSV();
		foreach ($groups as $group) {
			$runner_data = db_prepared_query("SELECT id, name, total_rounds FROM runners WHERE class = :class ORDER BY name", [
				':class' => $group['id']
			])->fetchAll();
			$this->addCSV($file, [trans('dynamic.class_x', ['class' => $group['name']])]);
			$this->addCSV($file, []);
			$this->addCSV($file, [trans('static.runner_name'), trans('static.donor_name'), trans('static.total_donations'), trans('static.wants_receipt')]);
			foreach ($runner_data as $runner) {
				$donor_data = db_prepared_query("SELECT name, total_donation, wantsReceipt FROM donors WHERE runner_id = :runner_id ORDER BY name", [
					':runner_id' => $runner['id']
				])->fetchAll();
				$this->addCSV($file, [$runner['name']]);
				foreach($donor_data as $donor) {
					$wants_receipt = $donor['wantsReceipt'] == 1 ? trans('static.yes') : trans('static.no');
					$this->addCSV($file, ['', $donor['name'], $donor['total_donation'], $wants_receipt]);
				}
			}
			$this->addCSV($file, []);
		}
		fclose($file);
		return $response;
	}
}