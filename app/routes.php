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
});

/*
 * Display the live map, with statistics about the run
 */
$app->get('map', function ($request, $response) {
    // /map
});

/*
 * Display the current top-runners and overall statistics
 * "Leaderboard"
 */
$app->get('stats', function ($request, $response) {
    // /stats
});

/*
 * Display log in form, redirect if already logged in
 */
$app->get('login', function ($request, $response) {
    // /login
});

/*
 * If password is correct authenticate user and redirect to /manage,
 * else send back to login
 */
$app->post('login', function ($request, $response) {
    // POST: /login
    // password verification, user authentication
});

/*
 * Log the user out, redirect to index
 */
$app->get('logout', function ($request, $response) {
    // POST: /logout
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
$app->get('manage', function ($request, $response) {
    // /manage
});

/*
 * Get class selection, redirect to /manage/{class},
 * where class is the selected class.
 * This will only be called if JavaScript is disabled
 */
$app->post('manage', function ($request, $response) {
    // POST: /manage
});

/*
 * Display a FAQ page regarding the management of classes
 */
$app->get('manage/help', function ($request, $response) {
    // /manage/help
});

$app->group('manage/{class}', function () {

    /*
    * Display an overview of the selected class (runners, finished rounds)
    */
    $this->get('/', function ($request, $response, $class) {
        // /manage/{class}
    });

    /*
    * Interface to add rounds to the runners
    */
    $this->get('add', function ($request, $response, $class) {
        // /manage/{class}/add
    });

    /*
     * Add rounds to the runners
     */
    $this->post('add', function ($request, $response, $class) {
        // POST: /manage/{class}/add
    });

    /*
     * Display log of all recent updates of the runners rounds
     */
    $this->get('log', function ($request, $response, $class) {
        // /manage/{class}/log
    });

    /*
    * Undo round updates, Undo undos
    */
    $this->post('log', function ($request, $response, $class) {
        // POST: /manage/{class}/log
    });

    /*
    * Display sidebar, from "more"
    * This will only be called if JavaScript is disabled
    */
    $this->get('more', function ($request, $response, $class) {
        // /manage/{class}/more
    });
});