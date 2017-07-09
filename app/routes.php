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
    return view('map');
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
$app->get('/login', function ($request) {
    // /login

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
    return view('select_class');
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
        return view('manage.log', ['class' => $class]);
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
        return view('edit.runner', ['id' => $id]);
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
        return view('edit.class', ['class' => $class]);
    });

    /*
     * Update class data
     */
    $this->post('/class/{class}', function ($request, $response, $class) {
        // POST: /edit/class/{class}
        // save new class data
    });

    /*
     * Display data of a donor, with the ability to change it
     */
    $this->get('/donor/{id}', function ($request, $response, $id) {
        // /edit/donor/{id}
        return view('edit.donor');
    });

    /*
    * Update donor data
    */
    $this->post('/donor/{id}', function ($request, $response, $class) {
        // POST: /edit/donor/{id}
        // save new donor data
    });
});