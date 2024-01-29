<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'eranskinak')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Eranskina
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="eranskina", fileNameProperty="eranskinaName", size="eranskinaSize")
     * @var File
     */
    #[Vich\UploadableField(mapping: "eranskina", fileNameProperty: "eranskinaName", size: "eranskinaSize")]
    #[Assert\File(maxSize: '4096k', mimeTypes: ['application/pdf'])]
    private $eranskinaFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $eranskinaName;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $eranskinaSize;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Eskakizuna::class, inversedBy: 'eranskinak', cascade: ['persist', 'merge', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private $eskakizuna;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $eranskina
     *
     * @return File
     */
    public function setEranskinaFile(File $eranskina = null)
    {
        $this->eranskinaFile = $eranskina;

        if ($eranskina) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
            $this->eranskinaSize = $this->eranskinaFile->getSize();
            $this->eranskinaName = $this->eranskinaFile->getFilename();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getEranskinaFile()
    {
        return $this->eranskinaFile;
    }

    /**
     * @param string $eranskinaName
     *
     * @return Product
     */
    public function setEranskinaName($eranskinaName)
    {
        $this->eranskinaName = $eranskinaName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEranskinaName()
    {
        return $this->eranskinaName;
    }

    /**
     * @param int $eranskinaSize
     *
     * @return Product
     */
    public function setEranskinaSize($eranskinaSize)
    {
        $this->eranskinaSize = $eranskinaSize;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer|null
     */
    public function getEranskinaSize()
    {
        return $this->eranskinaSize;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getEskakizuna()
    {
        return $this->eskakizuna;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    // public function addEskakizuna(Eskakizuna $eskakizuna)
    // {
    //     if (!$this->eskakizuna->contains($eskakizuna)) {
    //         $this->setEskakizuna($eskakizuna);
    //     }
    // }

    public function setEskakizuna(Eskakizuna $eskakizuna = null)
    {
        $this->eskakizuna = $eskakizuna;
    }

    public function _toString()
    {
        return $this->eranskinaName.'('.$this->eranskinaSize.')';
    }
}
