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
$app->get('/', function ($request, $response) {
    // /
    return view('home');
});

/*
 * Display the live map, with statistics about the run
 */
$app->get('/map', function ($request, $response) {
    // /map
});

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
$app->get('/login', function ($request, $response) {
    // /login
    return view('login');
});

/*
 * If password is correct authenticate user and redirect to /manage,
 * else send back to login
 */
$app->post('/login', function ($request, $response) {
    // POST: /login
    // TODO: password verification, user authentication
    return redirect('/manage');
});

/*
 * Log the user out, redirect to index
 */
$app->get('/logout', function ($request, $response) {
    // /logout
    // log out user
});

/*
|--------------------------------------------------------------------------
| MANAGE
|--------------------------------------------------------------------------
*/

/*
 * Display a list of all classes to choose from
 */
$app->get('/manage', function ($request, $response) {
    // /manage
    return view('select_class');
});

/*
 * Get class selection, redirect to /manage/{class},
 * where class is the selected class.
 * This will only be called if JavaScript is disabled
 */
$app->post('/manage', function ($request, $response) {
    // POST: /manage
    $class = $request->getParam('class');
    return redirect('/manage/'.$class);
});

/*
 * Display a FAQ page regarding the management of classes
 */
$app->get('/manage/help', function ($request, $response) {
    // /manage/help
});

$app->group('/manage/{class}', function () {

    /*
    * Display an overview of the selected class (runners, finished rounds)
    */
    $this->get('', function ($request, $response, $class) {
        // /manage/{class}
        return view('manage.overview', ['class' => $class]);
    });

    /*
    * Interface to add rounds to the runners
    */
    $this->get('/add', function ($request, $response, $class) {
        // /manage/{class}/add
        return view('manage.add', ['class' => $class]);
    });

    /*
     * Add rounds to the runners
     */
    $this->post('/add', function ($request, $response, $class) {
        // POST: /manage/{class}/add
    });

    /*
     * Display log of all recent updates of the runners rounds
     */
    $this->get('/log', function ($request, $response, $class) {
        // /manage/{class}/log
    });

    /*
    * Undo round updates, Undo undos
    */
    $this->post('/log', function ($request, $response, $class) {
        // POST: /manage/{class}/log
    });

    /*
    * Display sidebar, from "more"
    * This will only be called if JavaScript is disabled
    */
    $this->get('/more', function ($request, $response, $class) {
        // /manage/{class}/more
        return view('sidebar', ['class' => $class]);
    });
});

/*
 |--------------------------------------------------------------------------
 | EDIT
 |--------------------------------------------------------------------------
 */

$app->group('/edit', function () {
    /*
     * Display a list of all classes to choose from
     */
    $this->get('', function ($request, $response) {
        // /edit
    });

    /*
     * Get class selection, redirect to /edit/{class},
     * where class is the selected class.
     * This will only be called if JavaScript is disabled
    */
    $this->post('/', function ($request, $response) {
        // POST: /edit
        // Redirect the user to the selected class
    });

    /*
     * Display FAQ page regarding the editing of classes and runners
     */
    $this->get('/help', function ($request, $response) {
        // /edit/help
    });

    /*
     * Display an overview of the runners in a class
     */
    $this->get('/{class}', function ($request, $response, $class) {
        // /edit/{class}
    });

    /*
     * Display available data of a runner, with the ability to change it
     * NOT ROUNDS!
     */
    $this->get('/runner/{id}', function ($request, $response, $id) {
        // /edit/runner/{id}
    });

    /*
     * Update the runner's data
     */
    $this->post('/runner/{id}', function ($request, $response, $id) {
        // POST: /edit/runner/{id}
        // save new runner data
    });

    /*
     * Display data of a class, with the ability to change it
     */
    $this->get('/class/{class}', function ($request, $response, $class) {
        // /edit/class/{class}
    });

    /*
     * Update class data
     */
    $this->post('/class/{class}', function ($request, $response, $class) {
        // POST: /edit/class/{class}
        // save new class data
    });
});