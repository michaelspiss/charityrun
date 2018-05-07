<?php

/**
 * Returns a service from the container
 */
if(!function_exists('app')) {
    function app($service = '') {
        if($service == '') {
        	global $app;
        	return $app;
        }
	    global $container;
        return $container[$service];
    }
}

/**
 * Returns the translation for a key
 */
if(!function_exists('trans')) {
    function trans($key, $replace = []) {
        return app('translator')->trans($key, $replace);
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

if(!function_exists('db_prepared_query')) {
	/**
	 * Executes a prepared sql query with the given parameters.
	 * @param string $query
	 * @param array  $params as key => value pairs
	 * @param null   $execute_return_value sets the given variable to
	 * the execute's return value
	 * @return PDOStatement
	 */
	function db_prepared_query(string $query, array $params, &$execute_return_value = null) {
		/** @var PDOStatement $stmt */
		$stmt = app('database')->prepare($query);
		if(is_null($execute_return_value)) {
			$stmt->execute($params);
		} else {
			$execute_return_value = $stmt->execute($params);
		}
		return $stmt;
	}
}

if(!function_exists('requires_permission')) {
	function requires_permission(string $permission) {
		if(!app('auth')->can($permission)) {
			echo 'You don\'t have permission to do this';
			exit();
		}
	}
}

if(!function_exists('preferred_language')) {
	/**
	 * Figures the user's preferred language out
	 * @param string $default_language fallback if nothing matches
	 * @param array $available_languages
	 * @param string $http_accept_language $_SERVER['HTTP_ACCEPT_LANGUAGE']
	 * @return string
	 */
	function preferred_language($default_language, $available_languages, $http_accept_language) {
		$available_languages = array_flip($available_languages);
		$langs = array();
		preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			list($a, $b) = explode('-', $match[1]) + array('', '');
			$value = isset($match[2]) ? (float) $match[2] : 1.0;
			if(isset($available_languages[$match[1]])) {
				$langs[$match[1]] = $value;
				continue;
			}
			if(isset($available_languages[$a])) {
				$langs[$a] = $value - 0.1;
			}
		}
		if($langs) {
			arsort($langs);
			return key($langs); // We don't need the whole array of choices since we have a match
		} else {
			return $default_language;
		}
	}
}