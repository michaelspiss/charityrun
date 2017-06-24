<?php

namespace App\Representatives;

use MichaelSpiss\Representatives\Representative;

/**
 * Class Classes
 * @package App\Representatives
 */
class Classes extends SQLITE_DEPENDANT {
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
        return 'UPDATE classes SET "'.$update_string.'" WHERE id = :id';
    }

    /**
     * Returns all available class names
     * @return mixed
     */
    public static function getAllNames() {
        return app('database')->query('SELECT name FROM classes')->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Returns the Ids of all runners in a class
     * @param $className
     * @return array
     */
    public static function getAllClassMembers($className) {
        /** @var \PDOStatement $stmt */
        $stmt = app('database')->prepare('SELECT id FROM runners WHERE class = :className');
        $stmt->bindParam(':className', $className);
        if($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }
}