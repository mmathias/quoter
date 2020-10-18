<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
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
     * @var Collection
     * @ORM\OneToMany(targetEntity="Quote", mappedBy="author")
     */
    private $quotes;

    /**
     * Author constructor.
     */
    public function __construct()
    {
        $this->quotes = [];
    }

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

    public function getQuotes()
    {
        return $this->quotes;
    }

    public function setQuotes(Collection $quotes): self
    {
        $this->quotes = $quotes;

        return $this;
    }
}
