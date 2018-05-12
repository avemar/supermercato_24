<?php

namespace exercise_2/FileSystem;

class FS
{
    private $dirIndex = 0;
    private $currentPosition = [
        'dirIndex',
        'hashTableKey',
    ];
    private $allowedCharacters = 'a-zA-Z';
    private $delimiter;
    private $pathTreeList = [];
    private $hashTable = [];
    private $isRoot = false;

    public function __construct(string $delimiter = '/')
    {
        $this->setDelimiter($delimiter);
        $this->setCurrentPosition($this->dirIndex, $this->delimiter);
        $this->createRoot();
    }

    public function addPath(string $path)
    {
        $parsedPath = $this->parsePath($path);

        if (hashTableElemExists($path)) {
            return;
        }

        $tempPath = [];

        foreach($parsedPath as $dir) {
            $tempPath[] = $dir;
            $tempPosition[] = $this->dirIndex;

            if (hashTableElemExists($tempPath)) {
                continue;
            }

            $this->insertDir($tempPath);
        }
    }

    private function setDelimiter(string $delimiter)
    {
        if (!$this->isDelimiterValid($delimiter)) {
            throw new Exception('Delimiter "' . $delimiter . '" is invalid');
        }

        $this->delimiter = $delimiter;
    }

    private function isDelimiterValid(string $delimiter)
    {
        return preg_match(
            '/[' . $this->allowedCharacters . ']/',
            $delimiter
        ) !== 1;
    }

    private function setCurrentPosition(int $dirIndex, string $hashTableKey)
    {
        $this->currentPosition = [
            'dirIndex' => $dirIndex,
            'hashTableKey' => $hashTableKey,
        ];
    }

    private createRoot()
    {
        $this->insertDir([$this->delimiter]);
    }

    private insertDir(array $path)
    {
        $this->addPathTreeListElem(
            $this->dirIndex,
            array_values(array_slice($path, -1))[0]
        );
        $stringifiedPath = implode($this->delimiter, $path);
        $this->addHashTableElem($stringifiedPath, $this->dirIndex);
        $this->dirIndex++;
    }

    private addPathTreeListElem(int $key, string $dirName)
    {
        $this->pathTreeList[$key] = [
            'name' => $dirName,
        ];
    }

    private hashTableKeyExists(string $path)
    {
        return array_key_exists($path, $this->hashTable);
    }

    private addHashTableElem(string $path, int $position)
    {
        $this->hashTable[$path] = $position;
    }

    private function parsePath(string $path)
    {
        $dirs = explode($delimiter, $path);

        if (isset($dirs[0]) && $dirs[0] === '') {
            $this->isRoot = true;
            array_shift($dirs);
        }

        foreach ($dirs as $dir) {
            if (!$this->isValidDirName($dir)) {
                throw new Exception('directory name "' . $dir . '" is invalid');
            }
        }
    }

    private function isValidDirName(string $dir)
    {
        return preg_match(
            '/[' . $this->allowedCharacters . ']/',
            $dir
        ) === 1;
    }
}
