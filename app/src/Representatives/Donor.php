<?php

namespace App\Representatives;

use MichaelSpiss\Representatives\Representative;

class Donor extends SQLITE_DEPENDANT {

    /**
     * Returns an associative array of all attributes and their data types.
     * @return array
     */
    protected function initAttributes()
    {
        return [
            'name' => Representative::string,
            'runner-id' => Representative::integer,
            'donation' => Representative::float,
            'amountIsFixed' => Representative::integer,
            'wantsReceipt' => Representative::integer
        ];
    }

    /**
     * Returns the SQLite query to run on deletion. Must have an :id parameter.
     * @return string
     */
    protected function getDeleteQuery()
    {
        return 'DELETE FROM donors WHERE id = :id';
    }

    /**
     * Returns the SQLite query to run on read. Must have an :id parameter.
     * @param string $attribute_names_string
     * @return string
     */
    protected function getReadQuery($attribute_names_string)
    {
        return 'SELECT "'.$attribute_names_string.'" FROM donors WHERE id = :id';
    }

    /**
     * Returns the SQLite query to run on write. Must have an :id parameter.
     * @param string $update_string
     * @return string
     */
    protected function getWriteQuery($update_string)
    {
        return 'UPDATE donors SET '.$update_string.' WHERE id = :id';
    }

    /**
     * Returns overview-data about the runner's donors
     * @param $id integer the runners id
     * @return array with all ids and names of the runner's donors
     */
    public static function getAllForRunner($id) {
        /** @var \PDOStatement $stmt */
        $stmt = app('database')->prepare('SELECT name, id FROM donors WHERE `runner-id` = :id');
        $stmt->bindParam(':id', $id);
        if($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }
}