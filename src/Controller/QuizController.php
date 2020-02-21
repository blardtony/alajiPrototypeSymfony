<?php

namespace App\Controller;

use App\Api\TeacherApi;
use App\Controller\TeacherController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    /**
     * @Route("/quiz", name="quiz_list")
     */
    public function getQuizzes(Request $request, TeacherController $teacherController, TeacherApi $teacherApi)
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $teacherController->postQuiz($teacherApi);
            $teacherController->postCandidate($teacherApi);
            $teacherController->postCriteria($teacherApi);
            $teacherController->postResult($teacherApi);
        }
        return $this->render('quiz/index.html.twig', [
            'teacher' => $user
        ]);
    }
}
