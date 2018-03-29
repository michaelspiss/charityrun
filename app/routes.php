<?php

/*
|--------------------------------------------------------------------------
| SINGLE PAGES
|--------------------------------------------------------------------------
*/

/*
 * Index, let the user choose what to do:
 * 1. Display map
 * 2. Display stats
 * 3. Log in
 */
use App\Auth\LoginCheckMiddleware as LCM;

$app->get('/', function () {
    // /
    return view('home');
});

/*
 * Display the live map, with statistics about the run
 */
$app->get('/map', function () {
    // /map
	$total_data["stats"] = app('database')
		->query("SELECT id, value, active FROM stats ORDER BY item_order")
		->fetchAll(PDO::FETCH_UNIQUE);
	$total_data["settings"] = app('database')
		->query("SELECT id, value FROM settings WHERE id = 'total_km' OR id = 'km_per_round'")
		->fetchAll(PDO::FETCH_KEY_PAIR);
	return view('map', $total_data);
});

/*
 * Returns all the data needed for the map to display
 */
$app->get('/map/json', \App\Controllers\MapData::class.':getData');

/*
 * Returns up-to-date stats
 */
$app->get('/stats/json', \App\Controllers\MapData::class.':getStats');


/*
 * Display the current top-runners and overall statistics
 * "Leaderboard"
 */
$app->get('/stats', function ($request, $response) {
    // /stats
});

/*
 * Display log in form, redirect if already logged in
 */
$app->get('/login', function ($request) {
    // /login
	if(app('auth')->loggedIn()) {
		return redirect('/manage');
	}
    /** @var \Slim\Http\Request $request */
    // check if user wants to log in as admin
    $login_as_admin = isset($request->getQueryParams()['admin']);
    return view('login', ['login_as_admin' => $login_as_admin]);
});

/*
 * If password is correct authenticate user and redirect to /manage,
 * else send back to login
 */
$app->post('/login', function ($request) {
    // POST: /login
    /** @var \Slim\Http\Request $request */
    $password = $request->getParam('password');
    // use username if provided, else log in as 'Assistant'
    $username = $request->getParam('username', 'Assistant');
    if(app('auth')->login($username , $password))
    {
        return redirect('/manage');
    } else {
        return redirect('/login');
    }
});

/*
 * Log the user out, redirect to index
 */
$app->get('/logout', function () {
    // /logout
    // log the user out
    app('auth')->logout();
    return redirect('/');
});

/*
|--------------------------------------------------------------------------
| MANAGE
|--------------------------------------------------------------------------
*/

/*
 * Display a list of all classes to choose from
 */
$app->get('/manage', function () {
	// /manage
	$group_names = app('database')->query('SELECT name from groups')->fetchAll();
	return view('select_class', ['group_names' => $group_names]);
})->add(new LCM());

/*
 * Get class selection, redirect to /manage/{class},
 * where class is the selected class.
 * This will only be called if JavaScript is disabled
 */
$app->post('/manage', function ($request) {
    // POST: /manage
    /** @var \Slim\Http\Request $request */
    $class = $request->getParam('class');
    // if no class has been submitted
    if($class == "") {
        return redirect('/manage');
    }
    return redirect('/manage/'.$class);
})->add(new LCM());

/*
 * Display a FAQ page regarding the management of classes
 */
$app->get('/manage/help', function () {
    // /manage/help
    return view('help');
})->add(new LCM());

$app->group('/manage/{class}', function () {

    /*
    * Display an overview of the selected class (runners, finished rounds)
    */
    $this->get('', function ($request, $response, $class) {
        // /manage/{class}
	    // make sure the group exists
	    $group = db_prepared_query(
		    'SELECT name FROM groups WHERE name = :name',
		    [':name' => $class]
	    )->fetch();
	    if(empty($group)) {
		    return app('notFoundHandler')($request, $response);
	    }
	    $runners = db_prepared_query(
	    	'SELECT r.id, r.name, r.total_rounds FROM runners as r, groups as g WHERE g.name = :class AND r.class = g.id',
		        [':class' => $class]
		    )->fetchAll();
        return view('manage.overview', ['class' => $class, 'runners' => $runners]);
    });

    /*
    * Interface to add rounds to the runners
    */
    $this->get('/add', function ($request, $response, $class) {
        // /manage/{class}/add
	    requires_permission('addRounds');
	    $runners = db_prepared_query(
	    	'SELECT r.id, r.name FROM runners as r, groups as g WHERE g.name = :class AND r.class = g.id',
	        [':class' => $class]
	    )->fetchAll();
        return view('manage.add', ['class' => $class, 'runners' => $runners]);
    });

    /*
     * Add rounds to the runners
     */
    $this->post( '/add', \App\Controllers\Manage::class.':add_post');

    /*
     * Display log of all recent updates of the runners rounds
     */
	$this->get('/log[/{page:[0-9]+}]', function ($request, $response, $class, $page = 1) {
		// /manage/{class}/log/
		$offset = 25*($page-1) > 0 ? 25*($page-1) : 0;
		$logs = db_prepared_query(
			'SELECT l.id, l.log_string, l.datetime, l.rounds_changed, l.active, u.username as user_name FROM logs as l, users as u, groups AS g WHERE l.user = u.id AND l.class = g.id AND g.name = :class ORDER BY l.id DESC LIMIT 25 OFFSET :offset',
			[':class' => $class, ':offset' => $offset])->fetchAll();
		return view('manage.log', ['class' => $class, 'logs' => $logs, 'page' => $page]);
	});

    /*
    * Undo round updates, Undo undos
    */
	$this->post('/log', \App\Controllers\Manage::class.':rollback_log_post');

	/*
	* Display sidebar, from "more"
	* This will only be called if JavaScript is disabled
	*/
    $this->get('/more', function ($request, $response, $class) {
        // /manage/{class}/more
        return view('sidebar', ['class' => $class]);
    });
})->add(new LCM());

/*
 |--------------------------------------------------------------------------
 | EDIT
 |--------------------------------------------------------------------------
 */

$app->group('/edit', function () {

    /*
     * Display FAQ page regarding the editing of classes and runners
     */
    $this->get('/help', function ($request, $response) {
        // /edit/help
    });

    /*
     * Display available data of a runner, with the ability to change it
     * NOT ROUNDS!
     */
    $this->get('/runner/{id}', function ($request, $response, $id) {
        // /edit/runner/{id}
	    requires_permission('editRunner');
	    // get runner data
	    $runner = db_prepared_query(
	    	'SELECT r.*, g.name as class_name FROM runners as r, groups as g WHERE r.id = :id and r.class = g.id',
		    [':id' => $id]
	    )->fetch();
	    if(empty($runner)) {
		    return app('notFoundHandler')($request, $response);
	    }
		// get all group names (and ids)
		$stmt = app('database')->query('SELECT id, name FROM groups');
		$groups = $stmt->fetchAll();
		// get donor data
		$donors = db_prepared_query(
			'SELECT id, name FROM donors WHERE runner_id = :id',
			[':id' => $id]
			)->fetchAll();
        return view('edit.runner', [
        	'id' => $id,
	        'runner' => $runner,
	        'groups' => $groups,
	        'donors' => $donors
        ]);
    });

    /*
     * Update the runner's data
     */
    $this->post('/runner/{id}', function ($request, $response, $id) {
        // POST: /edit/runner/{id}
	    requires_permission('editRunner');
	    $params = $request->getParams();
	    if(isset($params['name'], $params['group'])) {
	    	$group_data = db_prepared_query('SELECT id FROM groups WHERE id = :group',
			    [':group' => htmlspecialchars($params['group'])])->fetch(); // make sure the group exists
	    	if(isset($group_data['id'])) {
			    db_prepared_query(
				    'UPDATE runners SET name = :name, class = :class WHERE id = :id',
				    [
				    	':name' => htmlspecialchars($params['name']), ':class' => $group_data['id'], ':id' => $id]
			    );
		    }
	    }
	    if(isset($request->getParams()['doDelete'])) {
		    $response = app()->subRequest('DELETE', '/edit/runner/'.$id);
		    return $response;
	    }
	    return redirect("/edit/runner/$id");
    });

	/**
	 * Deletes a runner
	 */
    $this->delete('/runner/{id}', function ($request, $response, $id) {
		requires_permission('editRunner');
		$group = db_prepared_query('SELECT g.name FROM groups as g, runners as r WHERE r.class = g.id AND r.id = :id',
			[':id' => $id])->fetch();
		if(isset($group['name'])) {
			$success = db_prepared_query('DELETE FROM runners WHERE id = :id',
				[':id' => $id]);
			if($success) {
				return redirect('/manage/'.$group['name']);
			}
		}
		echo 'something went wrong.';
		exit();
    });

    /*
     * Display data of a class, with the ability to change it
     */
    $this->get('/class/{class}', function ($request, $response, $class) {
        // /edit/class/{class}
	    requires_permission('editClass');
	    // make sure the group exists
	    $group = db_prepared_query(
		    'SELECT name FROM groups WHERE name = :name',
		    [':name' => $class]
	    )->fetch();
	    if(empty($group)) {
		    return app('notFoundHandler')($request, $response);
	    }
	    return view('edit.class', ['class' => $class]);
    });

    /*
     * Update class data
     */
    $this->post('/class/{class}', function ($request, $response, $class) {
        // POST: /edit/class/{class}
	    requires_permission('editClass');

	    if(!isset($request->getParams()['name']) || empty($request->getParam('name'))) {
	    	if(isset($request->getParams()['doDelete'])) {
	    		$response = app()->subRequest('DELETE', '/edit/class/'.$class);
	    		return $response;
		    }
		    return redirect('/manage/'.$class);
	    }
	    db_prepared_query('UPDATE groups SET name = :new_name WHERE name = :old_name',
		    [':new_name' => htmlspecialchars($request->getParam('name')), ':old_name' => $class]);
	    return redirect('/manage/'.urlencode($request->getParam('name')));
    });

    /**
     * Delete a class
     */
    $this->delete('/class/{class}', function ($request, $response, $class) {
    	requires_permission('editClass');
    	$runners = db_prepared_query('SELECT r.id FROM runners as r, groups as g WHERE g.name = :name AND r.class = g.id',
		    [':name' => htmlspecialchars($class)])->fetch();
    	if(!empty($runners)) {
    		echo 'This group still has runners. Please move them first.<br>';
    		echo '<a href="/edit/class/'.$class.'">back</a>';
    		exit();
	    }
	    $success = db_prepared_query('DELETE FROM groups WHERE name = :name',
		    [':name' => htmlspecialchars($class)]);
        if($success) {
			return redirect('/manage');
        }
        echo 'something went wrong.';
        exit();
    });

    /*
     * Display data of a donor, with the ability to change it
     */
    $this->get('/donor/{id}', function ($request, $response, $id) {
        // /edit/donor/{id}
	    requires_permission('editDonor');
	    $donor = db_prepared_query(
		    'SELECT d.*, r.name as runner_name, g.name as runner_class FROM donors as d, runners as r, groups as g WHERE d.id = :id AND d.runner_id = r.id AND r.class = g.id',
		    [':id' => $id]
	    )->fetch();
	    if(empty($donor)) {
		    return app('notFoundHandler')($request, $response);
	    }
        return view('edit.donor', ['id' => $id, 'donor' => $donor]);
    });

    /*
    * Update donor data
    */
    $this->post('/donor/{id}', function ($request, $response, $id) {
        // POST: /edit/donor/{id}
	    requires_permission('editDonor');
	    extract($request->getParams());
	    if(isset($name, $donation, $amountIsFixed, $wantsReceipt, $runner_id)
	       && preg_match('/^[0-9]+((\.|\,)[0-9]{1,2})?$/', $donation)
	       && ($amountIsFixed === "1" || $amountIsFixed === "0")
	       && ($wantsReceipt === "1" || $wantsReceipt === "0")
	       && is_numeric($runner_id)) {
		    $name = htmlspecialchars($name);
			db_prepared_query(
				'UPDATE donors SET name = :name, donation = :donation, amountIsFixed = :amountIsFixed, wantsReceipt = :wantsReceipt WHERE id = :id',
				[':name' => $name, ':donation' => $donation, ':amountIsFixed' => $amountIsFixed, ':wantsReceipt' => $wantsReceipt, ':id' => $id]
			);
	    }
	    return redirect("/edit/donor/$id");
    });
});


/*
 |--------------------------------------------------------------------------
 | ADD
 |--------------------------------------------------------------------------
 */

$app->group('/add', function () {

	/*
	 * Displays a form to add a new donor
	 */
	$this->get( '/donor/{runner_id}', function ( $request, $response, $runner_id ) {
		// /add/donor/{runner_id}
		requires_permission('addDonor');
		$runner = db_prepared_query(
			'SELECT r.name, g.name as runner_class FROM runners as r, groups as g WHERE r.class = g.id AND r.id = :id',
			[':id' => $runner_id]
		)->fetch();
		if(empty($runner)) {
			return app('notFoundHandler')($request, $response);
		}
		return view('add.donor', ['runner_id' => $runner_id, 'runner' => $runner]);
	});

	/**
	 * Adds the new donor to the database
	 */
	$this->post('/donor', function ($request, $response) {
	    // /add/donor
		requires_permission('addDonor');
		extract($request->getParams());
		if(isset($name, $donation, $amountIsFixed, $wantsReceipt, $runner_id)
		   && preg_match('/^[0-9]+((\.|\,)[0-9]{1,2})?$/', $donation)
		   && ($amountIsFixed === "1" || $amountIsFixed === "0")
		   && ($wantsReceipt === "1" || $wantsReceipt === "0")
		   && is_numeric($runner_id)) {
			$runner_existence_check = db_prepared_query(
				'SELECT id FROM runners WHERE id = :id',
				[':id' => $runner_id])->fetch();
			if(empty($runner_existence_check)) {
				return app('notFoundHandler')($request, $response);
			}
			$name = htmlspecialchars($name);
			$insert_operation = db_prepared_query( 'INSERT INTO donors (name, donation, amountIsFixed, wantsReceipt, runner_id) VALUES (:name, :donation, :amountIsFixed, :wantsReceipt, :runner_id)',
				[ ':name' => $name, ':donation' => $donation, ':amountIsFixed' => $amountIsFixed, ':wantsReceipt' => $wantsReceipt, ':runner_id' => $runner_id ] );
			if($insert_operation) {
				return redirect('/edit/runner/'.$runner_id);
			}
		}

		echo 'something went wrong.';
		exit();
	});

	/*
    * Displays a form to add a new runner
    */
	$this->get( '/runner/{class}', function ( $request, $response, $class ) {
		// /add/runner/{class}
		requires_permission('addRunner');
		$class_id = db_prepared_query(
			'SELECT id FROM groups WHERE name = :class',
			[':class' => $class]
		)->fetch();
		if(!isset($class_id['id'])) {
			return app('notFoundHandler')($request, $response);
		}
		$groups = app('database')->query('SELECT id, name FROM groups')->fetchAll();
		return view('add.runner', ['class' => $class, 'groups' => $groups, 'class_id' => $class_id['id']]);
	});

	/**
	 * Saves the new runner to the database
	 */
	$this->post('/runner', function ($request, $response) {
	    // /add/runner
		requires_permission('addRunner');
		extract($request->getParams()); // creates $name & $group (if present)
		$group_id = db_prepared_query('SELECT id FROM groups WHERE name = :group_name',
			[':group_name' => $group ?? ''])->fetch();
		if(isset($name, $group_id['id'])) {
			$success = db_prepared_query('INSERT INTO runners (name, class) VALUES (:name, :group_id)',
				[':name' => htmlspecialchars($name), ':group_id' => $group_id['id']]);
			if($success) {
				return redirect('/edit/runner/'.app('database')->lastInsertId());
			}
		}
		echo 'something went wrong.';
		exit();
	});

	/**
	 * Displays a form to add a new class
	 */
	$this->get('/class', function ($request, $response) {
		requires_permission('addClass');
	    return view('add.class');
	});

	/**
	 * Adds the class to the database
	 */
	$this->post('/class', function ($request) {
		requires_permission('addClass');
		extract($request->getParams());
	    if(isset($name)) {
	    	$existence_check = db_prepared_query('SELECT id FROM groups WHERE name = :name',
			    [':name' => htmlspecialchars($name)])->fetch();
	    	if(!empty($existence_check)) {
	    		echo trans('static.group_already_exists');
	    		exit();
		    }
			$insert_operation = db_prepared_query('INSERT INTO groups (name) VALUES (:name)',
				[':name' => htmlspecialchars($name)]);
			if($insert_operation) {
				return redirect('/manage/'.urlencode($name));
			}
	    }
	    echo 'something went wrong.';
	    exit();
	});
});