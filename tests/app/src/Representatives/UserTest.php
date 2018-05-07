<?php

class UserTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \PDO $pdo
     */
    protected static $pdo = null;
    /**
     * @var \App\Representatives\User $user
     */
    protected static $user = null;
    /**
     * @var mixed $id
     */
    protected static $id = null;

    public static function setUpBeforeClass() {
        // set up database connection
        self::$pdo = app('database');
        // create new User
        self::$pdo->query("INSERT INTO users (username, password) VALUES ('foo', 'encrypted')");
        self::$id = self::$pdo->lastInsertId();
    }

    public function setUp()
    {
        $this::$user = new \App\Representatives\User(self::$id);
    }

    public function testUserReturnsCorrectID()
    {
        $this->assertEquals(self::$id, $this::$user->getId());
    }

    public function testUserSetsSingleValueCorrectly() {
        $this::$user->set('username', 'FooBar');
        $this->assertEquals('FooBar', $this::$user->get('username'));
    }

    public function testUserGetsSingleValueCorrectly() {
        $actual = $this::$user->get('password');
        $this->assertEquals('encrypted', $actual);
    }

    public static function tearDownAfterClass()
    {
        self::$user->delete();
    }
}