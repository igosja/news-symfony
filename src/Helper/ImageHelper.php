<?php
declare(strict_types=1);

namespace App\Helper;

use common\services\ResizeImageService;

/**
 * Class ImageHelper
 * @package common\helpers
 */
class ImageHelper
{
    /**
     * <img src="<?= ImageHelper::resize($image_id, $width, $height); ?>">
     *
     * @param int $image_id
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function resize(int $image_id, int $width, int $height): string
    {
        $service = new ResizeImageService($image_id, $width, $height);
        if (!$service->execute()) {
            return $service->getImage()->path ?: '';
        }
        return $service->getResize()->path;
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
