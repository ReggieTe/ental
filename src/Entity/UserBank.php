<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserBankRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: "user_bank")]
#[ORM\Entity(repositoryClass: UserBankRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserBank
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\Column(type: "string", length: 255, name: "account_holder")]

    private $accountHolder;

    #[ORM\Column(type: "string", length: 255, name: "account_type")]

    private $accountType;

    #[ORM\Column(type: "string", length: 255, name: "account_number")]

    private $accountNumber;

    #[ORM\Column(type: "string", length: 255, name: "account_bank")]

    private $accountBank;

    #[ORM\Column(type: "boolean", name: "default_account")]

    private $defaultAccount;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "userBanks")]
    #[ORM\JoinColumn(nullable: false)]

    private $client;

    #[ORM\Column(type: "string", length: 255, nullable: true, name: "account_branch_code")]

    private $accountBranchCode;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

        $metadata->addPropertyConstraint("accountHolder", new Assert\NotBlank(['message' => 'Account holder must not be blank']));
        $metadata->addPropertyConstraint("accountType", new Assert\NotBlank(['message' => 'Account type number must not be blank']));
        $metadata->addPropertyConstraint("accountNumber", new Assert\NotBlank(['message' => 'Account number must not be blank']));
        $metadata->addPropertyConstraint("accountBank", new Assert\NotBlank(['message' => 'Account bank must not be blank']));

    }

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function __toString()
    {
        return $this->accountBank . "(" . $this->accountBank . ")";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAccountHolder(): ?string
    {
        return $this->accountHolder;
    }

    public function setAccountHolder(string $accountHolder): self
    {
        $this->accountHolder = $accountHolder;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountBank(): ?string
    {
        return $this->accountBank;
    }

    public function setAccountBank(string $accountBank): self
    {
        $this->accountBank = $accountBank;

        return $this;
    }

    public function getDefaultAccount(): ?bool
    {
        return $this->defaultAccount;
    }

    public function setDefaultAccount(bool $defaultAccount): self
    {
        $this->defaultAccount = $defaultAccount;

        return $this;
    }

    public function getClient(): ?UserAdmin
    {
        return $this->client;
    }

    public function setClient(?UserAdmin $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getAccountBranchCode(): ?string
    {
        return $this->accountBranchCode;
    }

    public function setAccountBranchCode(?string $accountBranchCode): self
    {
        $this->accountBranchCode = $accountBranchCode;

        return $this;
    }

    public function isDefaultAccount(): ?bool
    {
        return $this->defaultAccount;
    }
}
