<?php

/**
 * Basic virtual file system implementation, allowing for directories creation
 * and navigation only.
 *
 * @author  avemar vrsndr@gmail.com
 * @license WTFPL
 */

namespace exercise_2\FileSystem;

class FileSystem
{
    private $initialDirIndex = 0;
    private $dirIndex;
    private $currentPosition = [
        'dir_index',
        'hash_table_key',
    ];
    private $allowedCharacters = 'a-zA-Z';
    private $delimiter = '/';
    private $pathTreeList = [];
    private $hashTable = [];
    private $isRoot = false;

    /**
     * Constructor. It sets the starting index for new directories, the
     * delimiter, current position (a pointer to the current selected path /
     * directory) and takes care to create the root directory.
     *
     * @param   string  $delimiter (optional)   The delimiter to be used.
     */
    public function __construct(string $delimiter = null)
    {
        $this->dirIndex = $this->initialDirIndex;

        if (!is_null($delimiter)) {
            $this->setDelimiter($delimiter);
        }

        $this->setCurrentPosition($this->dirIndex, $this->delimiter);
        $this->createRoot();
    }

    /**
     * Inserts a single path in the file system. It looks up for existing
     * paths (avoids duplication) and populates the main data structures:
     * - the tree containing directories data;
     * - the hash table for fast lookup.
     *
     * @param   string  $path   The path to be inserted.
     */
    public function addPath(string $path)
    {
        $pathData = $this->getPathData($path);

        if ($pathData['path_exists'] === true) {
            return;
        }

        foreach ($pathData['parsed_path'] as $dir) {
            $pathData['temp_path'][] = $dir;

            if ($this->hashTableKeyExists(
                    $this->stringifyPath($pathData['temp_path'])
                )
            ) {
                continue;
            }

            $this->insertDir($pathData['temp_path']);
        }
    }

    /**
     * Changes the position in the file system.
     * It looks up for existing paths before beginning to parse and checking the
     * path one directory at a time.
     * It accepts ".." as a parent directory reference. Once the position is
     * at the root, subsequent parent references are ignored.
     *
     * @param   string  $path (optional)    The absolute or relative destination
     *                                      path. If not provided it will change
     *                                      move the position to the root
     *                                      directory.
     *
     * @throws  \Exception                  If path doesn't exist.
     */
    public function cd(string $path = null)
    {
        if (is_null($path)) {
            $path = $this->delimiter;
        }

        $pathData = $this->getPathData($path, true);

        if ($pathData['path_exists'] === true) {
            $stringifiedPath = $this->stringifyPath(
                array_merge($pathData['temp_path'], $pathData['parsed_path'])
            );
            $this->setCurrentPosition(
                $this->getHashTableElem($stringifiedPath),
                $stringifiedPath
            );

            return;
        }

        if (!in_array('..', $pathData['parsed_path'], true)) {
            throw new \Exception('This path does not exist');
        }

        foreach ($pathData['parsed_path'] as $dir) {
            if ($dir === '..') {
                array_pop($pathData['temp_path']);
                $nextDirIndex = $this->getCurrentPosition('parent');
                $stringifiedPath = $this->getPathFromDirIndex($nextDirIndex);
            } else {
                $pathData['temp_path'][] = $dir;
                $stringifiedPath = $this->stringifyPath($pathData['temp_path']);

                if (!$this->hashTableKeyExists($stringifiedPath)) {
                    throw new \Exception('This path does not exist');
                }

                $nextDirIndex = $this->getHashTableElem($stringifiedPath);
            }

            $this->setCurrentPosition($nextDirIndex, $stringifiedPath);
        }
    }

    /**
     * Retrieves the current full path string representation.
     *
     * @return  string  Current path.
     */
    public function getCurrentPath(): string
    {
        return $this->getCurrentPosition();
    }

    /**
     * Retrieves file system internal status.
     *
     * @return  array   Array containing file system internal status (current
     *                  position, directories tree and hash table).
     */
    public function getDebugData(): array
    {
        return [
            'position' => $this->currentPosition,
            'path_tree_list' => $this->pathTreeList,
            'hash_table' => $this->hashTable,
        ];
    }

    /**
     * Retrieves the delimiter.
     *
     * @return  string  The delimiter.
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * Performs the initial parsing, validation and path existence check at the
     * beginning of main commands (addPath() and cd()).
     *
     * @param   string  $path               The absolute or relative destination
     *                                      path.
     *
     * @param   bool    $isCd (optional)    Whether it's called by cd command or
     *                                      not.
     *
     * @return  array   $returnData         Array containing data about the path
     *                                      (if it already exists, the parsed
     *                                      and validated path and the temporary
     *                                      path).
     */
    private function getPathData(string $path, bool $isCd = false): array
    {
        $tempPath = [];
        $parsedPath = $this->parsePath($path, $isCd);

        if (!$this->isRoot) {
            $tempPath = $this->getCurrentPosition('array');
        }

        $stringifiedPath = $this->stringifyPath(
            array_merge($tempPath, $parsedPath)
        );

        $returnData = [
            'path_exists' => true,
            'parsed_path' => $parsedPath,
            'temp_path' => $tempPath,
        ];

        if ($this->hashTableKeyExists($stringifiedPath)) {
            return $returnData;
        }

        $returnData['path_exists'] = false;
        return $returnData;
    }

    /**
     * Validates and sets the delimiter.
     *
     * @param   string  $delimiter          The delimiter.
     *
     * @throws  \Exception                  If the delimiter is not allowed.
     */
    private function setDelimiter(string $delimiter)
    {
        if (!$this->isDelimiterValid($delimiter)) {
            throw new \Exception('Delimiter "' . $delimiter . '" is invalid');
        }

        $this->delimiter = $delimiter;
    }

    /**
     * Validates the delimiter.
     *
     * @param   string  $delimiter          The delimiter.
     *
     * @return  bool
     */
    private function isDelimiterValid(string $delimiter): bool
    {
        return preg_match(
            '/^[' . $this->allowedCharacters . ']$/',
            $delimiter
        ) !== 1;
    }

    /**
     * Sets the current position structure (directories tree index and
     * corresponding hash table key).
     *
     * @param   int     $dirIndex       Directories tree index.
     *
     * @param   string  $hashTableKey   Hash table key.
     */
    private function setCurrentPosition(int $dirIndex, string $hashTableKey)
    {
        $this->currentPosition = [
            'dir_index' => $dirIndex,
            'hash_table_key' => $hashTableKey,
        ];
    }

    /**
     * Creates the root directory.
     */
    private function createRoot()
    {
        $this->insertDir([
            $this->delimiter,
        ]);
    }

    /**
     * Inserts a new directory in both main structures.
     * It gets the specific directory from the entire path (the last element),
     * and then increments the index for subsequent insertions.
     *
     * @param   array   $path   Array containing the path segments.
     */
    private function insertDir(array $path)
    {
        $this->addPathTreeListElem(
            $this->dirIndex,
            $this->getDirNameFromPath($path),
            $this->getParentDirKey($path)
        );
        $this->addHashTableElem($this->stringifyPath($path), $this->dirIndex);
        $this->dirIndex++;
    }

    /**
     * Inserts a new directory into the directories tree.
     *
     * @param   int     $key        The new directory unique index.
     *
     * @param   string  $dirName    The name of the new directory.
     *
     * @param   int     $parentKey  The index of new directory's parent.
     */
    private function addPathTreeListElem(
        int $key,
        string $dirName,
        int $parentKey
    ) {
        $this->pathTreeList[$key] = [
            'name' => $dirName,
            'parent' => $parentKey,
            // This structure could contain further data
        ];
    }

    /**
     * Gets the current directory name from an array containing path segments.
     *
     * @param   array   $path       Array containing path segments.
     *
     * @return  string              The name of the directory.
     */
    private function getDirNameFromPath(array $path): string
    {
        return array_values(array_slice($path, -1))[0];
    }

    /**
     * Gets the parent directory index, starting from an array containing path
     * segments.
     *
     * @param   array   $path       Array containing path segments.
     *
     * @return  int                 Parent directory index.
     */
    private function getParentDirKey(array $path): int
    {
        if (count($path) === 1) {
            return $this->initialDirIndex;
        }

        array_pop($path);
        return $this->getHashTableElem($this->stringifyPath($path));
    }

    /**
     * Checks for path existence.
     *
     * @param   string  $path   String representation of a full path.
     *
     * @return  bool
     */
    private function hashTableKeyExists(string $path): bool
    {
        return array_key_exists($path, $this->hashTable);
    }

    /**
     * Gets the last path segment corresponding index.
     *
     * @param   string  $path   String representation of a full path.
     *
     * @return  int             The directory index.
     */
    private function getHashTableElem(string $path): int
    {
        return $this->hashTable[$path];
    }

    /**
     * Creates a new entry into the hash table. Path will be the key and the
     * directories tree index will be the value.
     *
     * @param   string  $path   String representation of a full path.
     *
     * @param   int     $index  The directory index.
     */
    private function addHashTableElem(string $path, int $index)
    {
        $this->hashTable[$path] = $index;
    }

    /**
     * Retrieves a full path from a directory index.
     *
     * @param   int     $index  The directory index.
     *
     * @param   string          Corresponding full path.
     */
    private function getPathFromDirIndex(int $index): string
    {
        return array_flip($this->hashTable)[$index];
    }

    /**
     * Parses and validates a path. It detects if a path is absolute or relative
     * and if it's called from cd() it relaxes checks (delimiter and empty paths
     * are valid in this case).
     *
     * @param   string  $path               Path to be parsed.
     *
     * @param   bool    $isCd (optional)    Whether it's called by cd command or
     *                                      not.
     *
     * @throws \Exception                   If directory name is not allowed.
     *
     * @return  array   $dirs               Array containing parsed path
     *                                      segments.
     */
    private function parsePath(string $path, bool $isCd = false): array
    {
        $this->isRoot = false;
        $dirs = explode($this->delimiter, $path);

        if (isset($dirs[0]) && $dirs[0] === '') {

            $this->isRoot = true;
            array_shift($dirs);

            if ($isCd && count($dirs) === 1 && $dirs[0] === '') {
                return [
                    $this->delimiter,
                ];
            }
        }

        foreach ($dirs as $dir) {
            if (!$this->isValidDirName($dir, $isCd)) {
                throw new \Exception('directory name "' . $dir . '" is invalid');
            }
        }

        return $dirs;
    }

    /**
     * Validates the directory name. If it's called from cd() it allows parent
     * reference ("..").
     *
     * @param   string  $dir                Name of directory.
     *
     * @param   bool    $isCd (optional)    Whether it's called by cd command or
     *                                      not.
     *
     * @return  bool
     */
    private function isValidDirName(string $dir, bool $isCd = false): bool
    {
        $pattern = '/^[' . $this->allowedCharacters . ']{1,200}$/';

        if ($isCd) {
            $pattern = '/^[' . $this->allowedCharacters . ']{1,200}$|^\.{2}$/';
        }

        return preg_match(
            $pattern,
            $dir
        ) === 1;
    }

    /**
     * Creates a string representation of an array containing path segments.
     *
     * @param   array   $path               Array containing path segments.
     *
     * @return  string                      Path string representation.
     */
    private function stringifyPath(array $path): string
    {
        return implode($this->delimiter, $path);
    }

    /**
     * Retrieves the current position. Different formats are possible, according
     * to $type.
     * Default format is the string representation of the path, as displayed by
     * getCurrentPath().
     *
     * @param   string  $type (optional)    The requested format of current
     *                                      position.
     *
     * @return  mixed
     */
    private function getCurrentPosition(string $type = null)
    {
        switch ($type) {
            case 'directory':
                return $this->pathTreeList[$this->currentPosition['dir_index']]['name'];

            case 'parent':
                return $this->pathTreeList[$this->currentPosition['dir_index']]['parent'];

            case 'array':
                if ($this->currentPosition['hash_table_key'] === $this->delimiter) {
                    return [];
                }

                return explode(
                    $this->delimiter,
                    $this->currentPosition['hash_table_key']
                );

            default:
                $path = [
                    $this->currentPosition['hash_table_key'],
                ];

                if ($path[0] !== $this->delimiter) {
                    array_unshift($path, $this->delimiter);
                }

                return implode('', $path);
        }
    }
}
