<?php

namespace App\Controller;

use App\Api\TeacherApi;
use App\Entity\Candidate;
use App\Entity\Criteria;
use App\Entity\Quiz;
use App\Entity\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CandidateController extends AbstractController
{
    /**
     * @Route("/quiz/{name}/candidates", name="list_candidates")
     */
    public function getAllCandidates(string $name)
    {
        $user = $this->getUser();
        $candidates = $this->getDoctrine()->getRepository(Candidate::class)->findBy(
            ['teacher' => $user]
        );
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['name' => $name]);

        foreach ($candidates as $candidate) {

        }

        return $this->render('candidate/list.html.twig', [
            'candidates' => $candidates,
            'quiz' => $quiz
        ]);
    }

    /**
     * @Route("/quiz/{nameQ}/candidates/{nameC}", name="one_candidate")
     */
    public function getCandidate(string $nameC, Request $request, TeacherApi $teacherApi, string $nameQ)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['fullname' => $nameC]);
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['name' => $nameQ]);

        //Envoie du formulaire en base de donnée.
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('addCriteria', $submittedToken)) {
                $criterias = $request->request->get('criteria');
                $manager = $this->getDoctrine()->getManager();
                foreach ($quiz->getCriterias() as $criteria) {
                    $result = $this->getDoctrine()->getRepository(Result::class)->findOneBy([
                        'candidate' => $candidate,
                        'criteria' => $criteria
                    ]);
                    $review = (int) $criterias[$criteria->getId()];
                    $result->setOralreview($review);
                    $manager->persist($result);
                }
                $manager->flush();
            }

            $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);
            //Calcul de la moyenne et vérifier si le critère est acquis ou pas.
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
     * @Route("/quiz/{nameQ}/candidates/{nameC}/form", name="form_candidate")
     */
    public function getFormCandidate(string $nameC, string $nameQ, Request $request)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['fullname' => $nameC]);
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['name' => $nameQ]);

        $result = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);

        return $this->render('candidate/form.html.twig', [
            'candidate' => $candidate,
            'result' => $result,
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/quiz/{nameQ}/candidates/{nameC}/summary", name="summary_candidate")
     */
    public function getSummary(string $nameC, Request $request)
    {

        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['fullname' => $nameC]);

        return $this->render('candidate/summary.html.twig', [
            'candidate' => $candidate,
        ]);
    }

}
