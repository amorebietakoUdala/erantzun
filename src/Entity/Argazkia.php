<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Eskakizuna;

#[ORM\Table(name: 'argazkiak')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Argazkia {
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="argazkia", fileNameProperty="imageName", size="imageSize")
     * @var File
     */
    #[Vich\UploadableField(mapping: "argazkia", fileNameProperty:"imageName", size: "imageSize")]
    #[Assert\File(maxSize: '4096k', mimeTypes: ['image/png', 'image/jpeg', 'image/pjpeg'])]
    private $imageFile;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $imageName;

     /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="thumbnail", fileNameProperty="imageThumbnail", size="imageThumbnailSize")
     * @var File
     */
    #[Vich\UploadableField(mapping: "thumbnail", fileNameProperty:"imageThumbnail", size: "imageThumbnailSize")]
    #[Assert\File(maxSize: '4096k', mimeTypes: ['image/png', 'image/jpeg', 'image/pjpeg'])]
    private $imageThumbnailFile;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $imageThumbnail;

    /**
     * @var integer
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $imageThumbnailSize;

    /**
     * @var integer
     */
    #[ORM\Column(type: 'integer')]
    private $imageSize;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Eskakizuna::class, inversedBy: 'argazkiak', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $eskakizuna;
    
    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $image
     *
     * @return File
     */
    public function setImageFile(File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null != $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
	    $this->imageSize = $this->imageFile->getSize();
	    $this->imageName = $this->imageFile->getFilename();
        }
        
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     *
     * @return Product
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
        
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName()
    {
        return $this->imageName;
    }
    
    /**
     * @param integer $imageSize
     *
     * @return Product
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;
        
        return $this;
    }

    public function getId() {
	return $this->id;
    }

        /**
     * @return integer|null
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }
    
    public function getUpdatedAt() {
	return $this->updatedAt;
    }

    public function getEskakizuna() {
	return $this->eskakizuna;
    }

    public function setUpdatedAt(\DateTime $updatedAt) {
	$this->updatedAt = $updatedAt;
    }

    public function setEskakizuna(Eskakizuna $eskakizuna = null) {
	$this->eskakizuna = $eskakizuna;
    }

    public function getImageThumbnail() {
	return $this->imageThumbnail;
    }

    public function setImageThumbnail($imageThumbnail) {
	$this->imageThumbnail = $imageThumbnail;
    }

    public function getImageThumbnailFile() {
	return $this->imageThumbnailFile;
    }

    public function getImageThumbnailSize() {
	return $this->imageThumbnailSize;
    }

     /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageThumbnailFile
     *
     * @return File
     */
    public function setImageThumbnailFile(File $imageThumbnailFile) {
	$this->imageThumbnailFile = $imageThumbnailFile;
	$this->imageThumbnail = 'thumb-'.$this->imageFile->getFilename();
	$this->imageThumbnailSize = $this->imageThumbnailFile->getSize();
	return $this;
    }

    public function setImageThumbnailSize($imageThumbnailSize) {
	$this->imageThumbnailSize = $imageThumbnailSize;
    }

    public function _toString() {
	return $this->imageName . "(" . $this->imageSize . ")";
    }

}