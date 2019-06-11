<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    private $file;

    private $path;


    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function getFile()
    {
        return $this->file;
    }


    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @ORM\PreFlush()
     */
    public function handle() {

        // si il n'y a pas d'action de soumission de formulaire, je fais rien
        if ($this->file === null) {
            return;
        }

        // si j'edit un fichier
        if ($this->id) {
            // recupere le fichier de l'image en modification, et le supprime
            unlink($this->path.'/'.$this->name());
        }

        // cree un nom unique a l'image soumis par l'utilisateur
        $name = $this->createName();

        // donne le nom a l'image soumis
        $this->setName($name);

        $this->file->move($this->path, $name);
    }

    // donne un nom unique a l'image soumis
    private function createName(): string
    {

        return md5(uniqid()). $this->file->getClientOriginalName();
    }

}
