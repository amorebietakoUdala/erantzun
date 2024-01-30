<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

/**
 * Erantzuna Entitatea
 *
 * @author ibilbao
 */
use App\Repository\ErantzunaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

#[ORM\Table(name: 'erantzunak')]
#[ORM\Entity(repositoryClass: ErantzunaRepository::class)]
class Erantzuna implements \Stringable {
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $erantzuna;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private $noiz;

    #[ORM\ManyToOne(targetEntity: 'Eskakizuna', inversedBy: 'erantzunak')]
    private $eskakizuna;

    #[ORM\ManyToOne(targetEntity: 'User')]
    private $erantzulea;

    public function getId() {
	return $this->id;
    }

    public function getErantzuna() {
	return $this->erantzuna;
    }

    public function getEskakizuna() {
	return $this->eskakizuna;
    }

    public function getErantzulea() {
	return $this->erantzulea;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setErantzuna($erantzuna) {
	$this->erantzuna = $erantzuna;
    }

    public function setEskakizuna(Eskakizuna $eskakizuna) {
	$this->eskakizuna = $eskakizuna;
    }

    public function setErantzulea(User $erantzulea) {
	$this->erantzulea = $erantzulea;
    }

    public function getNoiz() {
	return $this->noiz;
    }

    public function setNoiz($noiz) {
	$this->noiz = $noiz;
    }

    public function __toString(): string {
	return (string) $this->getErantzuna();
    }
}
