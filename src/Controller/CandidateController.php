<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Result;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function getCandidate(int $id)
    {

        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);

        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);


        return $this->render('candidate/oneCandidate.html.twig', [
            'candidate' => $candidate,
            'results' => $results
        ]);
    }
}
