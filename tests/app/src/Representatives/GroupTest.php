<?php

class GroupTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \PDO $pdo
     */
    protected static $pdo = null;
    /**
     * @var \App\Representatives\Group $group
     */
    protected static $group = null;
    /**
     * @var mixed $id
     */
    protected static $id = null;

    public static function setUpBeforeClass() {
        // set up database connection
        self::$pdo = app('database');
        // create new runner
        self::$pdo->query("INSERT INTO classes (name) VALUES (00)");
        self::$id = self::$pdo->lastInsertId();
    }

    public function setUp()
    {
        $this::$group = new \App\Representatives\Group(self::$id);
    }

    public function testGroupReturnsCorrectID()
    {
        $this->assertEquals(self::$id, $this::$group->getId());
    }

    public function testGroupSetsSingleValueCorrectly() {
        $this::$group->set('name', '01');
        $this->assertEquals('01', $this::$group->get('name'));
    }

    public static function tearDownAfterClass()
    {
        self::$group->delete();
    }
}