<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le Nom ne peut pas être vide')]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'numeric', message: 'Le coefficient doit être un nombre')]
    #[Assert\NotNull(message: 'Le coefficient ne peut pas être vide')]
    private ?float $coefficient = null;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Grade::class, cascade: ['remove'])]
private Collection $grade;

    public function __construct()
    {
        $this->grade = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCoefficient(): ?float
    {
        return $this->coefficient;
    }

    public function setCoefficient(?float $coefficient): static
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrade(): Collection
    {
        return $this->grade;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grade->contains($grade)) {
            $this->grade->add($grade);
            $grade->setSubject($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grade->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getSubject() === $this) {
                $grade->setSubject(null);
            }
        }

        return $this;
    }

    public function calculateAverage(): ?float
    {
        $total = 0;
        $coeffTotal = 0;

        foreach ($this->getGrade() as $grade) {
            $total += $grade->getValue() * $this->getCoefficient();
            $coeffTotal += $this->getCoefficient();
        }

        return $coeffTotal > 0 ? $total / $coeffTotal : null;
    }
}
