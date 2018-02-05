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
		requires_permission('addRounds');
		$log_string = '';
		$rounds_changed = 0;
		foreach($request->getParams() as $field_name => $value) {
			if(preg_match('/^runner[0-9]+$/', $field_name)
			   && preg_match('/^[0-9]+$/', $value)) {
				$id = substr( $field_name, 6 ); // remove "runner"
				db_prepared_query(
					'UPDATE runners SET total_rounds = total_rounds + :rounds WHERE id = :id',
					[':id' => $id, ':rounds' => $value] );
				db_prepared_query(
					"UPDATE stats SET value = value + :rounds WHERE id = 'total_rounds'",
					[':rounds', $value] );
				$log_string .= $id.'+'.$value.';';
				$rounds_changed = $rounds_changed + $value;
			}
		}
		if($rounds_changed !== 0) {
			db_prepared_query(
				'INSERT INTO logs (`class`, `log_string`, `datetime`, `user`, `rounds_changed`) VALUES (:class, :log_string, :date_time, :user, :rounds_changed)',
				[
					':class' => $class,
					':log_string' => $log_string,
					':date_time' => date('Y-m-d H:m:s'),
					':user' => app('auth')->user()->id(),
					':rounds_changed' => $rounds_changed
				]);
		}
		return redirect("/manage/$class");
	}

	public function rollback_log_post($request, $response, $class) {
		// POST: /manage/{class}/log
		requires_permission('rollback');
		$id = $request->getParams()['id'] ?? '';
		if(preg_match('/^[0-9]+$/', $id)) {
			$rollback_data = db_prepared_query(
				'SELECT log_string FROM logs WHERE id = :id', [':id' => $id] )->fetch();
			if(isset($rollback_data['log_string'])) {
				if (strpos($rollback_data['log_string'], '+')) {
					$this->rollback_addition($rollback_data['log_string'], $class);
					$this->disable_log($id);
				}
				elseif (strpos($rollback_data['log_string'], '-')) {
					$this->rollback_rollback($rollback_data['log_string'], $class);
					$this->disable_log($id);
				}
			}
		}
		return redirect("/manage/$class/log");
	}

	private function disable_log(int $id) {
		db_prepared_query('UPDATE logs SET active = 0 WHERE id = :id',
			[':id' => $id]);
	}

	/**
	 * Helper method for rollback_log_post to roll back round additions
	 * @param string $log_string
	 * @param string $class
	 * @see rollback_log_post()
	 */
	private function rollback_addition(string $log_string, string $class) {
		$round_changes = array_filter(explode(';', $log_string));
		$total_change = 0;
		foreach ( $round_changes as $round_change ) {
			list($id, $change) = explode('+', $round_change);
			db_prepared_query('UPDATE runners SET total_rounds = total_rounds - :change WHERE id = :id',
				[':id' => $id, ':change' => $change]);
			$total_change = $total_change - $change;
		}
		db_prepared_query(
			'INSERT INTO logs (`class`, `log_string`, `datetime`, `user`, `rounds_changed`) VALUES (:class, :log_string, :date_time, :user, :rounds_changed)',
			[
				':class' => $class,
				':log_string' => str_replace('+', '-', $log_string),
				':date_time' => date('Y-m-d H:m:s'),
				':user' => app('auth')->user()->id(),
				':rounds_changed' => $total_change
			]);
	}

	/**
	 * Helper method for rollback_log_post to roll back previous rollbacks
	 * @param string $log_string
	 * @param string $class
	 * @see rollback_log_post()
	 */
	private function rollback_rollback(string $log_string, string $class) {
		$round_changes = array_filter(explode(';', $log_string));
		$total_change = 0;
		foreach ( $round_changes as $round_change ) {
			list($id, $change) = explode('-', $round_change);
			db_prepared_query('UPDATE runners SET total_rounds = total_rounds + :change WHERE id = :id',
				[':id' => $id, ':change' => $change]);
			$total_change = $total_change + $change;
		}
		db_prepared_query(
			'INSERT INTO logs (`class`, `log_string`, `datetime`, `user`, `rounds_changed`) VALUES (:class, :log_string, :date_time, :user, :rounds_changed)',
			[
				':class' => $class,
				':log_string' => str_replace('-', '+', $log_string),
				':date_time' => date('Y-m-d H:m:s'),
				':user' => app('auth')->user()->id(),
				':rounds_changed' => $total_change
			]);
	}

}