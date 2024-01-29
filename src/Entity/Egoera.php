<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

/**
 * Egoeren Entitatea
 *
 * @author ibilbao
 */

use App\Repository\EgoeraRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Table(name: 'egoerak')]
#[ORM\Entity(repositoryClass: EgoeraRepository::class)]
#[ORM\Cache(region: 'app')]
class Egoera implements \Stringable {
    final public const EGOERA_BIDALI_GABE = 1;
    final public const EGOERA_BIDALIA = 2;
    final public const EGOERA_ERANTZUNDA = 3;
    final public const EGOERA_ITXIA = 4;
    
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private $deskripzioa_es;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private $deskripzioa_eu;

    public function getId() {
	return $this->id;
    }

    public function getDeskripzioa_es() {
	return $this->deskripzioa_es;
    }

    public function getDeskripzioa_eu() {
	return $this->deskripzioa_eu;
    }

    public function setDeskripzioa_es($deskripzioa_es) {
	$this->deskripzioa_es = $deskripzioa_es;
    }

    public function setDeskripzioa_eu($deskripzioa_eu) {
	$this->deskripzioa_eu = $deskripzioa_eu;
    }

    public function getDeskripzioaEs() {
	return $this->deskripzioa_es;
    }

    public function getDeskripzioaEu() {
	return $this->deskripzioa_eu;
    }

    public function setDeskripzioaEs($deskripzioa_es) {
	$this->deskripzioa_es = $deskripzioa_es;
    }

    public function setDeskripzioaEu($deskripzioa_eu) {
	$this->deskripzioa_eu = $deskripzioa_eu;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function __toString(): string {
	return (string) $this->getDeskripzioaEs();
    }

    public function __toDebug() {
	return	'id: '.$this->id.'|'.
		'deskripzioa_es: '.$this->deskripzioa_es.'|'.
		'deskripzioa_eu: '.$this->deskripzioa_eu;
    }
}