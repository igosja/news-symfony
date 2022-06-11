<?php
declare(strict_types=1);

namespace App\Service;

use common\helpers\ImageHelper;
use common\interfaces\ExecuteInterface;
use common\models\db\Image;
use common\models\db\Resize;
use RuntimeException;
use Yii;

/**
 * Class ResizeImageService
 * @package common\services
 */
class ResizeImageService
{
    /**
     * @var int $_height
     */
    private int $_height;

    /**
     * @var \common\models\db\Image|null $_image
     */
    private ?Image $_image = null;

    /**
     * @var int $_image_id
     */
    private int $_image_id;

    /**
     * @var \common\models\db\Resize|null $_resize
     */
    private ?Resize $_resize = null;

    /**
     * @var string $_resize_path
     */
    private string $_resize_path = '';

    /**
     * @var int $_width
     */
    private int $_width;

    /**
     * @param int $image_id
     * @param int $width
     * @param int $height
     */
    public function __construct(int $image_id, int $width, int $height)
    {
        $this->_image_id = $image_id;
        $this->_height = $height;
        $this->_width = $width;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        $this->loadResize();
        if ($this->_resize) {
            return true;
        }

        $size_h = $this->_height;
        $size_w = $this->_width;

        $this->loadImage();
        if (!$this->_image) {
            return true;
        }

        $image_path = Yii::getAlias('@frontend') . '/web' . $this->_image->path;
        $image_info = getimagesize($image_path);

        [$image_width, $image_height] = $image_info;

        $h_coefficient = $size_h / $image_height;
        $w_coefficient = $size_w / $image_width;

        if ($h_coefficient > $w_coefficient) {
            $size_w_new = $image_width * $h_coefficient;
            $size_h_new = $size_h;
        } else {
            $size_h_new = $image_height * $w_coefficient;
            $size_w_new = $size_w;
        }

        if ($image_info[2] === IMAGETYPE_JPEG) {
            $src = imagecreatefromjpeg($image_path);
        } elseif ($image_info[2] === IMAGETYPE_GIF) {
            $src = imagecreatefromgif($image_path);
        } else {
            $src = imagecreatefrompng($image_path);
        }

        $im = imagecreatetruecolor($size_w, $size_h);
        $back = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $back);

        $file_name = substr(md5(uniqid('', true)), -20) . '.jpg';

        $path = ImageHelper::generatePath();

        $upload_dir = Yii::getAlias('@frontend') . '/web/upload/' . implode('/', $path);

        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $upload_dir));
            }
        }

        $file_url = $upload_dir . '/' . $file_name;

        $offset_x = ($size_w_new - $size_w) / $h_coefficient / 2;

        if (0 > $offset_x) {
            $offset_x = -$offset_x;
        }

        $offset_y = ($size_h_new - $size_h) / $w_coefficient / 2;

        if (0 > $offset_y) {
            $offset_y = -$offset_y;
        }

        imagecopyresampled($im, $src, 0, 0, (int)$offset_x, (int)$offset_y, (int)$size_w_new, (int)$size_h_new, imagesx($src), imagesy($src));

        if (imagejpeg($im, $file_url, 100)) {
            chmod($file_url, 0777);
        }

        imagedestroy($im);

        $this->_resize_path = str_replace(Yii::getAlias('@frontend') . '/web', '', $file_url);

        return $this->saveResize();
    }

    /**
     * @return void
     */
    private function loadResize(): void
    {
        $this->_resize = Resize::find()
            ->andWhere([
                'height' => $this->_height,
                'image_id' => $this->_image_id,
                'width' => $this->_width,
            ])
            ->one();
    }

    /**
     * @return void
     */
    private function loadImage(): void
    {
        $this->_image = Image::find()
            ->andWhere(['id' => $this->_image_id])
            ->one();
    }

    /**
     * @return bool
     */
    private function saveResize(): bool
    {
        $this->_resize = new Resize();
        $this->_resize->height = $this->_height;
        $this->_resize->image_id = $this->_image_id;
        $this->_resize->path = $this->_resize_path;
        $this->_resize->width = $this->_width;
        return $this->_resize->save();
    }

    /**
     * @return \common\models\db\Image|null
     */
    public function getImage(): ?Image
    {
        return $this->_image;
    }

    /**
     * @return \common\models\db\Resize|null
     */
    public function getResize(): ?Resize
    {
        return $this->_resize;
    }
}