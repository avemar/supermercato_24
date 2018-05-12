<?php

namespace exercise_2\FileSystem;

class FS
{
    private $initialDirIndex = 0;
    private $dirIndex;
    private $currentPosition = [
        'dir_index',
        'hash_table_key',
    ];
    private $allowedCharacters = 'a-zA-Z';
    private $delimiter;
    private $pathTreeList = [];
    private $hashTable = [];
    private $isRoot = false;

    public function __construct(string $delimiter)
    {
        $this->dirIndex = $this->initialDirIndex;
        $this->setDelimiter($delimiter);
        $this->setCurrentPosition($this->dirIndex, $this->delimiter);
        $this->createRoot();
    }

    public function addPath(string $path)
    {
        $parsedPath = $this->parsePath($path);

        if ($this->hashTableKeyExists($path)) {
            return;
        }

        $tempPath = [];

        if (!$this->isRoot) {
            $tempPath = $this->getCurrentPosition('array');
        }

        foreach ($parsedPath as $dir) {
            $tempPath[] = $dir;

            if ($this->hashTableKeyExists($this->stringifyPath($tempPath))) {
                continue;
            }

            $this->insertDir($tempPath);
        }
        var_dump($this->isRoot);
    }

    public function cd(string $path)
    {
        $parsedPath = $this->parsePath($path);

        if ($this->isRoot) {
            // Fix is root case (check for existing / valid path)
            $this->setCurrentPosition($this->dirIndex, $this->delimiter);
        }

        // Create non root case
        // DONE!
    }

    public function getCurrentPath()
    {
        return $this->getCurrentPosition();
    }

    public function getDebugData(): array
    {
        return [
            'position' => $this->currentPosition,
            'pathTreeList' => $this->pathTreeList,
            'hashTable' => $this->hashTable,
        ];
    }

    private function setDelimiter(string $delimiter)
    {
        if (!$this->isDelimiterValid($delimiter)) {
            throw new Exception('Delimiter "' . $delimiter . '" is invalid');
        }

        $this->delimiter = $delimiter;
    }

    private function isDelimiterValid(string $delimiter): bool
    {
        return preg_match(
            '/^[' . $this->allowedCharacters . ']$/',
            $delimiter
        ) !== 1;
    }

    private function setCurrentPosition(int $dirIndex, string $hashTableKey)
    {
        $this->currentPosition = [
            'dir_index' => $dirIndex,
            'hash_table_key' => $hashTableKey,
        ];
    }

    private function createRoot()
    {
        $this->insertDir([$this->delimiter]);
    }

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

    private function addPathTreeListElem(
        int $key,
        string $dirName,
        int $parentKey
    ) {
        $this->pathTreeList[$key] = [
            'name' => $dirName,
            'parent' => $parentKey,
        ];
    }

    private function getDirNameFromPath(array $path): string
    {
        return array_values(array_slice($path, -1))[0];
    }

    private function getParentDirKey(array $path): int
    {
        if (count($path) === 1) {
            return $this->initialDirIndex;
        }

        array_pop($path);
        return $this->getHashTableElem($this->stringifyPath($path));
    }

    private function hashTableKeyExists(string $path): bool
    {
        return array_key_exists($path, $this->hashTable);
    }

    private function getHashTableElem(string $path): int
    {
        return $this->hashTable[$path];
    }

    private function addHashTableElem(string $path, int $index)
    {
        $this->hashTable[$path] = $index;
    }

    private function parsePath(string $path, bool $isCd = false): array
    {
        $this->isRoot = false;
        $dirs = explode($this->delimiter, $path);

        if (isset($dirs[0]) && $dirs[0] === '') {
            $this->isRoot = true;
            array_shift($dirs);
        }

        foreach ($dirs as $dir) {
            if (!$this->isValidDirName($dir, $isCd)) {
                throw new Exception('directory name "' . $dir . '" is invalid');
            }
        }

        return $dirs;
    }

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

    private function stringifyPath(array $path): string
    {
        return implode($this->delimiter, $path);
    }

    private function getCurrentPosition(string $type = null)
    {
        switch ($type) {
            case 'directory':
                return $this->pathTreeList[$this->currentPosition['dir_index']]['name'];

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
                    $this->currentPosition['hash_table_key']
                ];

                if ($path[0] !== $this->delimiter) {
                    array_unshift($path, $this->delimiter);
                }

                return implode('', $path);
        }
    }
}
