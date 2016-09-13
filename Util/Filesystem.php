<?php

namespace SAPF\Util;

class Filesystem
{

    public static function file($dirPath, $fileName)
    {
        if (StringUtils::endsWith($dirPath, DIRECTORY_SEPARATOR)) {
            return $dirPath . $fileName;
        }
        return $dirPath . DIRECTORY_SEPARATOR . $fileName;
    }

    public static function create($path, $chmod = 0770)
    {
        if (!file_exists(dirname($path))) {
            self::create(dirname($path), $chmod);
        }

        if (!file_exists($path)) {
            if (!@mkdir($path, 0777)) {
                throw new \LogicException("Unable to create dir '$path'.");
            }
            if (!chmod($path, $chmod)) {
                throw new \LogicException("Unable to chmod dir '$path'.");
            }
        }
    }

    public static function delete($path)
    {
        if (is_file($path) || is_link($path)) {
            $func = DIRECTORY_SEPARATOR === '\\' && is_dir($path) ? 'rmdir' : 'unlink';
            if (!@$func($path)) { // @ is escalated to exception
                throw new \LogicException("Unable to delete '$path'.");
            }
        }
        elseif (is_dir($path)) {
            foreach (new \FilesystemIterator($path) as $item) {
                static::delete($item->getPathname());
            }
            if (!@rmdir($path)) { // @ is escalated to exception
                throw new \LogicException("Unable to delete directory '$path'.");
            }
        }
    }

}
