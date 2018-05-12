<?php

namespace exercise_2/Path;

use exercise_2/FileSystem/FS;

class Path
{
    private $fileSystem;

    public function __construct(array $paths = [], string $delimiter = null)
    {
        $this->fileSystem = new FS($delimiter);

        foreach ($paths as $path) {
            $this->fileSystem->addPath($path);
        }
    }

    public function cd(string $path)
    {
        if (!isset($this->fileSystem)) {
            throw new Exception('No path created');
        }
    }
}
