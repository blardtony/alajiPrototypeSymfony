<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    /**
     * @Route("/quiz", name="quiz_list")
     */
    public function getQuizzes()
    {
        $user = $this->getUser();

        return $this->render('quiz/index.html.twig', [
            'teacher' => $user
        ]);
    }
}
