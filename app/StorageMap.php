<?php

declare(strict_types=1);


namespace Matchmaker;


class StorageMap
{
    private static array $storageMap = [
        'uploads' => __DIR__ . '/../storage/uploads',
        'test' => __DIR__ . '/../storage/test',
        'disk3' => __DIR__ . '/../storage/test2',
    ];

    private static array $imageDirMap = [
        'uploads' => '/static/images',
        'test' => '/static/images2',
        'disk3' => '/static/images3',
    ];

    public static function getPath(string $storage): string
    {
        return self::$storageMap[$storage];
    }

    public static function getImageDir(string $storage): string
    {
        return self::$imageDirMap[$storage];
    }
}
