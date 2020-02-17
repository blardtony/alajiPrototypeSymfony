<?php

namespace App\Controller;

use App\Api\TeacherApi;
use App\Entity\Teacher;

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
        dump($courses);
        die;
        return $this->render('teacher/profile.html.twig', [
            $courses
        ]);
    }
}
