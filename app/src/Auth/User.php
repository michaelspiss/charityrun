<?php

namespace App\Auth;

use Solution10\Auth\UserRepresentation;

class User implements UserRepresentation {

    protected $id;
    protected $username;

    function __construct($id, $username)
    {
        $this->id = $id;
        $this->username = $username;
    }

    /**
     * Returns the ID of this user. Doesn't matter what type it returns.
     * @return    mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Returns the user's username
     * @return string
     */
    public function username() {
        return $this->username;
    }
}