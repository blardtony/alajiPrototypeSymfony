<?php

namespace App\Controller;

use App\Api\TeacherApi;
use App\Entity\Candidate;
use App\Entity\Quiz;
use App\Entity\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CandidateController extends AbstractController
{
    /**
     * @Route("/quiz/{id}/candidates", name="list_candidates")
     */
    public function getAllCandidates(int $id)
    {
        $user = $this->getUser();
        $candidates = $this->getDoctrine()->getRepository(Candidate::class)->findBy(
            ['teacher' => $user]
        );
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $id]);

        foreach ($candidates as $candidate) {

        }

        return $this->render('candidate/list.html.twig', [
            'candidates' => $candidates,
            'quiz' => $quiz
        ]);
    }

    /**
     * @Route("/quiz/{idQ}/candidates/{id}", name="one_candidate")
     */
    public function getCandidate(int $id, Request $request, TeacherApi $teacherApi, int $idQ)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $idQ]);

        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('addCriteria', $submittedToken)) {
                $oral1 = intval($_POST['criteria1']);
                $oral2 = intval($_POST['criteria2']);
                $oral3 = intval($_POST['criteria3']);
                $oral4 = intval($_POST['criteria4']);

                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 1]);
                $results->setOralreview($oral1);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);


                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 2]);
                $results->setOralreview($oral2);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);


                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 3]);
                $results->setOralreview($oral3);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);


                $results = $this->getDoctrine()->getRepository(Result::class)->findOneBy(['candidate' => $candidate, 'criteria' => 4]);
                $results->setOralreview($oral4);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($results);

                $manager->flush();

            }
            $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);
            foreach ($results as $result) {

                $oral = $result->getOralreview();
                $test = $result->getTestreview();
                $coefTest = $result->getCoeftest();
                $coefOral = $result->getCoeforal();
                $average  = $teacherApi->averageCriteria($test, $coefTest, $oral, $coefOral);

                $acquis = $teacherApi->acquis($average);


                $result->setAverage($average);
                $result->setAcquis($acquis);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($result);
                $manager->flush();

            }

        }
        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);

        return $this->render('candidate/oneCandidate.html.twig', [
            'candidate' => $candidate,
            'results' => $results,
            'quiz' => $quiz
        ]);
    }

    /**
     * @Route("/quiz/{idQ}/candidates/{id}/form", name="form_candidate")
     */
    public function getFormCandidate(int $id, int $idQ, Request $request)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['id' => $idQ]);

        $result = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);

        return $this->render('candidate/form.html.twig', [
            'candidate' => $candidate,
            'result' => $result,
            'quiz' => $quiz
        ]);
    }


}
