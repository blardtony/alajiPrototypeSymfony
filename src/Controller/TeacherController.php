<?php

namespace App\Controller;

use App\Api\TeacherApi;
use App\Entity\Candidate;
use App\Entity\Criteria;
use App\Entity\Quiz;
use App\Entity\Result;
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
     * @Route("/teacher/quiz", name="teacher_profile")
     */
    public function postQuiz(TeacherApi $teacherApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(Teacher::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $teacherApi->getCourses($idTeacher);

        $idCourses = $courses['groups'][0]['courseid'];

        $quizzes = $teacherApi->getQuiz($idCourses);
        $nameQuiz = $quizzes["quizzes"][0]['name'];
        $idQuiz = $quizzes["quizzes"][0]['id'];

        $quizDb = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);
        if (!$quizDb) {
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

        return $this->json([
            'success' => true,
            'quizzes' => "All quizzes are in the database"
        ]);

    }

    /**
     * @Route("/teacher/candidate", name="candidate")
     */
    public function postCandidate(TeacherApi $teacherApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(Teacher::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $teacherApi->getCourses($idTeacher);
        $idCourses = $courses['groups'][0]['courseid'];
        $idGroup = $courses['groups'][0]['id'];


        $users = $teacherApi->getUsers($idCourses);
        foreach ($users as $user) {

            $roles = $user['roles'][0]['roleid'];
            $group = $user['groups'];

            if (!empty($group)) {
                $groupIdCandidate = $group[0]['id'];
            }

            if ($roles === 5 && $groupIdCandidate === $idGroup) {
                $idCandidate = $user['id'];
                $fullnameCandidate = $user['fullname'];
                $avatarCandidate = $user['profileimageurl'];
                $emailCandidate = $user['email'];

                $candidateDb =  $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['moodleId' => $idCandidate]);
                if (!$candidateDb) {

                    $candidate = new Candidate;

                    $candidate->setFullname($fullnameCandidate);
                    $candidate->setMoodleId($idCandidate);
                    $candidate->setEmail($emailCandidate);
                    $candidate->setTeacher($teacher);
                    $candidate->setAvatar($avatarCandidate);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($candidate);
                    $manager->flush();

                }
            }

        }
        return $this->json([
            'success' => true,

        ]);


    }

    /**
     * @Route("/teacher/criteria", name="criteria")
     */
    public function postCriteria(TeacherApi $teacherApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(Teacher::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $teacherApi->getCourses($idTeacher);
        $idCourses = $courses['groups'][0]['courseid'];

        $quizzes = $teacherApi->getQuiz($idCourses);
        $idQuiz = $quizzes["quizzes"][0]['id'];

        $candidateDb =  $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['teacher' => $teacher ]);
        $idMoodleCandidate = $candidateDb->getMoodleId();

        $attempt = $teacherApi->getAttempsUser($idQuiz, $idMoodleCandidate);
        $idAttempt = end($attempt['attempts'])['id'];

        $attemptreview = $teacherApi->getAttempsReview($idAttempt);
        $questions = $attemptreview['questions'];

        $quizCriteria =  $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);

        foreach ($questions as $question) {
            $nameQuestion = $teacherApi->getNameQuestion($question['html']);



            $criteriaDb =  $this->getDoctrine()->getRepository(Criteria::class)->findOneBy(['name' => $nameQuestion]);

            if (!$criteriaDb) {

                $criteria = new Criteria;

                $criteria->setName($nameQuestion);
                $criteria->setQuiz($quizCriteria);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($criteria);
                $manager->flush();

            }
        }

        return $this->json([
            'success' => true,

        ]);

    }

    /**
     * @Route("/teacher/result", name="result")
     */
    public function postResult(TeacherApi $teacherApi)
    {

        $user = $this->getUser();
        $userEmail = $user->getEmail();

        $teacher = $this->getDoctrine()->getRepository(Teacher::class)->findOneBy(['email' => $userEmail]);

        $idTeacher = $teacher->getMoodleId();

        $courses = $teacherApi->getCourses($idTeacher);
        $idCourses = $courses['groups'][0]['courseid'];

        $quizzes = $teacherApi->getQuiz($idCourses);
        $idQuiz = $quizzes["quizzes"][0]['id'];

        $dbCandidates =  $this->getDoctrine()->getRepository(Candidate::class)->findBy(['teacher' => $teacher ]);


        foreach ($dbCandidates as $dbCandidate) {

            $idMoodleCandidate = $dbCandidate->getMoodleId();
            $idCandidateDb = $dbCandidate->getId();


            $attempt = $teacherApi->getAttempsUser($idQuiz, $idMoodleCandidate);
            $idAttempt = end($attempt['attempts'])['id'];

            $attemptreview = $teacherApi->getAttempsReview($idAttempt);
            $questions = $attemptreview['questions'];

            foreach ($questions as $question) {

                $testNote = intval($question['mark']);
                $nameQuestion = $teacherApi->getNameQuestion($question['html']);


                $nameCriteria =  $this->getDoctrine()->getRepository(Criteria::class)->findOneBy(['name' => $nameQuestion]);
                $idNameCriteria = $nameCriteria->getId();


                $testNoteDb =  $this->getDoctrine()->getRepository(Result::class)->findOneBy([
                    'testreview' => $testNote,
                    'candidate' => $idCandidateDb,
                    'criteria' => $idNameCriteria
                ]);

                if (!$testNoteDb) {

                    $result = new Result;

                    $result->setCandidate($dbCandidate);
                    $result->setCriteria($nameCriteria);
                    $result->setTestreview($testNote);
                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($result);
                    $manager->flush();

                }
            }

        }
        return $this->json([
            'success' => true,

        ]);

    }
}
