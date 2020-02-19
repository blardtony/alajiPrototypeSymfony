<?php
namespace App\Tests;

use App\Api\TeacherApi;

class AcquisTest extends \Codeception\Test\Unit
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
    public function testAcquis(TeacherApi $teacherApi)
    {
        $acquis = $teacherApi->acquis(0.5);
        $this->assertEquals("Acquis", $acquis);
    }
}
