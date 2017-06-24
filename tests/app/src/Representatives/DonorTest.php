<?php

class DonorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \PDO $pdo
     */
    protected static $pdo = null;
    /**
     * @var \App\Representatives\Donor $donor
     */
    protected static $donor = null;
    /**
     * @var mixed $id
     */
    protected static $id = null;

    public static function setUpBeforeClass() {
        // set up database connection
        self::$pdo = app('database');
        // create new Donor
        self::$pdo->query("INSERT INTO donors (name, \"runner-id\", donation, amountIsFixed, wantsReceipt) VALUES ('Foo', 1, 0.00, 0, 0)");
        self::$id = self::$pdo->lastInsertId();
    }

    public function setUp()
    {
        $this::$donor = new \App\Representatives\Donor(self::$id);
    }

    public function testDonorReturnsCorrectID()
    {
        $this->assertEquals(self::$id, $this::$donor->getId());
    }

    public function testDonorSetsSingleValueCorrectly() {
        $this::$donor->set('name', 'FooBar');
        $this->assertEquals('FooBar', $this::$donor->get('name'));
    }

    public function testDonorGetsSingleValueCorrectly() {
        $actual = $this::$donor->get('donation');
        $this->assertEquals(0.00, $actual);
    }

    public static function tearDownAfterClass()
    {
        self::$donor->delete();
    }
}