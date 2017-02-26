<?php

namespace tests\app\src\Auth;

use App\Auth\PDOStorage;

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
}
