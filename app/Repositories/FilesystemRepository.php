<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use Intervention\Image\Image;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;


class FilesystemRepository
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @throws FilesystemException
     */
    public function deleteAndClean(string $location): void
    {
        $location = $this->encodePath($location);
        $this->filesystem->delete($location);

        $location = substr($location, 0, 5);

        if (0 === count($this->filesystem->listContents($location)->toArray())) {
            $this->filesystem->deleteDirectory($location);
        }

        $location = substr($location, 0, 2);

        if (0 === count($this->filesystem->listContents($location)->toArray())) {
            $this->filesystem->deleteDirectory($location);
        }
    }

    private function encodePath(string $encodedName): string
    {
        return sprintf(
            "%s/%s/%s",
            substr($encodedName, 0, 2),
            substr($encodedName, 2, 2),
            $encodedName,
        );
    }

    /**
     * @throws FilesystemException
     */
    public function write(string $name, Image $image): void
    {
        $this->filesystem->writeStream($this->encodePath($name), $image->stream()->detach());
    }
}
