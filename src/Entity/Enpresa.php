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
use App\Repository\EnpresaRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'enpresak')]
#[ORM\Entity(repositoryClass: EnpresaRepository::class)]
class Enpresa implements \Stringable {
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $izena;

    #[ORM\OneToMany(targetEntity: 'Zerbitzua', mappedBy: 'enpresa')]
    #[ORM\OrderBy(['ordena' => 'DESC'])]
    private $zerbitzuak;

    #[Assert\GreaterThan(0)]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $ordena;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $aktibatua;
    
    #[ORM\OneToMany(targetEntity: 'User', mappedBy: 'enpresa')]
    #[ORM\OrderBy(['ordena' => 'DESC'])]
    private $erabiltzaileak;

    #[ORM\OneToMany(targetEntity: 'Eskakizuna', mappedBy: 'enpresa')]
    #[ORM\JoinColumn(nullable: true)]
    private $eskakizunak;

    public function __construct() {
	    $this->erabiltzaileak = new ArrayCollection();
        $this->zerbitzuak = new ArrayCollection();
        $this->eskakizunak = new ArrayCollection();
    }
    
    public function getId() {
	return $this->id;
    }

    public function getIzena() {
	return $this->izena;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setIzena($izena) {
	$this->izena = $izena;
    }
    
    public function getOrdena() {
	return $this->ordena;
    }

    public function setOrdena($ordena) {
	$this->ordena = $ordena;
    }
     /**
     * @return ArrayCollection|User[]
     */
    public function getErabiltzaileak() {
	return $this->erabiltzaileak;
    }

    /**
    * 
    * @return ArrayCollection|Zerbitzuak[]
    */
    public function getZerbitzuak() {
	return $this->zerbitzuak;
    }

    
    public function getAktibatua() {
        return $this->aktibatua;
    }

    public function setAktibatua($aktibatua) {
        $this->aktibatua = $aktibatua;
    }

        
    public function __toString(): string {
	return (string) $this->getIzena();
    }

    /**
    * 
    * @return ArrayCollection|Eskakizunak[]
    */
    public function getEskakizunak() {
	return $this->eskakizunak;
    }

    public function __toDebug() {
	return	'id: '.$this->id.'|'
		.'izena: '.$this->izena.'|'
		.'ordena: '.$this->ordena.'|'
//		.'erabiltzaileak: '.$this->erabiltzaileak.'|'
//		.'zerbitzuak: '.$this->zerbitzuak.'|'
		.'aktibatua: '.$this->aktibatua.'|'
		;
    }

    
}
