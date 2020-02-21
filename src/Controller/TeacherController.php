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


        $quizzess = $teacherApi->getQuiz($idCourses);
        if (count($quizzess['quizzes']) > 1) {
            foreach ($quizzess['quizzes'] as $quizzes) {

                $nameQuiz = $quizzes[0]['name'];


                $idQuiz = $quizzes[0]['id'];

                $quizDb = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);
                if (!$quizDb) {
                    $quizDb = new Quiz;
                    $quizDb->setName($nameQuiz);
                    $quizDb->setMoodleId($idQuiz);
                    $quizDb->setTeacher($teacher);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($quizDb);
                    $manager->flush();
                }

                $quizDb->setName($nameQuiz);
                $quizDb->setMoodleId($idQuiz);
                $quizDb->setTeacher($teacher);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($quizDb);
                $manager->flush();

            }
        }

            $nameQuiz = $quizzess["quizzes"][0]['name'];


            $idQuiz = $quizzess["quizzes"][0]['id'];

            $quizDb = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['moodleId' => $idQuiz]);
            if (!$quizDb) {
                $quizDb = new Quiz;
                $quizDb->setName($nameQuiz);
                $quizDb->setMoodleId($idQuiz);
                $quizDb->setTeacher($teacher);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($quizDb);
                $manager->flush();
            }

            $quizDb->setName($nameQuiz);
            $quizDb->setMoodleId($idQuiz);
            $quizDb->setTeacher($teacher);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($quizDb);
            $manager->flush();




        return $this->json([
            'success' => true,
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
                    $candidateDb = new Candidate;
                    $candidateDb->setFullname($fullnameCandidate);
                    $candidateDb->setMoodleId($idCandidate);
                    $candidateDb->setEmail($emailCandidate);
                    $candidateDb->setTeacher($teacher);
                    $candidateDb->setAvatar($avatarCandidate);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($candidateDb);
                    $manager->flush();
                }

                $candidateDb->setFullname($fullnameCandidate);
                $candidateDb->setMoodleId($idCandidate);
                $candidateDb->setEmail($emailCandidate);
                $candidateDb->setTeacher($teacher);
                $candidateDb->setAvatar($avatarCandidate);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($candidateDb);
                $manager->flush();

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

        $quizzess = $teacherApi->getQuiz($idCourses);
        if (count($quizzess['quizzes']) > 1) {
            foreach ($quizzess as $quizzes) {

                $idQuiz = $quizzes[0]['id'];


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
                        $criteriaDb = new Criteria;
                        $criteriaDb->setName($nameQuestion);
                        $criteriaDb->setQuiz($quizCriteria);
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($criteriaDb);
                        $manager->flush();
                    }
                    $criteriaDb->setName($nameQuestion);
                    $criteriaDb->setQuiz($quizCriteria);
                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($criteriaDb);
                    $manager->flush();
                }
            }
        }
        $idQuiz = $quizzess["quizzes"][0]['id'];


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
                $criteriaDb = new Criteria;
                $criteriaDb->setName($nameQuestion);
                $criteriaDb->setQuiz($quizCriteria);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($criteriaDb);
                $manager->flush();
            }
            $criteriaDb->setName($nameQuestion);
            $criteriaDb->setQuiz($quizCriteria);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($criteriaDb);
            $manager->flush();
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

        $quizzess = $teacherApi->getQuiz($idCourses);
        if (count($quizzess['quizzes']) > 1) {
            foreach ($quizzess as $quizzes) {

                $idQuiz = $quizzes[0]['id'];
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
                            'candidate' => $idCandidateDb,
                            'criteria' => $idNameCriteria
                        ]);


                        $testNoteDb->setCandidate($dbCandidate);
                        $testNoteDb->setCriteria($nameCriteria);
                        $testNoteDb->setTestreview($testNote);
                        $manager = $this->getDoctrine()->getManager();
                        $manager->persist($testNoteDb);
                        $manager->flush();


                    }

                }
            }
        }
        $idQuiz = $quizzess["quizzes"][0]['id'];
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
                    'candidate' => $idCandidateDb,
                    'criteria' => $idNameCriteria
                ]);


                $testNoteDb->setCandidate($dbCandidate);
                $testNoteDb->setCriteria($nameCriteria);
                $testNoteDb->setTestreview($testNote);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($testNoteDb);
                $manager->flush();


            }

        }


        return $this->json([
            'success' => true,

        ]);

    }
    /**
     * @Route("/teacher/coef", name="result")
     */
    public function postcoef(TeacherApi $teacherApi)
    {

        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 1]);

        foreach ($results as $result) {
            $result->setCoeforal(0.77);
            $result->setCoeftest(0.23);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }

        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 2]);
        foreach ($results as $result) {
            $result->setCoeforal(0.11);
            $result->setCoeftest(0.89);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }


        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 3]);
        foreach ($results as $result) {
            $result->setCoeforal(0.48);
            $result->setCoeftest(0.52);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }


        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['criteria' => 4]);
        foreach ($results as $result) {
            $result->setCoeforal(0.66);
            $result->setCoeftest(0.34);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($result);
        }

        $manager->flush();
        return $this->json([
            'success' => true,

        ]);

    }
}
