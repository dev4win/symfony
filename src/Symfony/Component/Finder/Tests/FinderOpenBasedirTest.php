<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests;

use Symfony\Component\Finder\Finder;

class FinderOpenBasedirTest extends Iterator\RealIteratorTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testIgnoreVCSIgnoredWithOpenBasedir()
    {
        $this->markTestIncomplete('Test case needs to be refactored so that PHPUnit can run it');

        if (\ini_get('open_basedir')) {
            $this->markTestSkipped('Cannot test when open_basedir is set');
        }

        $finder = $this->buildFinder();
        $this->assertSame(
            $finder,
            $finder
                ->ignoreVCS(true)
                ->ignoreDotFiles(true)
                ->ignoreVCSIgnored(true)
        );

        $openBaseDir = \dirname(__DIR__, 5).\PATH_SEPARATOR.self::toAbsolute('gitignore/search_root');

        if ($deprecationsFile = getenv('SYMFONY_DEPRECATIONS_SERIALIZE')) {
            $openBaseDir .= \PATH_SEPARATOR.$deprecationsFile;
        }

        $oldOpenBaseDir = ini_set('open_basedir', $openBaseDir);

        try {
            $this->assertIterator(self::toAbsolute([
                'gitignore/search_root/b.txt',
                'gitignore/search_root/c.txt',
                'gitignore/search_root/dir',
                'gitignore/search_root/dir/a.txt',
                'gitignore/search_root/dir/c.txt',
            ]), $finder->in(self::toAbsolute('gitignore/search_root'))->getIterator());
        } finally {
            ini_set('open_basedir', $oldOpenBaseDir);
        }
    }

    protected function buildFinder()
    {
        return Finder::create()->exclude('gitignore');
    }
}
