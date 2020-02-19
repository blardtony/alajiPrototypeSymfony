<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Result;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CandidateController extends AbstractController
{
    /**
     * @Route("/candidates/list", name="list_candidates")
     */
    public function getAllCandidates()
    {
        $user = $this->getUser();
        $candidates = $this->getDoctrine()->getRepository(Candidate::class)->findBy(
            ['teacher' => $user]
        );

        return $this->render('candidate/list.html.twig', [
            'candidates' => $candidates,
        ]);
    }

    /**
     * @Route("/candidate/{id}", name="one_candidate")
     */
    public function getCandidate(int $id, Request $request)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);

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

        }
        $result = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);



        return $this->render('candidate/oneCandidate.html.twig', [
            'candidate' => $candidate,
            'results' => $result
        ]);
    }

    /**
     * @Route("/candidate/{id}/form", name="form_candidate")
     */
    public function getFormCandidate(int $id, Request $request)
    {
        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);
        $result = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);

        return $this->render('candidate/form.html.twig', [
            'candidate' => $candidate,
            'result' => $result
        ]);
    }

    private function averageCriteria(array $array)
    {
        $nbElements = count($array);
        $sum = 0;
        $coef = 0;
        for ($i=0; $i < $nbElements; $i++) {
          $sum = $sum + ($array[$i][0] * $array[$i][1]);
          $coef = $coef + $array[$i][1];
        }
        return $sum/$coef;
    }
}
