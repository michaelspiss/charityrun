<?php

namespace App\Representatives;

use MichaelSpiss\Representatives\Representative;

abstract class SQLITE_DEPENDANT extends Representative {

    /**
     * Returns the SQLite query to run on deletion. Must have an :id parameter.
     * @return string
     */
    abstract protected function getDeleteQuery();

    /**
     * Returns the SQLite query to run on read. Must have an :id parameter.
     * @param string $attribute_names_string
     * @return string
     */
    abstract protected function getReadQuery($attribute_names_string);

    /**
     * Returns the SQLite query to run on write. Must have an :id parameter.
     * @param string $update_string
     * @return string
     */
    abstract protected function getWriteQuery($update_string);
    /**
     * Deletes the Representative's data set from the storage.
     * To disable this functionality simply put a "return false;" here.
     * @param mixed $id
     * @return bool true on success, false otherwise
     */
    protected function deleteDataset($id)
    {
        /** @var \PDO $db */
        $db = app('database');
        $stmt = $db->prepare($this->getDeleteQuery());
        $stmt->bindParam(':id', $id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Reads all requested attribute values from the storage
     * @param mixed $id the representative's identifier
     * @param array $attribute_names
     * @return array|bool associative array [attribute name => value], false if read failed
     */
    protected function readValues($id, $attribute_names)
    {
        $attribute_names = implode('", "', $attribute_names);
        /** @var \PDO $db */
        $db = app('database');
        $stmt = $db->prepare($this->getReadQuery($attribute_names));
        $stmt->bindParam(':id', $id);
        if($stmt->execute()) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Writes updated values to the storage
     * All attributes are expected to exist, All values are expected to have a valid data type
     * @param mixed $id the representative's identifier
     * @param array $attribute_names_values associative array [attribute name => value]
     * @return bool true on success, false otherwise
     */
    protected function writeValues($id, $attribute_names_values)
    {
        $update_string = '';
        $length = count($attribute_names_values);
        // build update string
        $i = 0;
        foreach($attribute_names_values as $attribute_name => $value) {
            $update_string .= $attribute_name.'=\''.$value.'\'';
            // make sure the last element won't get a comma
            if($i < $length-1) {
                $update_string .= ', ';
            }
            $i++;
        }

        /** @var \PDO $db */
        $db = app('database');
        $stmt = $db->prepare($this->getWriteQuery($update_string));
        $stmt->bindParam(':id', $id);
        if($stmt->execute()) {
            return true;
        }
        return false;

    }
}