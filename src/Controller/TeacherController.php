<?php

namespace App\Controller;

use App\Api\TeacherApi;
use App\Entity\Teacher;
use App\Entity\Quiz;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher", name="teacher")
     */
    public function teacher(TeacherApi $teacherApi)
    {
        $teachers = $teacherApi->getTeacher();


        return $this->json([
            'success' => true,
            $teachers
        ]);
    }

    /**
     * @Route("/teacher/profile", name="teacher_profile")
     */
    public function getInfos(TeacherApi $teacherApi)
    {
        $teacher = $this->getDoctrine()->getRepository(Teacher::class)->findOneBy(['email' => 'jsimonet.alaji@gmail.com']);

        $idTeacher = $teacher->getMoodleId();

        $courses = $teacherApi->getCourses($idTeacher);
        $idCourses = $courses['groups'][0]['courseid'];

        $quizzes = $teacherApi->getQuiz($idCourses);
        $nameQuiz = $quizzes["quizzes"][0]['name'];
        $idQuiz = $quizzes["quizzes"][0]['id'];

        $quizDb = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodle_id' => $idQuiz]);
        if (condition) {
            // code...
        }
        $quiz = new Quiz;
        $quiz->setName($nameQuiz);
        $quiz->setMoodleId($idQuiz);
        $quiz->setTeacher($teacher);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($quiz);
        $manager->flush();

        return $this->json([
            'success' => true,
            'idQuiz' => $quiz->getId()
        ]);
    }
}
