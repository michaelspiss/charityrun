<?php

namespace App\Representatives;

use MichaelSpiss\Representatives\Representative;

class User extends SQLITE_DEPENDANT {

    /**
     * Returns an associative array of all attributes and their data types.
     * @return array
     */
    protected function initAttributes()
    {
        return [
            'username' => Representative::string,
            'password' => Representative::string
        ];
    }

    /**
     * Returns the SQLite query to run on deletion. Must have an :id parameter.
     * @return string
     */
    protected function getDeleteQuery()
    {
        return 'DELETE FROM users WHERE id = :id';
    }

    /**
     * Returns the SQLite query to run on read. Must have an :id parameter.
     * @param string $attribute_names_string
     * @return string
     */
    protected function getReadQuery($attribute_names_string)
    {
        return 'SELECT "'.$attribute_names_string.'" FROM users WHERE id = :id';
    }

    /**
     * Returns the SQLite query to run on write. Must have an :id parameter.
     * @param string $update_string
     * @return string
     */
    protected function getWriteQuery($update_string)
    {
        return 'UPDATE users SET '.$update_string.' WHERE id = :id';
    }
}