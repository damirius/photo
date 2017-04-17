<?php
namespace AppBundle\Imagine\Binary\Loader;

use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

class UploadedLoader implements LoaderInterface
{
    /**
     * @var MimeTypeGuesserInterface
     */
    protected $mimeTypeGuesser;

    /**
     * @var ExtensionGuesserInterface
     */
    protected $extensionGuesser;

    /**
     * @param MimeTypeGuesserInterface  $mimeGuesser
     */
    public function __construct(MimeTypeGuesserInterface $mimeGuesser, ExtensionGuesserInterface $extensionGuesser) {
        $this->mimeTypeGuesser = $mimeGuesser;
        $this->extensionGuesser = $extensionGuesser;
    }

    /**
    * @param mixed $path
    *
    * @return BinaryInterface
    */
    public function find($path)
    {
        $data = file_get_contents($path->getRealPath());
        $mime = $this->mimeTypeGuesser->guess($path);

        // return binary instance with data
        return new Binary($data, $mime, $this->extensionGuesser->guess($mime));
    }
}