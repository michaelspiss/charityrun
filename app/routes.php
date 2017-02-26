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