<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserAppNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAppNotificationRepository::class)]
#[ORM\Table(name: "app_user_notification_user_admin")]
#[ORM\HasLifecycleCallbacks]

class UserAppNotification
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(name: "notification_read", type: "boolean")]

    private $notificationRead;

    #[ORM\Column(name: "app_user_notification_id", type: "string")]

    private $appNotification;

    #[ORM\Column(name: "user_admin_id", type: "string")]

    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotificationRead(): ?bool
    {
        return $this->notificationRead;
    }

    public function setNotificationRead(bool $notificationRead): self
    {
        $this->notificationRead = $notificationRead;

        return $this;
    }

    public function getAppNotification()
    {
        return $this->appNotification;
    }

    public function setAppNotification($appNotification): self
    {
        $this->appNotification = $appNotification;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isNotificationRead(): ?bool
    {
        return $this->notificationRead;
    }
}
