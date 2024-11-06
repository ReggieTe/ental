<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserReportRepository;
use App\Service\Validate;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "user_report")]
#[ORM\Entity(repositoryClass: UserReportRepository::class)]
#[ORM\HasLifecycleCallbacks]
//@UniqueEntity(fields={"comment"}, message :"Report has already been submitted")

class UserReport
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 60, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "comment", type: "text", nullable: false)]
    #[Assert\NotBlank(message: "Report message required")]

    private $comment;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "userReports")]

    private $addedby;

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    //Get the value of comment

    ////@return  string

    public function getComment()
    {
        return $this->comment;
    }

    //Set the value of comment

    //@param  string  $comment

    //@return  self

    public function setComment(string $comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAddedby(): ?UserAdmin
    {
        return $this->addedby;
    }

    public function setAddedby(?UserAdmin $addedby): self
    {
        $this->addedby = $addedby;

        return $this;
    }
}
