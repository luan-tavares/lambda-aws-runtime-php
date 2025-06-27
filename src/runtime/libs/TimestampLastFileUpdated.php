<?php

namespace Runtime;

use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

abstract class TimestampLastFileUpdated
{
    public static function get(string $path): int
    {
        $latest = 0;

        if (!is_dir($path)) {
            throw new Exception("{$path} is not DIR");
        }

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        foreach ($rii as $file) {
            if ($file->isFile()) {
                $mtime = $file->getMTime();
                if ($mtime > $latest) {
                    $latest = $mtime;
                }
            }
        }

        return $latest;
    }
}
