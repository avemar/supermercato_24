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

    public function cd(string $path)
    {
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

    private function setDelimiter(string $delimiter)
    {
        if (!$this->isDelimiterValid($delimiter)) {
            throw new \Exception('Delimiter "' . $delimiter . '" is invalid');
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
        $this->insertDir([
            $this->delimiter,
        ]);
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

    private function getPathFromDirIndex(int $index): string
    {
        return array_flip($this->hashTable)[$index];
    }

    private function parsePath(string $path, bool $isCd = false): array
    {
        $this->isRoot = false;
        $dirs = explode($this->delimiter, $path);

        if (isset($dirs[0]) && $dirs[0] === '') {

            $this->isRoot = true;
            array_shift($dirs);

            if ($isCd && count($dirs) === 1 && $dirs[0] === '') {
                return [
                    '/',
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
