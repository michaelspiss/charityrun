<?php

namespace App\Auth;

class LoginCheckMiddleware {
    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next) {
        // check if user is not logged in
        if(!app('auth')->loggedIn()) {
            // redirect returns a response, so this is fine
            return redirect('/login');
        }
        // else just keep going
        $response = $next($request, $response);
        return $response;
    }
}