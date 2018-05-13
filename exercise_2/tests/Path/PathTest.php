<?php

namespace exercise_2\Path;

use PHPUnit\Framework\TestCase;
use exercise_2\Path\Path;

class PathTest extends TestCase
{
    private $path;

    public function tearDown()
    {
        unset($this->path);
    }

    public function testCreateEmptyPath()
    {
        $this->path = new Path();
        $this->assertTrue($this->onlyRootCreated());
    }

    /**
     * @dataProvider validPathProvider
     */
    public function testCreateValidPaths(array $paths)
    {
        $this->path = new Path($paths);
        $fsData = $this->path->getFileSystemData();
        $this->assertTrue(
            count($fsData['hash_table']) === count($fsData['path_tree_list'])
        );
        $this->assertEquals(
            [
                0 => [
                    'name' => '/',
                    'parent' => 0,
                ],
                1 => [
                    'name' => 'a',
                    'parent' => 0,
                ],
                2 => [
                    'name' => 'b',
                    'parent' => 1,
                ],
                3 => [
                    'name' => 'c',
                    'parent' => 2,
                ],
                4 => [
                    'name' => 'd',
                    'parent' => 3,
                ],
                5 => [
                    'name' => 'c',
                    'parent' => 0,
                ],
                6 => [
                    'name' => 'd',
                    'parent' => 5,
                ],
                7 => [
                    'name' => 'e',
                    'parent' => 6,
                ],
                8 => [
                    'name' => 'f',
                    'parent' => 7,
                ],
                9 => [
                    'name' => 'p',
                    'parent' => 0,
                ],
                10 => [
                    'name' => 'q',
                    'parent' => 9,
                ],
            ],
            $fsData['path_tree_list'],
        );
    }

    /**
     * @dataProvider invalidPathProvider
     */
    public function testCreateInvalidPaths(array $paths)
    {
        $this->path = new Path($paths);
        $this->assertTrue($this->onlyRootCreated());
    }

    /**
     * @dataProvider validAndInvalidPathProvider
     */
    public function testCreateValidAndInvalidPaths(array $paths)
    {
        $this->path = new Path($paths);
        $fsData = $this->path->getFileSystemData();
        $this->assertTrue(count($fsData['hash_table']) === 6);
    }

    /**
     * @dataProvider validCdProvider
     */
    public function testValidCd(array $paths)
    {
        $this->path = new Path([
            '/a/b/c/d',
            'c/d/e/f',
            '/p/q',
        ]);

        foreach ($paths as $path) {
            $this->path->cd($path);
        }

        $this->assertTrue($this->path->currentPath() === '/c/d');
    }

    /**
     * @dataProvider validAndinvalidCdProvider
     */
    public function testValidAndInvalidCd(array $paths)
    {
        $this->path = new Path([
            '/a/b/c/d',
            'c/d/e/f',
            '/p/q',
        ]);

        foreach ($paths as $path) {
            $this->path->cd($path);
        }

        $this->assertTrue($this->path->currentPath() === '/c/d/e');
    }

    public function validPathProvider()
    {
        return [[[
            '/a/b/c/d',
            'c/d/e/f',
            '/p/q',
        ]]];
    }

    public function invalidPathProvider()
    {
        return [[[
            '/a/b/c/d/',
            '//',
            '/',
            '/a/b_/c/d',
            '..',
        ]]];
    }

    public function validAndInvalidPathProvider()
    {
        return [[[
            '/a/b/c/d/',
            '/a/b/c',
            '/',
            '/a/b/c/d/e',
        ]]];
    }

    public function validCdProvider()
    {
        return [[[
            'a/b/c',
            null,
            '/c/d',
            'e/f',
            '../..',
        ]]];
    }

    public function validAndinvalidCdProvider()
    {
        return [[[
            '',
            '/c/d/e',
            'c/d',
            'h/j',
            'h/?',
        ]]];
    }

    private function onlyRootCreated()
    {
        $fsData = $this->path->getFileSystemData();

        return ($fsData['hash_table'][$this->path->getDelimiter()] === 0 &&
            $fsData['path_tree_list'][0]['name'] === $this->path->getDelimiter() &&
            count($fsData['path_tree_list']) === 1);
    }
}
