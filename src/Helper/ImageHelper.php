<?php
declare(strict_types=1);

namespace App\Helper;

use App\Entity\Image;
use App\Service\ResizeImageService;

/**
 * Class ImageHelper
 * @package common\helpers
 */
class ImageHelper
{
    /**
     * @var \App\Service\ResizeImageService
     */
    private ResizeImageService $resizeImageService;

    /**
     * @param \App\Service\ResizeImageService $resizeImageService
     */
    public function __construct(ResizeImageService $resizeImageService)
    {
        $this->resizeImageService = $resizeImageService;
    }

    /**
     * <img src="<?= ImageHelper()->resize($image, $width, $height); ?>">
     *
     * @param \App\Entity\Image $image
     * @param int $width
     * @param int $height
     * @return string
     */
    public function resize(Image $image, int $width, int $height): string
    {
        return $this->resizeImageService->execute($image, $width, $height);
    }

    /**
     * @return array
     */
    public static function generatePath(): array
    {
        $path = [];
        $path[] = substr(md5(uniqid((string)mt_rand(), true)), -10);
        $path[] = substr(md5(uniqid((string)mt_rand(), true)), -10);
        $path[] = substr(md5(uniqid((string)mt_rand(), true)), -10);

        return $path;
    }
}
