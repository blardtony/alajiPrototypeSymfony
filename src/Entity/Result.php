<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultRepository")
 */
class Result
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Candidate", inversedBy="results")
     */
    private $candidate;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $oralreview;

    /**
     * @ORM\Column(type="smallint")
     */
    private $testreview;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Criteria", inversedBy="results")
     */
    private $criteria;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOralreview(): ?int
    {
        return $this->oralreview;
    }

    public function setOralreview(?int $oralreview): self
    {
        $this->oralreview = $oralreview;

        return $this;
    }

    public function getTestreview(): ?int
    {
        return $this->testreview;
    }

    public function setTestreview(int $testreview): self
    {
        $this->testreview = $testreview;

        return $this;
    }

    public function getCriteria(): ?Criteria
    {
        return $this->criteria;
    }

    public function setCriteria(?Criteria $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }
}
