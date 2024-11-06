<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\AppSettingsRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "app_settings")] //{@ORM\UniqueConstraint(name:"name", columns={"name"})})]
#[ORM\Entity(repositoryClass: AppSettingsRepository::class)]
#[ORM\HasLifecycleCallbacks]

class AppSettings
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 60, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "name", type: "string", length: 150, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid subject.")]

    private $name = '';

    #[ORM\Column(name: "default_value", type: "string", nullable: true)]

    private $defaultValue = '';

    #[ORM\Column(name: "custom_value", type: "string")]

    private $customValue = '';

    public function __construct()
    {
        $this->id = Validate::genKey();
    }
    public function getId(): ?string
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

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(?string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function getCustomValue(): ?string
    {
        return $this->customValue;
    }

    public function setCustomValue(string $customValue): self
    {
        $this->customValue = $customValue;

        return $this;
    }
}
