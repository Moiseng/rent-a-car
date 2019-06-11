<?php


namespace App\Services;


use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageHandler
{

    private $path;

    public function __construct($path)
    {
        $this->path = $path.'/public/images';
    }


    public function handle(Image $image) {

        // recupere le fichier soumis par l'utilisateur
        /** @var UploadedFile $file */
        $file = $image->getFile();

        // cree un nom unique a l'image soumis par l'utilisateur
        $name = $this->createName($file);

        // donne le nom a l'image soumis
        $image->setName($name);

        $file->move($this->path, $name);


    }

    // donne un nom unique a l'image soumis
    private function createName(UploadedFile $file): string
    {

        return md5(uniqid()). $file->getClientOriginalName(). '.' .$file->guessExtension();
    }

}