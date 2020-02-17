<?php
namespace App\Api;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

class CandidateApi extends AbstractApi
{

    public function getCandidate()
    {
        $candidate =  $this->call('core_enrol_get_enrolled_users', [
            'courseid' => 41
        ]);
    }
}
