<?php

class RunnerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \PDO $pdo
     */
    protected static $pdo = null;
    /**
     * @var \App\Representatives\Runner $runner
     */
    protected static $runner = null;
    /**
     * @var mixed $id
     */
    protected static $id = null;

    public static function setUpBeforeClass() {
        // set up database connection
        self::$pdo = app('database');
        // create new runner
        self::$pdo->query("INSERT INTO runners (class, name) VALUES (1, 'Wooabadoo')");
        self::$id = self::$pdo->lastInsertId();
    }

    public function setUp()
    {
        $this::$runner = new \App\Representatives\Runner(self::$id);
    }

    public function testRunnerReturnsCorrectID()
    {
        $this->assertEquals(self::$id, $this::$runner->getId());
    }

    public function testRunnerSetsSingleValueCorrectly() {
        $this::$runner->set('name', 'FooBar');
        $this->assertEquals('FooBar', $this::$runner->get('name'));
    }

    public function testRunnerGetsSingleValueCorrectly() {
        $actual = $this::$runner->get('total-rounds');
        $this->assertEquals(0, $actual);
    }

    public static function tearDownAfterClass()
    {
        self::$runner->delete();
    }
}