<?php

namespace tests\app\src\Auth;

use App\Auth\PDOStorage;
use App\Auth\User;

class PDOStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PDOStorage
     */
    protected $PDOStorage;

    /**
     * @var \PDO
     */
    protected $db;

    public function __construct()
    {
        $this->db = new \PDO('sqlite:../../../../storage/database/database.sqlite');
    }

    public function setUp()
    {
        $this->PDOStorage = new PDOStorage($this->db);
    }

    public function testAuthFetchUserByUsernameReturnsFalseIfNotFound() {
        $actual = $this->PDOStorage->authFetchUserByUsername('test', 'user');

        $this->assertFalse($actual);
    }

    public function testAuthFetchUserByUsernameReturnsDataIfFound() {
        $actual = $this->PDOStorage->authFetchUserByUsername('test', 'foo');

        $this->assertEquals(['id' => 1, 'username' => 'foo', 'password' => 'xyz'], $actual);
    }

    public function testAuthFetchUserRepresentationReturnsUserObjectOnSuccess() {
        $actual = $this->PDOStorage->authFetchUserRepresentation('test', 1);

        $expected = new User('1', 'foo');
        $this->assertEquals($expected, $actual);
    }
}
