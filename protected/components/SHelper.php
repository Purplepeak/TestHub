<?php

class SHelper extends CApplicationComponent
{
    public static function deleteFolder($dir, $completeDelete = false)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($subDir = $dir . '/' . $object)) {
                        self::deleteFolder($subDir);
                        rmdir($subDir);
                    } else {
                        unlink($subDir);
                    }
                }
            }
            
            if($completeDelete) {
                rmdir($dir);
            }
        }
    }
}