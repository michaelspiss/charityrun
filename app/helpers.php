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
    function view($view, $args = []) {
        // allow dot notation
        $view = implode(DIRECTORY_SEPARATOR, explode('.', $view)).'.phtml';
        return app('renderer')->render(app('response'), $view, $args);
    }
}

/**
 * Returns the base url
 */
if(!function_exists('base_url')) {
    function base_url() {
        /** @var \Psr\Http\Message\UriInterface $uri */
        $uri = app('request')->getUri();
        // if port is needed append it
        $port = ':'.$uri->getPort();
        if($port == ':80' || $port == ':443') {
            $port = '';
        }

        return $uri->getScheme().'://'.$uri->getHost().$port;
    }
}

/**
 * Returns a redirect response
 */
if(!function_exists('redirect')) {
    function redirect($path) {
        return app('response')->withRedirect($path);
    }
}