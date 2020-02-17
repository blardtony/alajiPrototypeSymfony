<?php
namespace App\Api;


use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

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
}
