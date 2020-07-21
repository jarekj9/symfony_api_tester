<?php

namespace App\Entity;

use App\Repository\ApiUrlsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiUrlsRepository::class)
 */
class ApiUrls
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
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ApiUrls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userkey;

    /**
     * @ORM\OneToMany(targetEntity=ApiVarsValues::class, mappedBy="urlkey", orphanRemoval=true)
     */
    private $ApiVarsValues;

    public function __construct()
    {
        $this->ApiVarsValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function getUserkey(): ?User
    {
        return $this->userkey;
    }

    public function setUserkey(?User $userkey): self
    {
        $this->userkey = $userkey;

        return $this;
    }

    /**
     * @return Collection|ApiVarsValues[]
     */
    public function getApiVarsValues(): Collection
    {
        return $this->ApiVarsValues;
    }

    public function addApiVarsValue(ApiVarsValues $apiVarsValue): self
    {
        if (!$this->ApiVarsValues->contains($apiVarsValue)) {
            $this->ApiVarsValues[] = $apiVarsValue;
            $apiVarsValue->setUrlkey($this);
        }

        return $this;
    }

    public function removeApiVarsValue(ApiVarsValues $apiVarsValue): self
    {
        if ($this->ApiVarsValues->contains($apiVarsValue)) {
            $this->ApiVarsValues->removeElement($apiVarsValue);
            // set the owning side to null (unless already changed)
            if ($apiVarsValue->getUrlkey() === $this) {
                $apiVarsValue->setUrlkey(null);
            }
        }

        return $this;
    }


    public function __toString() //necessary for choice field in ApiVarsValuesForm (to show names in choice field)
    {
        return (string) $this->name.' : '.$this->url;
    }
}
