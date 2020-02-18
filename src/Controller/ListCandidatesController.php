<?php

namespace App\Controller;

use App\Entity\Candidate;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListCandidatesController extends AbstractController
{
    /**
     * @Route("/list/candidates", name="list_candidates")
     */
    public function index()
    {
        $candidates = $this->getDoctrine()->getRepository(Candidate::class)->findBy(
            ['teacher' => 1]
        );

        return $this->render('list_candidates/index.html.twig', [
            'candidates' => $candidates,
        ]);
    }
}
