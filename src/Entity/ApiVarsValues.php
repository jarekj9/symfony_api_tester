<?php

namespace App\Entity;

use App\Repository\ApiVarsValuesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiVarsValuesRepository::class)
 */
class ApiVarsValues
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
    private $var;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=ApiUrls::class, inversedBy="ApiVarsValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $urlkey;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVar(): ?string
    {
        return $this->var;
    }

    public function setVar(string $var): self
    {
        $this->var = $var;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
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

    public function getUrlkey(): ?ApiUrls
    {
        return $this->urlkey;
    }

    public function setUrlkey(?ApiUrls $urlkey): self
    {
        $this->urlkey = $urlkey;

        return $this;
    }

    public function __toString() //necessary for choice field (to show names in choice field)
    {
        return (string) $this->name.' : '.$this->var.' : '.$this->value;
    }
}
