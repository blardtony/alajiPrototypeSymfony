<?php
namespace App\Api;


use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

use \DOMDocument;
use \DomXPath;

class TeacherApi extends AbstractApi
{

    public function getTeacher()
    {
        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => 41
        ]);
    }

    public function getCourses(int $id)
    {
        return $this->call('core_group_get_course_user_groups', [
            'userid' => $id
        ]);
    }

    public function getQuiz(int $id)
    {
        return $this->call('mod_quiz_get_quizzes_by_courses', [
            'courseids[]' => $id
        ]);
    }

    public function getUsers(int $id)
    {
        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => $id
        ]);
    }

    public function getAttempsUser(int $idQuiz, int $idCandidate)
    {
        return $this->call('mod_quiz_get_user_attempts', [
            'quizid' => $idQuiz,
            'userid' => $idCandidate
        ]);
    }

    public function getAttempsReview(int $id)
    {
        return $this->call('mod_quiz_get_attempt_review', [
            'attemptid' => $id,
        ]);
    }

    public function getNameQuestion(string $name)
    {
        $names = mb_convert_encoding($name, 'HTML-ENTITIES', 'UTF-8');
        $dom = @DOMDocument::loadHTML($names);

        $finder = new DomXPath($dom);
        $classname="qtext";
        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        return $nodes->item(0)->nodeValue;

    }

    public function averageCriteria(int $test, float $coeftest, int $oral, float $coeforal)
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

    function acquis(float $moyenne)
    {
        if ($moyenne>=0.5) {
            $response = "Acquis";
        }else {
            $response = "Non acquis";
        }
        return $response;
    }

}
