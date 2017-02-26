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
         $stmt = $this->db->prepare('INSERT INTO user_packages (user_id, package) VALUES (:id, :package)');
         $stmt->bindParam('id', $user->id());
         $stmt->bindParam('package', $package->name());
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
         // TODO: Implement authRemovePackageFromUser() method.
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
         // TODO: Implement authFetchPackagesForUser() method.
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
         // TODO: Implement authUserHasPackage() method.
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
         // TODO: Implement authOverridePermissionForUser() method.
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
         // TODO: Implement authFetchOverridesForUser() method.
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
         // TODO: Implement authRemoveOverrideForUser() method.
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
         // TODO: Implement authResetOverridesForUser() method.
     }
 }