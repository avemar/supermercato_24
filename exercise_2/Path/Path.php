<?php

namespace exercise_2\Path;

use exercise_2\FileSystem\FS;

class Path
{
    private $fileSystem;

    public function __construct(array $paths = [], string $delimiter = '/')
    {
        $this->fileSystem = new FS($delimiter);

        foreach ($paths as $path) {
            $this->fileSystem->addPath($path);
        }
    }

    public function cd(string $path)
    {
        $this->fileSystem->cd($path);
    }

    public function currentPath()
    {
        return $this->fileSystem->getCurrentPath();
    }

    public function getFSData()
    {
        return $this->fileSystem->getDebugData();
    }
}
