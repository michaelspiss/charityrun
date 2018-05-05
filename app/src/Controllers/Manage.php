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
		$donation_change = 0;
		foreach($request->getParams() as $field_name => $value) {
			if(preg_match('/^runner[0-9]+$/', $field_name)
			   && preg_match('/^[0-9]+$/', $value)) {
				$id = substr( $field_name, 6 ); // remove "runner"
				db_prepared_query(
					'UPDATE runners SET total_rounds = total_rounds + :rounds WHERE id = :id',
					[':id' => $id, ':rounds' => $value] );
				$log_string .= $id.'+'.$value.';';
				$rounds_changed += $value;
				$donation_change += $this->updateDonations($id);
				db_prepared_query(
					'UPDATE stats SET value = value + :donation_change WHERE id = :id',
					[':donation_change' => $donation_change] );
			}
		}
		if($rounds_changed !== 0) {
			db_prepared_query(
				"UPDATE stats SET value = value + :donation_change WHERE id = 'total_donations'",
				[':donation_change' => $donation_change] );
			db_prepared_query(
				"UPDATE stats SET value = value + :rounds WHERE id = 'total_rounds'",
				[':rounds' => $rounds_changed] );
			$group_id = $this->getGroupIdFromName($class);
			if($group_id) {
				$this->addLog($group_id, $log_string,$rounds_changed);
			}
		}
		return redirect("/manage/$class");
	}

	// big helper function, mainly consisting of state detection
	private function updateDonations($runner_id, $is_rollback = false) {
		$rounds = db_prepared_query('SELECT total_rounds FROM runners WHERE id = :runner_id', [
			':runner_id' => $runner_id
		])->fetch()['total_rounds'];
		// three scenarios:
		// 1: Runner now has 0 rounds -> set all to 0
		// 2/3: Runner has >0 rounds -> set absolute to max, calculate relative
		$donors = db_prepared_query('SELECT id, donation, amountIsFixed, total_donation FROM donors WHERE runner_id = :runner_id', [
			':runner_id' => $runner_id
		])->fetchAll();
		if($rounds == 0) {
			db_prepared_query('UPDATE donors SET total_donation = 0 WHERE runner_id = :runner_id', [
				':runner_id' => $runner_id
			]);
			$donations_changed = array_sum(array_column($donors, 'total_donation'));
			return -$donations_changed;
		}
		$donations_changed = 0;
		foreach($donors as $donor) {
			if($donor['amountIsFixed'] == 1 && $donor['total_donation'] > 0) {
				if($is_rollback) {
					$donations_changed += -$donor['donation'];
					db_prepared_query('UPDATE donors SET total_donation = :donation WHERE id = :id',
						[':id' => $donor['id'], ':donation' => 0]);
				}
				continue;
			}
			elseif($donor['amountIsFixed'] == 1 ) { // amountIsFixed && total_donation is 0
				$donations_changed += $donor['donation'];
				db_prepared_query('UPDATE donors SET total_donation = :donation WHERE id = :id',
					[':id' => $donor['id'], ':donation' => $donor['donation']]);
			} else {
				// donation amount is depending on rounds
				$donations_changed += $donor['donation'] * $rounds - $donor['total_donation'];
				db_prepared_query('UPDATE donors SET total_donation = :donation WHERE id = :id',
					[':id' => $donor['id'], ':donation' => $donor['donation'] * $rounds]);
			}
		}
		return $donations_changed;
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
		$donation_change = 0;
		$group_id = $this->getGroupIdFromName($class);
		if($group_id) {
			foreach ( $round_changes as $round_change ) {
				list($id, $change) = explode('+', $round_change);
				db_prepared_query('UPDATE runners SET total_rounds = total_rounds - :change WHERE id = :id',
					[':id' => $id, ':change' => $change]);
				$total_change = $total_change - $change;
				$donation_change += $this->updateDonations($id, true);
			}
			db_prepared_query(
				"UPDATE stats SET value = value + :rounds WHERE id = 'total_rounds'",
				[':rounds' => $total_change] );
			db_prepared_query(
				"UPDATE stats SET value = value + :donation_change WHERE id = 'total_donations'",
				[':donation_change' => $donation_change] );
			$this->addLog($group_id, str_replace('+', '-', $log_string),$total_change);
		}
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
		$donation_change = 0;
		$group_id = $this->getGroupIdFromName($class);
		if($group_id) {
			foreach ( $round_changes as $round_change ) {
				list( $id, $change ) = explode( '-', $round_change );
				db_prepared_query( 'UPDATE runners SET total_rounds = total_rounds + :change WHERE id = :id',
					[ ':id' => $id, ':change' => $change ] );
				$total_change = $total_change + $change;
				$donation_change += $this->updateDonations($id, true);
			}
			db_prepared_query(
				"UPDATE stats SET value = value + :rounds WHERE id = 'total_rounds'",
				[':rounds' => $total_change] );
			db_prepared_query(
				"UPDATE stats SET value = value + :donation_change WHERE id = 'total_donations'",
				[':donation_change' => $donation_change] );
			$this->addLog( $group_id, str_replace( '-', '+', $log_string ),
				$total_change );
		}
	}

	protected function addLog(int $group, string $log_string, string $rounds_changed) {
		db_prepared_query(
			'INSERT INTO logs (`class`, `log_string`, `datetime`, `user`, `rounds_changed`) VALUES (:class, :log_string, :date_time, :user, :rounds_changed)',
			[
				':class' => $group,
				':log_string' => $log_string,
				':date_time' => date('Y-m-d H:m:s'),
				':user' => app('auth')->user()->id(),
				':rounds_changed' => $rounds_changed
			]);
	}

	protected function getGroupIdFromName(string $name) {
		$group_data = db_prepared_query('SELECT id FROM groups WHERE name = :group',
			[':group' => $name])->fetch();
		return $group_data['id'] ?? false;
	}

}