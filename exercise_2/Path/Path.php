<?php

namespace exercise_2\Path;

use exercise_2\FileSystem\FS;

class Path
{
    private $fileSystem;

    public function __construct(array $paths = [], string $delimiter = '/')
    {
        try {
            $this->fileSystem = new FS($delimiter);
        } catch (\Exception $e) {
            echo $e;
        }
        $this->addPaths($paths);
    }

    public function cd(string $path = '/')
    {
        try {
            $this->fileSystem->cd($path);
        } catch (\Exception $e) {
            echo $e;
        }
    }

    public function addPaths(array $paths = [])
    {
        foreach ($paths as $path) {
            try {
                $this->fileSystem->addPath($path);
            } catch (\Exception $e) {
                echo $e;
            }
        }
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
