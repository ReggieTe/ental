<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\ContactRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "contact_form")]
//@UniqueEntity(fields={"message"}, message :"Error : Similiar message has been sent already!")]
#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Contact
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid name.")]

    private $name;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid subject.")]

    private $subject;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(message: "'{{ value }}' is not a valid email.")]

    private $email;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/[a-zA-Z., ]+$/", message: "The message contains invalid characters")]

    private $message;

    public function __construct()
    {
        $this->id = Validate::genKey(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
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

    //Get the value of subject

    public function getSubject()
    {
        return $this->subject;
    }

    //Set the value of subject

    //@return  self

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    //Get the value of email

    public function getEmail()
    {
        return $this->email;
    }

    //Set the value of email

    //@return  self

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    //Get the value of message

    public function getMessage()
    {
        return $this->message;
    }

    //Set the value of message

    //@return  self

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
