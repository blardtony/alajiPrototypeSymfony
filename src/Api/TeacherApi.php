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
}
