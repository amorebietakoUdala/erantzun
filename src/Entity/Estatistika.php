<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

use App\Repository\EstatistikaRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[ORM\Table(name: 'view_estatistikak')]
#[ORM\Entity(repositoryClass: EstatistikaRepository::class, readOnly: true)]
class Estatistika implements \Stringable
{
    #[JMS\MaxDepth(1)]
    #[JMS\Expose()]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'Enpresa')]
    #[ORM\JoinColumn(name: 'enpresa_id', referencedColumnName: 'id', nullable: false)]
    private $enpresa;

    #[ORM\Id]
    #[ORM\Column(type: 'date', nullable: false)]
    private $data;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $urtea;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $hilabetea;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private $eskakizunak;

    public function getId()
    {
        return $this->enpresa->getId().$this->urtea;
    }

    public function getEnpresa(): ?String
    {
        return $this->enpresa;
    }

    public function getUrtea(): int
    {
        return $this->urtea;
    }

    public function getEskakizunak(): int
    {
        return $this->eskakizunak;
    }

    public function setEnpresa(Enpresa $enpresa)
    {
        $this->enpresa = $enpresa;
    }

    public function setUrtea($urtea)
    {
        $this->urtea = $urtea;
    }

    public function setEskakizunak($eskakizunak)
    {
        $this->eskakizunak = $eskakizunak;
    }

    public function getHilabetea(): int
    {
        return $this->hilabetea;
    }

    public function setHilabetea($hilabetea)
    {
        $this->hilabetea = $hilabetea;
    }

    public function getData(): \DateTime
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return $this->getUrtea().''.$this->getHilabetea();
    }

    public function fill(array $data): self {
        $this->enpresa = $data['enpresa'];
        $this->urtea = $data['urtea'];
        $this->eskakizunak = $data['eskakizunak'];
        return $this;
    }
}
