<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

/**
 * Enpresen Entitatea
 *
 * @author ibilbao
 */
use App\Repository\ZerbitzuaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Enpresa;

#[ORM\Table(name: 'zerbitzuak')]
#[ORM\Entity(repositoryClass: ZerbitzuaRepository::class)]
#[ORM\Cache(region: 'app')]
class Zerbitzua implements \Stringable {
    
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $izena_es;

    #[ORM\Column(type: 'string')]
    private $izena_eu;

    /**
     * Many Zerbitzuak have One Enpresa
     */
    #[ORM\ManyToOne(targetEntity: 'Enpresa', inversedBy: 'zerbitzuak')]
    #[ORM\JoinColumn(nullable: true)]
    private $enpresa;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $ordena;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $aktibatua;
    
    /**
     * Zerbitzu batek eskakizun asko dauzka
     */
    #[ORM\OneToMany(targetEntity: 'Eskakizuna', mappedBy: 'zerbitzua')]
    #[ORM\JoinColumn(nullable: true)]
    private $eskakizunak;
    

    public function __construct() {
	$this->eskakizunak = new ArrayCollection();
    }

    public function getId() {
	return $this->id;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function getOrdena() {
	return $this->ordena;
    }

    public function setOrdena($ordena) {
	$this->ordena = $ordena;
    }

    public function getIzena_es() {
	return $this->izena_es;
    }

    public function getIzena_eu() {
	return $this->izena_eu;
    }

    public function getIzenaEs() {
	return $this->izena_es;
    }

    public function getIzenaEu() {
	return $this->izena_eu;
    }
    
    public function getEnpresa() {
        return $this->enpresa;
    }

    public function setIzena_es($izena_es) {
	$this->izena_es = $izena_es;
    }
    
    public function setIzena_eu($izena_eu) {
	$this->izena_eu = $izena_eu;
    }

    public function setIzenaEu($izena_eu) {
	$this->izena_eu = $izena_eu;
    }

    public function setIzenaEs($izena_es) {
	$this->izena_es = $izena_es;
    }

    public function __toString(): string {
	return (string) $this->getIzenaEu();
    }

    public function getAktibatua() {
        return $this->aktibatua;
    }

    public function setAktibatua($aktibatua) {
        $this->aktibatua = $aktibatua;
    }

    public function setEnpresa(Enpresa $enpresa) {
        $this->enpresa = $enpresa;
    }
    
}
