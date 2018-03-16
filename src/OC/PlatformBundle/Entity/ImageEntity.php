<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ImageEntity
 *
 * @ORM\Table(name="oc_image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageEntityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ImageEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    private $file;

    // Nom de fichier temporaire
    private $tempFilename;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        // On vérifie si l'on a déjà un fichier pour cette entité
        if($this->url !== null) {
            $this->tempFilename = $this->url;

            // On réinitialise les valeurs des attributs url et alt
            $this->url = null;
            $this->alt = null;
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if($this->file === null) {
            return;
        }

        $this->url = $this->file->guessExtension();
        $this->alt = $this->file->getClientOriginalName();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if($this->file === null) {
            return;
        }

        // SI ancien fichier existe, on supp
        if($this->tempFilename !== null) {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if(file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Sinon on déplace dans un rép choisi
        $this->file->move(
            $this->getUploadRootDir(), // Répertoire de destination
            $this->id.'.'.$this->url // Nom du fichier crée : id.extension
        );
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // Sauvegarde temporaire du nom de fichier
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // On reprend la sauvegarde temporaire du nom pour supp le fichier
        if(file_exists($this->tempFilename)) {
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image
        return 'uploads/img';
    }

    public function getUploadRootDir()
    {
        // Chemin relatif vers image pour code PHP
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getUrl();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return ImageEntity
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return ImageEntity
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }
}

