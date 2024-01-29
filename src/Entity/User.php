<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

/**
 * User Entity. Defines the user that can access the application.
 *
 * @author ibilbao
 */

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enpresa;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use AMREU\UserBundle\Model\UserInterface as AMREUserInterface;
use AMREU\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[JMS\ExclusionPolicy('all')]
#[ORM\Table(name: 'user')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Cache(region: 'app')]
class User extends BaseUser implements AMREUserInterface,  PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    protected $username;

    #[ORM\Column(type: 'json')]
    protected $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    protected $password;

    #[ORM\Column(type: 'string', length: 255)]
    protected $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    protected $email;

    #[ORM\Column(type: 'boolean', options: ['default' => '1'])]
    protected $activated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $lastLogin;

    #[ORM\Column(type: 'string', nullable: true)]
    private $confirmationToken;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $passwordRequestedAt;

    #[ORM\ManyToOne(targetEntity: 'Enpresa', inversedBy: 'erabiltzaileak')]
    #[ORM\JoinColumn(nullable: true)]
    private $enpresa;

    #[JMS\Exclude()]
    #[ORM\Column(type: 'string', nullable: true)]
    private $telefonoa;

    #[JMS\Exclude()]
    #[ORM\Column(type: 'string', nullable: true)]
    private $telefonoa2;

    #[JMS\Exclude()]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $ordena;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(string $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): \DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(\DateTime $passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function getEnpresa()
    {
        return $this->enpresa;
    }

    public function getTelefonoa()
    {
        return $this->telefonoa;
    }

    public function getTelefonoa2()
    {
        return $this->telefonoa2;
    }

    public function setEnpresa(Enpresa $enpresa = null)
    {
        $this->enpresa = $enpresa;
    }

    public function setTelefonoa($telefonoa)
    {
        $this->telefonoa = $telefonoa;
    }

    public function setTelefonoa2($telefonoa2)
    {
        $this->telefonoa2 = $telefonoa2;
    }

    public function getOrdena()
    {
        return $this->ordena;
    }

    public function setOrdena($ordena)
    {
        $this->ordena = $ordena;
    }

    public function __toString(): string
    {
        return (string) $this->getFirstName();
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): BaseUser
    {
        $this->username = $username;
        return $this;
    }
}
