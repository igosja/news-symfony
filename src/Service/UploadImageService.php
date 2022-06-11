<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use App\Helper\ImageHelper;
use App\Repository\ImageRepository;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageService
{
    /**
     * @var ImageRepository $imageRepository
     */
    private ImageRepository $imageRepository;

    /**
     * @param \App\Repository\ImageRepository $imageRepository
     */
    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string $directory
     * @return \App\Entity\Image
     */
    public function upload(UploadedFile $file, string $directory): Image
    {
        $file_name = substr(md5(uniqid('', true)), -20) . '.' . $file->guessExtension();
        $path = ImageHelper::generatePath();
        $upload_dir = $directory . '/' . implode('/', $path);
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $upload_dir));
            }
        }

        $upload_url = $upload_dir . '/' . $file_name;
        $file_url = implode('/', $path) . '/' . $file_name;
        if ($file->move($upload_dir, $file_name)) {
            chmod($upload_url, 0777);
        }

        $image = new Image();
        $image->setCreatedAt(time());
        $image->setUpdatedAt(time());
        $image->setPath($file_url);

        $this->imageRepository->add($image, true);

        return $image;
    }
}