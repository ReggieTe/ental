<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\AppUserNotificationRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "app_user_notification")]
#[ORM\Entity(repositoryClass: AppUserNotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]

class AppUserNotification
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 60, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "head", type: "text", length: 65535, nullable: false)]

    private $head;

    #[ORM\Column(name: "body", type: "text", length: 65535, nullable: false)]

    private $body;

    #[ORM\ManyToMany(targetEntity: UserAdmin::class, inversedBy: "notifications")]

    private $client;

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->client = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getHead(): ?string
    {
        return $this->head;
    }

    public function setHead(string $head): self
    {
        $this->head = $head;

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

    //@return Collection<int, UserAdmin>

    public function getClient(): Collection
    {
        return $this->client;
    }

    public function addClient(UserAdmin $client): self
    {
        if (!$this->client->contains($client)) {
            $this->client[] = $client;
        }

        return $this;
    }

    public function removeClient(UserAdmin $client): self
    {
        $this->client->removeElement($client);

        return $this;
    }

}
