<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;

class Manage {

	protected $container;

	public function __construct(ContainerInterface $container) {
	    $this->container = $container;
	}

	public function add_post(Request $request, $response, $class) {
		// POST: /manage/{class}/add
		if(!app('auth')->can('addRounds')) {
			echo "You don't have permission to do this.";
			exit();
		}
		foreach($request->getParams() as $field_name => $value) {
			if(preg_match('/^runner[0-9]+$/', $field_name)
			   && preg_match('/^[0-9]+$/', $value)) {
				$id = substr( $field_name, 6 ); // remove "runner"
				db_prepared_query(
					'UPDATE runners SET total_rounds = total_rounds + :rounds WHERE id = :id',
					[':id' => $id, ':rounds' => $value]
					);
				db_prepared_query(
					"UPDATE stats SET value = value + :rounds WHERE id = 'total_rounds'",
					[':rounds', $value]
				);
			}
		}
		return redirect("/manage/$class");
	}

}