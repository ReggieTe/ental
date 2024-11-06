<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\InformationCenterRepository;
use App\Service\Validate;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformationCenterRepository::class)]
#[ORM\Table(name: "app_information_center")]
#[ORM\HasLifecycleCallbacks]

class InformationCenter
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\Column(type: "string", length: 255)]

    private $section;

    #[ORM\Column(type: "string", length: 255)]

    private $header;

    #[ORM\Column(type: "text")]

    private $body;

    #[ORM\Column(type: "integer")]

    private $list;

    #[ORM\Column(type: "boolean")]

    private $status;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getList(): ?int
    {
        return $this->list;
    }

    public function setList(int $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }
}
