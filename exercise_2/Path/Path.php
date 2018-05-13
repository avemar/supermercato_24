<?php

/**
 * Facade class for interacting with underlying file system.
 *
 * @author avemar vrsndr@gmail.com
 * @license WTFPL
 */

namespace exercise_2\Path;

use exercise_2\FileSystem\FileSystem;

class Path
{
    private $fileSystem;

    /**
     * Constructor.
     *
     * @param   array   $paths (optional)       Paths to be created. It accepts
     *                                          paths as strings
     *                                          i.e. ['/a/b/c', 'e/f'].
     *                                          In this case the presence or
     *                                          absence of leading delimiter
     *                                          doesn't change the insertion
     *                                          method: all paths are absolute.
     *
     * @param   string  $delimiter (optional)   The delimiter to be used.
     */
    public function __construct(array $paths = [], string $delimiter = null)
    {
        try {
            $this->fileSystem = new FileSystem($delimiter);
        } catch (\Exception $e) {
            echo $e;
        }

        $this->addPaths($paths);
    }

    /**
     * Change directory command.
     *
     * @param   string  $path (optional)    The absolute or relative destination
     *                                      path. It accepts ".." as a parent
     *                                      directory reference.
     *                                      If not provided it fallbacks to the
     *                                      file system implementation.
     *
     * @return  Path
     */
    public function cd(string $path = null)
    {
        try {
            $this->fileSystem->cd($path);
        } catch (\Exception $e) {
            echo $e;
        }

        return $this;
    }

    /**
     * Creates new paths into the file system.
     *
     * @param   array   $paths (optional)   Paths to be created. Paths will be
     *                                      created according to the current
     *                                      position in file system, thus
     *                                      creating a path with a leading
     *                                      delimiter it leads to a different
     *                                      outcome.
     *
     * @return  Path
     */
    public function addPaths(array $paths = [])
    {
        foreach ($paths as $path) {
            try {
                $this->fileSystem->addPath($path);
            } catch (\Exception $e) {
                echo $e;
            }
        }

        return $this;
    }

    /**
     * Retrieves the current full path string representation.
     *
     * @return  string  Current path.
     */
    public function currentPath(): string
    {
        return $this->fileSystem->getCurrentPath();
    }

    /**
     * Retrieves file system internal status. Useful for debugging.
     *
     * @return  array   Array containing file system internal status.
     */
    public function getFileSystemData(): array
    {
        return $this->fileSystem->getDebugData();
    }
}
