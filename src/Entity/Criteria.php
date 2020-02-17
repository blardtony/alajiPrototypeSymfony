<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CriteriaRepository")
 */
class Criteria
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $oralreview;

    /**
     * @ORM\Column(type="integer")
     */
    private $moodlereview;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quiz", inversedBy="criterias")
     */
    private $quiz;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Candidate", inversedBy="criterias")
     */
    private $candidate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOralreview(): ?int
    {
        return $this->oralreview;
    }

    public function setOralreview(?int $oralreview): self
    {
        $this->oralreview = $oralreview;

        return $this;
    }

    public function getMoodlereview(): ?int
    {
        return $this->moodlereview;
    }

    public function setMoodlereview(int $moodlereview): self
    {
        $this->moodlereview = $moodlereview;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }
}
