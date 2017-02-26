<?php

/**
 * Returns a service from the container
 */
if(!function_exists('app')) {
    function app($service) {
        global $container;
        return $container[$service];
    }
}

/**
 * Returns the translation for a key
 */
if(!function_exists('trans')) {
    function trans($key) {
        return app('translator')->trans($key);
    }
}

/**
 * Returns a view with the given parameters
 */
if(!function_exists('view')) {
    function view($response, $view, $args = []) {
        // allow dot notation
        $view = implode(DIRECTORY_SEPARATOR, explode('.', $view)).'.phtml';
        return app('renderer')->render($response, $view, $args);
    }
}