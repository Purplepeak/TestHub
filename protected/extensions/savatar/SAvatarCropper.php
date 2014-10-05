<?php

class SAvatarCropper extends CApplicationComponent
{

    const MODE_SCALE = "scale";

    const MODE_CROP = "crop";

    private $thumbPath;

    public $sourceImage;

    private $sourceWidth;

    private $sourceHeight;

    public $imageMime;
    
    private $thumbImagePath;
    
    public $newImageName;
    
    /**
     * Если $saveAsJpeg = true, все картинки будут сохранятся с расширением jpg, с каким бы
     * расширением они к нам не поступили.
     */
    
    public $saveAsJpeg;
    
    /**
     * Свойство необходимо для получения измененного, в случае $saveAsJpeg = true, имени файла извне.
     */
    
    public $changedImageName;

    public function __construct($thumbPath, $saveAsJpeg = false)
    {
        $this->thumbPath = $thumbPath;
        $this->saveAsJpeg = $saveAsJpeg;
    }

    /**
     * Массив $allowedSizes содержит допустимые размеры превью и может быть получен методом setAllowedSizes().
     * Если ограничение установлено не было, разрешается создавать превью любых размеров.
     * Допустимые значения передаются setAllowedSizes() в виде массива:
     * array('100x100', '230x250' и т.д).
     */
    private $allowedSizes = array();

    public static function errorHeader($error)
    {
        $http_protocol = $_SERVER['SERVER_PROTOCOL'];
        $http = array(
            "200" => $http_protocol . " 200 OK",
            "404" => $http_protocol . " 404 Not Found",
            "500" => $http_protocol . " 500 Internal Server Error"
        );
        header($http[$error]);
    }

    /**
     * Метод создает ссылку на превью изображения.
     * Проверяем правильность режима сжатия
     * и было ли установлено ограничение по размерам превью.
     */
    public function link($image, $thumbWidth, $thumbHeight, $mode)
    {
        $link = $this->thumbPath . "/{$thumbWidth}x{$thumbHeight}/{$mode}/{$image}";
        
        if (($mode != self::MODE_SCALE) && ($mode != self::MODE_CROP)) {
            throw new SAvatarCropperException("Mode '{$mode}' is not available");
        }
        
        if (! $this->allowedSizes) {
            return $link;
        }
        
        if ($this->isAllowedSize($thumbWidth, $thumbHeight)) {
            return $link;
        } else {
            throw new SAvatarCropperException("Preview size '{$thumbWidth}x{$thumbHeight}' is not allowed");
        }
    }

    /**
     * Метод сохраняет превью в папку назначения.
     */
    private function saveImage($thumb, $thumbWidth, $thumbHeight, $mode, $image)
    {
        if(isset($this->newImageName)) {
            $imageName = $this->newImageName;
        } else {
            $imageName = basename($image);
        }
        
        if($this->saveAsJpeg) {
            switch($this->imageMime) {
            	case "image/jpeg":
            	    $ext = '.jpg';
            	    break;
            	case "image/png":
            	    $ext = '.png';
            	    break;
            	case "image/gif":
            	    $ext = '.gif';
            	default:
            	    throw new SAvatarCropperException("Invalid image type: {$this->imageMime}");
            	    break;
            }
            
            $imageName = basename($imageName, $ext) . '.jpg';
        }
        
        $this->changedImageName = $imageName;
        
        $width = round($thumbWidth);
        $height = round($thumbHeight);
        
        $dir = $this->thumbPath . "/{$width}x{$height}/{$mode}/";
        if (! file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $thumbImagePath = $dir . "/{$imageName}";
        
        //$this->thumbImagePath = $thumbImagePath;
        
        if($this->saveAsJpeg) {
            http_response_code(200);
            imagejpeg($thumb, $thumbImagePath);
        } else {
            switch ($this->imageMime) {
            	case "image/jpeg":
            	    http_response_code(200);
            	    imagejpeg($thumb, $thumbImagePath);
            	    break;
            	case "image/png":
            	    http_response_code(200);
            	    imagepng($thumb, $thumbImagePath);
            	    break;
            	case "image/gif":
            	    http_response_code(200);
            	    imagegif($thumb, $thumbImagePath);
            	    break;
            	default:
            	    throw new SAvatarCropperException("Invalid image type: {$this->imageMime}");
            	    break;
            }
        }
        
        return $thumbImagePath;
    }

    public function showImage($thumbImagePath)
    {
        header('Content-Type: ' . $this->imageMime);
        readfile($thumbImagePath);
    }

    public function getSourceImage($image)
    {
        if (! is_readable($image)) {
            self::errorHeader("404");
            throw new SAvatarCropperException("File {$image} not found");
        }
        
        $size = $this->getImageData($image);
        
        switch ($this->imageMime) {
            case "image/jpeg":
                $this->sourceImage = imagecreatefromjpeg($image);
                break;
            case "image/png":
                $this->sourceImage = imagecreatefrompng($image);
                break;
            case "image/gif":
                $this->sourceImage = imagecreatefromgif($image);
                break;
            default:
                self::errorHeader("500");
                throw new SAvatarCropperException("Invalid image type: {$this->imageMime}");
        }
        
        list ($this->sourceWidth, $this->sourceHeight) = $size;
    }
    
    public function getImageData($image) {
        $size = getimagesize($image);
        
        if (! $size) {
            self::errorHeader("500");
            throw new SAvatarCropperException("Read error {$image}");
        }
        
        $this->imageMime = $size['mime'];
        
        return $size;
    }

    public function getResizedImage($image, $thumbWidth, $thumbHeight, $mode)
    {
        $this->getSourceImage($image);
        
        $sourceRatio = $this->sourceWidth / $this->sourceHeight;
        $thumbRatio = $thumbWidth / $thumbHeight;
        
        switch ($mode) {
            case self::MODE_CROP:
                if ($this->sourceWidth <= $thumbWidth && $this->sourceHeight <= $thumbHeight) {
                    $newWidth = $this->sourceWidth;
                    $newHeight = $this->sourceHeight;
                } elseif ($sourceRatio >= $thumbRatio) {
                    $newHeight = $thumbHeight;
                    $newWidth = $thumbHeight * $sourceRatio;
                } else {
                    $newWidth = $thumbWidth;
                    $newHeight = $thumbWidth / $sourceRatio;
                }
                $thumb = $this->getTransparentThumb($thumbWidth, $thumbHeight);
                $thumbCoordinates = 0 - floor(($newWidth - $thumbWidth) / 2);
                imagecopyresampled($thumb, $this->sourceImage, $thumbCoordinates, $thumbCoordinates, 0, 0, $newWidth, $newHeight, $this->sourceWidth, $this->sourceHeight);
                break;
            
            case self::MODE_SCALE:
                if ($this->sourceWidth <= $thumbWidth && $this->sourceHeight <= $thumbHeight) {
                    $newWidth = $this->sourceWidth;
                    $newHeight = $this->sourceHeight;
                } elseif ($sourceRatio >= $thumbRatio) {
                    $newWidth = $thumbWidth;
                    $newHeight = $thumbWidth / $sourceRatio;
                } else {
                    $newHeight = $thumbHeight;
                    $newWidth = $thumbHeight * $sourceRatio;
                }
                $thumb = $this->getTransparentThumb($newWidth, $newHeight);
                imagecopyresampled($thumb, $this->sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $this->sourceWidth, $this->sourceHeight);
                break;
            default:
                return false;
        }
        
        $thumbPath = $this->saveImage($thumb, $thumbWidth, $thumbHeight, $mode, $image);
        $this->showImage($thumbPath);
    }

    public function setAllowedSizes($allowedSizes)
    {
        foreach ($allowedSizes as $value) {
            $sizeReg = '{(\\d+)x(\\d+)}';
            if (! preg_match($sizeReg, $value, $regArray)) {
                throw new SAvatarCropperException("Invalid format passed to the method setAllowedSizes(). 
                                             The argument should be an array: array('100x100', '230x250' etc)");
            }
            array_shift($regArray);
            $sizes = array_map('intval', $regArray);
            array_push($this->allowedSizes, $sizes);
        }
    }

    /**
     * Метод сравнивает допустимые параметры превью с указанными.
     */
    public function isAllowedSize($width, $height)
    {
        $results = array();
        
        foreach ($this->allowedSizes as $value) {
            if ($width == $value[0] && $height == $value[1]) {
                array_push($results, 1);
            } else {
                array_push($results, 0);
            }
        }
        
        if(in_array(1, $results)) {
            return true;
        } else {
            return false;
        }
    }

    private function getTransparentThumb($thumbWidth, $thumbHeight)
    {
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagealphablending($thumb, false);
        $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagesavealpha($thumb, true);
        imagefilledrectangle($thumb, 0, 0, $thumbWidth, $thumbHeight, $transparent);
        
        return $thumb;
    }

    public function cropWithCoordinates($image, $srcX, $srcY, $newWidth, $newHeight, $thumbWidth, $thumbHeight, $mode)
    {
        $this->getSourceImage($image);
        
        $thumb = $this->getTransparentThumb($thumbWidth, $thumbHeight);
        
        $thumbCoordinates = 0 - floor(($newWidth - $thumbWidth) / 2);
        
        imagecopy($thumb, $this->sourceImage, $thumbCoordinates, $thumbCoordinates, $srcX, $srcY, $this->sourceWidth, $this->sourceHeight);
        
        $this->saveImage($thumb, $thumbWidth, $thumbHeight, $mode, $image);
    }
}