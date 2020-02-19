<?php
namespace App\Tests;


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
    public function testAcquis()
    {
        function acquis(float $moyenne)
        {
            if ($moyenne>=0.5) {
                $response = "Acquis";
            }else {
                $response = "Non acquis";
            }
            return $response;
        }
        $acquis = acquis(0.5);
        $this->assertEquals("Acquis", $acquis);
    }
}
