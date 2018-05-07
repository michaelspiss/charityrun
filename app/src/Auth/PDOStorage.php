<?php

namespace App\Auth;

 use Solution10\Auth\Package;
 use Solution10\Auth\StorageDelegate;
 use Solution10\Auth\UserRepresentation;

 class PDOStorage implements StorageDelegate {

     /**
      * A pdo database connection
      * @var \PDO
      */
     protected $db;

     public function __construct(\PDO $pdo)
     {
         $this->db = $pdo;
     }

     /**
      * Fetches a user by their username. This function should return either an
      * array containing:
      *  - id: the unique identifier for this user
      *  - username: the username we just looked up
      *  - password: the hashed version of the users password.
      * If it's a success, or false if there's no user by that name
      *
      * @param   string $instance_name Instance name
      * @param   string $username Username to search for
      * @return  array|bool
      */
     public function authFetchUserByUsername($instance_name, $username)
     {
         $stmt = $this->db->prepare('SELECT id, username, password FROM users WHERE username = :username');
         $stmt->bindParam('username', $username);
         $stmt->execute();
         // return associative array
         return $stmt->fetch(\PDO::FETCH_ASSOC);
     }

     /**
      * Fetches the full user representation of a given ID. ie your active record
      * instance or the like.
      *
      * @param   string $instance_name Instance name
      * @param   int    $user_id ID of the logged in user
      * @return  UserRepresentation      The representation you return must implement the UserRepresentation interface.
      */
     public function authFetchUserRepresentation($instance_name, $user_id)
     {
         // fetch data
         $stmt = $this->db->prepare('SELECT id, username FROM users WHERE id = :id');
         $stmt->bindParam('id', $user_id);
         $stmt->execute();
         $user = $stmt->fetch();

         return new User($user['id'], $user['username']);
     }

     /**
      * Adding a package to a given user.
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user User representation (taken from authFetchUserRepresentation)
      * @param   Package            $package Package to add.
      * @return  bool
      */
     public function authAddPackageToUser($instance_name, UserRepresentation $user, Package $package)
     {
         $id = $user->id();
         $package_name = $package->name();

         $stmt = $this->db->prepare('INSERT INTO user_packages (user_id, package) VALUES (:id, :package)');
         $stmt->bindParam('id', $id);
         $stmt->bindParam('package', $package_name);
         return $stmt->execute();
     }

     /**
      * Removing a package from a given user.
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user User representation (taken from authFetchUserRepresentation)
      * @param   Package            $package Package to remove.
      * @return  bool
      */
     public function authRemovePackageFromUser($instance_name, UserRepresentation $user, Package $package)
     {
         $id = $user->id();
         $package_name = $package->name();

         $stmt = $this->db->prepare('DELETE FROM user_packages WHERE package = :package AND user_id = :id');
         $stmt->bindParam('id', $id);
         $stmt->bindParam('package', $package_name);
         return $stmt->execute();
     }

     /**
      * Fetching all packages for a user
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user User representation (taken from authFetchUserRepresentation)
      * @return  array
      */
     public function authFetchPackagesForUser($instance_name, UserRepresentation $user)
     {
         $id = $user->id();

         $stmt = $this->db->prepare('SELECT package FROM user_packages WHERE user_id = :id');
         $stmt->bindParam('id', $id);
         $stmt->execute();
         $result = $stmt->fetch(\PDO::FETCH_ASSOC);
         if($result === false) {
             return [];
         }
         return $result;
     }

     /**
      * Returns whether a user has a given package or not.
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user User representation
      * @param   Package            $package Package to check for
      * @return  bool
      */
     public function authUserHasPackage($instance_name, UserRepresentation $user, Package $package)
     {
         $id = $user->id();
         $package_name = $package->name();

         $stmt = $this->db->prepare('SELECT id FROM user_packages WHERE user_id = :id AND package = :package');
         $stmt->bindParam('id', $id);
         $stmt->bindParam('package', $package_name);
         $stmt->execute();
         // true if data was received, false if not
         return (bool) $stmt->fetch();
     }

     /**
      * Stores an overridden permission for a user
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user
      * @param   string             $permission Permission name
      * @param   bool               $new_value New value
      * @return  bool
      */
     public function authOverridePermissionForUser(
         $instance_name,
         UserRepresentation $user,
         $permission,
         $new_value
     )
     {
         $id = $user->id();

         $stmt = $this->db->prepare('INSERT INTO user_overrides (user_id, permission, value) VALUES (:id, :permission, :value)');
         $stmt->bindParam('id', $id);
         $stmt->bindParam('permission', $permission);
         // Make sure values are booleans
         $new_value = (bool) $new_value;
         $stmt->bindParam('value', $new_value);
         return $stmt->execute();
     }

     /**
      * Fetches all the permission overrides for a given user.
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user
      * @return  array   An array of permission => (bool) values
      */
     public function authFetchOverridesForUser($instance_name, UserRepresentation $user)
     {
         $id = $user->id();

         $stmt = $this->db->prepare('SELECT permission, value FROM user_overrides WHERE user_id = :id');
         $stmt->bindParam('id', $id);
         $stmt->execute();

         // if no overrides exist, return empty array
         $overrides = $stmt->fetch(\PDO::FETCH_KEY_PAIR);
         if($overrides) {
             return $overrides;
         }
         return [];
     }

     /**
      * Removes a specific override from a given user
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user
      * @param   string             $permission Permission override to remove
      * @return  bool
      */
     public function authRemoveOverrideForUser($instance_name, UserRepresentation $user, $permission)
     {
         $id = $user->id();

         $stmt = $this->db->prepare('DELETE FROM user_overrides WHERE user_id = :id AND permission = :permission');
         $stmt->bindParam('id', $id);
         $stmt->bindParam('permission', $permission);
         return $stmt->execute();
     }

     /**
      * Removes all the overrides for a given user.
      *
      * @param   string             $instance_name Auth instance name
      * @param   UserRepresentation $user
      * @return  bool
      */
     public function authResetOverridesForUser($instance_name, UserRepresentation $user)
     {
         $id = $user->id();

         $stmt = $this->db->prepare('DELETE FROM user_overrides WHERE user_id = :id');
         $stmt->bindParam('id', $id);
         return $stmt->execute();
     }
 }