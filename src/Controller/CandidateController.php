<?php

namespace App\Controller;

use App\Api\TeacherApi;
use App\Entity\Candidate;
use App\Entity\Criteria;
use App\Entity\Quiz;
use App\Entity\Result;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
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

        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(['name' => $name]);

        return $this->render('candidate/list.html.twig', [
            'teacher' => $user,
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
        return $this->render('candidate/oneCandidate.html.twig', [
            'candidate' => $candidate,
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
        return $this->render('candidate/form.html.twig', [
            'candidate' => $candidate,
            'quiz' => $quiz,
        ]);
    }


    /**
     * @Route("/quiz/{nameQ}/candidates/{nameC}/summary", name="summary_candidate")
     */
    public function getSummary(string $nameC, Request $request, MarkdownParserInterface $parser)
    {

        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['fullname' => $nameC]);
        $name = $candidate->getFullname();
        $strippedName = str_replace(' ', '', $name);
        
        //https://ourcodeworld.com/articles/read/799/how-to-create-a-pdf-from-html-in-symfony-4-using-dompdf Pour le pdf
        if ($request->isMethod('POST')) {
            //Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('candidate/pdfSummary.html.twig', [
                'candidate' => $candidate
            ]);
            // Load HTML to Dompdf
            $dompdf->loadHtml($html);
            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');
            // Render the HTML as PDF
            $dompdf->render();
            // Output the generated PDF to Browser (force download)
            $dompdf->stream("Récapitulatif_$strippedName.pdf", [
                "Attachment" => true
            ]);
        }
        return $this->render('candidate/summary.html.twig', [
            'candidate' => $candidate,
        ]);
    }

    /**
     * @Route("/quiz/{nameQ}/candidates/{nameC}/summary/pdf", name="pdf_summary_candidate")
     */
    public function getSummaryPdf(string $nameC, Request $request)
    {

        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->findOneBy(['fullname' => $nameC]);

        return $this->render('candidate/pdfSummary.html.twig', [
            'candidate' => $candidate,
        ]);
    }

}
