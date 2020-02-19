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
    public function testAverage(TeacherApi $teacherApi)
    {
        function average(int $test, float $coeftest, int $oral, float $coeforal)
        {
            $array = [[$test, $coeftest], [$oral, $coeforal]];
            $nbElements = count($array);
            $sum = 0;
            $coef = 0;
            for ($i=0; $i < $nbElements; $i++) {
              $sum = $sum + ($array[$i][0] * $array[$i][1]);
              $coef = $coef + $array[$i][1];
            }
            return $sum/$coef;
        }
        $average = average(1, 0.77, 0, 0.23);
        $this->assertEquals(0.77, $average);
    }
}
