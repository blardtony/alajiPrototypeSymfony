<?php
namespace App\Tests;

use App\Api\TeacherApi;

class AverageTest extends \Codeception\Test\Unit
{
    /**
     * @var \App\Tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testAverage()
    {
        $average = averageCriteria(1, 0.77, 0, 0.23);
        $this->assertEquals(0.77, $average);
    }
}
