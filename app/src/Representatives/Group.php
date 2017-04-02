<?php

namespace App\Representatives;

use MichaelSpiss\Representatives\Representative;

// sorry, Class doesn't work
class Group extends SQLITE_DEPENDANT {

    /**
     * Returns an associative array of all attributes and their data types.
     * @return array
     */
    protected function initAttributes()
    {
        return [
            'name' => Representative::string
        ];
    }

    /**
     * Returns the SQLite query to run on deletion. Must have an :id parameter.
     * @return string
     */
    protected function getDeleteQuery()
    {
        return 'DELETE FROM classes WHERE id = :id';
    }

    /**
     * Returns the SQLite query to run on read. Must have an :id parameter.
     * @param string $attribute_names_string
     * @return string
     */
    protected function getReadQuery($attribute_names_string)
    {
        return 'SELECT "'.$attribute_names_string.'" FROM classes WHERE id = :id';
    }

    /**
     * Returns the SQLite query to run on write. Must have an :id parameter.
     * @param string $update_string
     * @return string
     */
    protected function getWriteQuery($update_string)
    {
        return 'UPDATE classes SET '.$update_string.' WHERE id = :id';
    }
}