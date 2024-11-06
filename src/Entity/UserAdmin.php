<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserAdminRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: "user_admin")] //)]//{@ORM\UniqueConstraint(name:"phone", columns={"phone"})})
//@UniqueEntity(fields={"email"}, message :"There is already an account with this email")
#[ORM\Entity(repositoryClass: UserAdminRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserAdmin implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 50, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "token", type: "string", length: 50, nullable: true)]

    private $token;

    #[ORM\Column(name: "name", type: "string", length: 50, nullable: true)]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid name.")]

    private $name;

    #[ORM\Column(name: "fullname", type: "string", length: 50, nullable: true)]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid name.")]

    private $fullname;

    #[ORM\Column(name: "address", type: "string", length: 60, nullable: true)]

    private $address;

    #[ORM\Column(name: "phone", type: "string", length: 100, nullable: true)]

    private $phone;

    #[ORM\Column(name: "email", type: "string", length: 100, nullable: false)]
    #[Assert\NotBlank(message: "Email required")]
    #[Assert\Email(message: "'{{ value }}' is not a valid email.")]

    private $email;

    #[ORM\Column(type: "json")]

    private $roles = ["ROLE_USER"];

    #[ORM\Column(name: "password", type: "string", length: 100, nullable: true)]

    private $password;

    #[ORM\Column(name: "api_password ", type: "string", length: 100, nullable: true)]

    private $apiPassword;

    #[ORM\Column(name: "salt", type: "string", length: 100, nullable: true)]

    private $salt;

    #[ORM\Column(name: "state", type: "integer", nullable: true)]

    private $state;

    #[ORM\Column(name: "phone_verified", type: "boolean", nullable: true)]

    private $phoneVerified;

    #[ORM\Column(name: "email_verified", type: "boolean", nullable: true)]

    private $emailVerified;

    #[ORM\Column(name: "last_password_reset_request_date", type: "datetime", nullable: false)]

    private $lastPasswordResetRequestDate;

    #[ORM\Column(name: "lastlogin", type: "datetime", nullable: false)]

    private $lastlogin;

    #[ORM\Column(name: "lastlogout", type: "datetime", nullable: false)]

    private $lastlogout;

    #[ORM\OneToOne(targetEntity: UserSetting::class, mappedBy: "addedby", cascade: ["persist", "remove"])]

    private $userSetting;

    #[ORM\ManyToMany(targetEntity: AppUserNotification::class, mappedBy: "client")]

    private $notifications;

    #[ORM\OneToMany(targetEntity: UserOtp::class, mappedBy: "addedby")]

    private $userOtps;

    #[ORM\OneToMany(targetEntity: Car::class, mappedBy: "owner", orphanRemoval: true)]

    private $cars;

    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: "user", orphanRemoval: true)]

    private $rentals;

    #[ORM\OneToMany(targetEntity: UserCarAdditional::class, mappedBy: "owner")]

    private $userCarAdditionals;

    #[ORM\OneToMany(targetEntity: UserDrivingRestriction::class, mappedBy: "owner")]

    private $userDrivingRestrictions;

    #[ORM\OneToMany(targetEntity: UserTripChecklist::class, mappedBy: "owner", orphanRemoval: true)]

    private $userTripChecklists;

    #[ORM\Column(type: "string", length: 255)]

    private $type;

    #[ORM\OneToMany(targetEntity: UserBank::class, mappedBy: "client", orphanRemoval: true)]

    private $userBanks;

    #[ORM\OneToOne(targetEntity: UserPayFast::class, mappedBy: "client", cascade: ["persist", "remove"])]

    private $userPayFast;

    #[ORM\OneToOne(targetEntity: UserPayPal::class, mappedBy: "client", cascade: ["persist", "remove"])]

    private $userPayPal;

    #[ORM\Column(type: "boolean", nullable: true, name: "is_payfast_enabled")]

    private $isPayfastEnabled;

    #[ORM\Column(type: "boolean", nullable: true, name: "is_paypal_enabled")]

    private $isPaypalEnabled;

    #[ORM\Column(type: "boolean", nullable: true, name: "is_bank_enabled")]

    private $isBankEnabled;

    #[ORM\Column(type: "string", name: "location")]

    private $location;

    #[ORM\Column(type: "string", length: 255, nullable: true)]

    private $website;

    #[ORM\Column(type: "string", name: "fcm_id", length: 255, nullable: true)]

    private $FcmId;

    #[ORM\ManyToMany(targetEntity: Levy::class, mappedBy: "user")]

    private $levies;

    #[ORM\OneToMany(targetEntity: Promotion::class, mappedBy: "owner", orphanRemoval: true)]

    private $promotions;

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->id = Validate::genKey();
        $this->notifications = new ArrayCollection();
        $this->userOtps = new ArrayCollection();
        $this->cars = new ArrayCollection();
        $this->rentals = new ArrayCollection();
        $this->userCarAdditionals = new ArrayCollection();
        $this->userDrivingRestrictions = new ArrayCollection();
        $this->userTripChecklists = new ArrayCollection();
        $this->userBanks = new ArrayCollection();
        $this->phoneVerified = 0;
        $this->emailVerified = 0;
        $this->isBankEnabled = 0;
        $this->isPayfastEnabled = 0;
        $this->isPaypalEnabled = 0;
        $this->state = 1;
        $this->levies = new ArrayCollection();
        $this->promotions = new ArrayCollection();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

        $metadata->addConstraint(new UniqueEntity(['fields' => ['phone'], 'errorPath' => 'phone', 'message' => 'This phone has already been listed']));

    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->fullname ? $this->fullname : $this->email;
    }

    public function setUsername(string $username): self
    {
        //$this->username = $username;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    //@see UserInterface

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    //@see UserInterface

    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    //@see UserInterface

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getLastPasswordResetRequestDate(): ?\DateTimeInterface
    {
        return $this->lastPasswordResetRequestDate;
    }

    public function setLastPasswordResetRequestDate(\DateTimeInterface $lastPasswordResetRequestDate) : self
    {
        $this->lastPasswordResetRequestDate = $lastPasswordResetRequestDate;

        return $this;
    }

    public function getLastlogin(): ?\DateTimeInterface
    {
        return $this->lastlogin;
    }

    public function setLastlogin(\DateTimeInterface $lastlogin) : self
    {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    public function getLastlogout(): ?\DateTimeInterface
    {
        return $this->lastlogout;
    }

    public function setLastlogout(\DateTimeInterface $lastlogout) : self
    {
        $this->lastlogout = $lastlogout;

        return $this;
    }

    //Get the value of name

    ////@return  string

    public function getName(): ?string
    {
        return $this->name;
    }

    //Set the value of name

    //@param  string  $name

    //@return  self

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserSetting(): ?UserSetting
    {
        return $this->userSetting;
    }

    public function setUserSetting(?UserSetting $userSetting): self
    {
        // unset the owning side of the relation if necessary
        if ($userSetting === null && $this->userSetting !== null) {
            $this->userSetting->setAddedby(null);
        }

        // set the owning side of the relation if necessary
        if ($userSetting !== null && $userSetting->getAddedby() !== $this) {
            $userSetting->setAddedby($this);
        }

        $this->userSetting = $userSetting;

        return $this;
    }

    //@return Collection<int, AppUserNotification>

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(AppUserNotification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->addClient($this);
        }

        return $this;
    }

    public function removeNotification(AppUserNotification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            $notification->removeClient($this);
        }

        return $this;
    }

    //@return Collection|UserOtp[]

    public function getUserOtps(): Collection
    {
        return $this->userOtps;
    }

    public function addUserOtp(UserOtp $userOtp): self
    {
        if (!$this->userOtps->contains($userOtp)) {
            $this->userOtps[] = $userOtp;
            $userOtp->setAddedby($this);
        }

        return $this;
    }

    public function removeUserOtp(UserOtp $userOtp): self
    {
        if ($this->userOtps->removeElement($userOtp)) {
            // set the owning side to null (unless already changed)
            if ($userOtp->getAddedby() === $this) {
                $userOtp->setAddedby(null);
            }
        }

        return $this;
    }

    //Get the value of token

    ////@return  string

    public function getToken()
    {
        return $this->token;
    }

    //Set the value of token

    //@param  string  $token

    //@return  self

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    //Get the value of apiPassword

    ////@return  string

    public function getApiPassword()
    {
        return $this->apiPassword;
    }

    //Set the value of apiPassword

    //@param  string  $apiPassword

    //@return  self

    public function setApiPassword($apiPassword)
    {
        $this->apiPassword = $apiPassword;

        return $this;
    }

    //Get the value of phoneVerified

    //@return  bool

    public function getPhoneVerified(): ?bool
    {
        return $this->phoneVerified;
    }

    //Set the value of phoneVerified

    //@param  int  $phoneVerified

    //@return  self

    public function setPhoneVerified(?bool $phoneVerified)
    {
        $this->phoneVerified = $phoneVerified;

        return $this;
    }

    //Get the value of emailVerified

    //@return  int

    public function getEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    //Set the value of emailVerified

    //@param  int  $emailVerified

    //@return  self

    public function setEmailVerified(?bool $emailVerified)
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    //@return Collection|Car[]

    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): self
    {
        if (!$this->cars->contains($car)) {
            $this->cars[] = $car;
            $car->setOwner($this);
        }

        return $this;
    }

    public function removeCar(Car $car): self
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getOwner() === $this) {
                $car->setOwner(null);
            }
        }

        return $this;
    }

    //@return Collection|Rental[]

    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): self
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals[] = $rental;
            $rental->setUser($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): self
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getUser() === $this) {
                $rental->setUser(null);
            }
        }

        return $this;
    }

    //@return Collection|UserCarAdditional[]

    public function getUserCarAdditionals(): Collection
    {
        return $this->userCarAdditionals;
    }

    public function addUserCarAdditional(UserCarAdditional $userCarAdditional): self
    {
        if (!$this->userCarAdditionals->contains($userCarAdditional)) {
            $this->userCarAdditionals[] = $userCarAdditional;
            $userCarAdditional->setOwner($this);
        }

        return $this;
    }

    public function removeUserCarAdditional(UserCarAdditional $userCarAdditional): self
    {
        if ($this->userCarAdditionals->removeElement($userCarAdditional)) {
            // set the owning side to null (unless already changed)
            if ($userCarAdditional->getOwner() === $this) {
                $userCarAdditional->setOwner(null);
            }
        }

        return $this;
    }

    //@return Collection|UserDrivingRestriction[]

    public function getUserDrivingRestrictions(): Collection
    {
        return $this->userDrivingRestrictions;
    }

    public function addUserDrivingRestriction(UserDrivingRestriction $userDrivingRestriction): self
    {
        if (!$this->userDrivingRestrictions->contains($userDrivingRestriction)) {
            $this->userDrivingRestrictions[] = $userDrivingRestriction;
            $userDrivingRestriction->setOwner($this);
        }

        return $this;
    }

    public function removeUserDrivingRestriction(UserDrivingRestriction $userDrivingRestriction): self
    {
        if ($this->userDrivingRestrictions->removeElement($userDrivingRestriction)) {
            // set the owning side to null (unless already changed)
            if ($userDrivingRestriction->getOwner() === $this) {
                $userDrivingRestriction->setOwner(null);
            }
        }

        return $this;
    }

    //@return Collection|UserTripChecklist[]

    public function getUserTripChecklists(): Collection
    {
        return $this->userTripChecklists;
    }

    public function addUserTripChecklist(UserTripChecklist $userTripChecklist): self
    {
        if (!$this->userTripChecklists->contains($userTripChecklist)) {
            $this->userTripChecklists[] = $userTripChecklist;
            $userTripChecklist->setOwner($this);
        }

        return $this;
    }

    public function removeUserTripChecklist(UserTripChecklist $userTripChecklist): self
    {
        if ($this->userTripChecklists->removeElement($userTripChecklist)) {
            // set the owning side to null (unless already changed)
            if ($userTripChecklist->getOwner() === $this) {
                $userTripChecklist->setOwner(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    //@return Collection<int, UserBank>

    public function getUserBanks(): Collection
    {
        return $this->userBanks;
    }

    public function addUserBank(UserBank $userBank): self
    {
        if (!$this->userBanks->contains($userBank)) {
            $this->userBanks[] = $userBank;
            $userBank->setClient($this);
        }

        return $this;
    }

    public function removeUserBank(UserBank $userBank): self
    {
        if ($this->userBanks->removeElement($userBank)) {
            // set the owning side to null (unless already changed)
            if ($userBank->getClient() === $this) {
                $userBank->setClient(null);
            }
        }

        return $this;
    }

    public function getUserPayFast(): ?UserPayFast
    {
        return $this->userPayFast;
    }

    public function setUserPayFast(UserPayFast $userPayFast): self
    {
        // set the owning side of the relation if necessary
        if ($userPayFast->getClient() !== $this) {
            $userPayFast->setClient($this);
        }

        $this->userPayFast = $userPayFast;

        return $this;
    }

    public function getUserPayPal(): ?UserPayPal
    {
        return $this->userPayPal;
    }

    public function setUserPayPal(UserPayPal $userPayPal): self
    {
        // set the owning side of the relation if necessary
        if ($userPayPal->getClient() !== $this) {
            $userPayPal->setClient($this);
        }

        $this->userPayPal = $userPayPal;

        return $this;
    }

    public function getIsPayfastEnabled(): ?bool
    {
        return $this->isPayfastEnabled;
    }

    public function setIsPayfastEnabled(?bool $isPayfastEnabled): self
    {
        $this->isPayfastEnabled = $isPayfastEnabled;

        return $this;
    }

    public function getIsPaypalEnabled(): ?bool
    {
        return $this->isPaypalEnabled;
    }

    public function setIsPaypalEnabled(?bool $isPaypalEnabled): self
    {
        $this->isPaypalEnabled = $isPaypalEnabled;

        return $this;
    }

    public function getIsBankEnabled(): ?bool
    {
        return $this->isBankEnabled;
    }

    public function setIsBankEnabled(?bool $isBankEnabled): self
    {
        $this->isBankEnabled = $isBankEnabled;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getFcmId(): ?string
    {
        return $this->FcmId;
    }

    public function setFcmId(?string $FcmId): self
    {
        $this->FcmId = $FcmId;

        return $this;
    }

    //@return Collection<int, Levy>

    public function getLevies(): Collection
    {
        return $this->levies;
    }

    public function addLevy(Levy $levy): self
    {
        if (!$this->levies->contains($levy)) {
            $this->levies[] = $levy;
            $levy->addUser($this);
        }

        return $this;
    }

    public function removeLevy(Levy $levy): self
    {
        if ($this->levies->removeElement($levy)) {
            $levy->removeUser($this);
        }

        return $this;
    }

    //@return Collection<int, Promotion>

    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setOwner($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getOwner() === $this) {
                $promotion->setOwner(null);
            }
        }

        return $this;
    }

    public function isPhoneVerified(): ?bool
    {
        return $this->phoneVerified;
    }

    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function isPayfastEnabled(): ?bool
    {
        return $this->isPayfastEnabled;
    }

    public function setPayfastEnabled(?bool $isPayfastEnabled): static
    {
        $this->isPayfastEnabled = $isPayfastEnabled;

        return $this;
    }

    public function isPaypalEnabled(): ?bool
    {
        return $this->isPaypalEnabled;
    }

    public function setPaypalEnabled(?bool $isPaypalEnabled): static
    {
        $this->isPaypalEnabled = $isPaypalEnabled;

        return $this;
    }

    public function isBankEnabled(): ?bool
    {
        return $this->isBankEnabled;
    }

    public function setBankEnabled(?bool $isBankEnabled): static
    {
        $this->isBankEnabled = $isBankEnabled;

        return $this;
    }
}
