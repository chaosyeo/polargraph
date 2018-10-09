<?php

namespace Polargraph;

use Phar;
use FilesystemIterator;
use Symfony\Component\Finder\Finder;
use SplFileInfo;

class Compiler
{
    public function execute($filename = 'polargraph.phar')
    {
        if(file_exists($filename)) {
            unlink($filename);
        }

        $phar = new Phar($filename,
            FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME,
            'polargraph.phar');
        $phar->setSignatureAlgorithm(Phar::SHA256);
        $phar->startBuffering();

        $sort = function(SplFileInfo $alpha, SplFileInfo $bravo) {
            return strcmp(strtr($alpha->getRealPath(), '\\', '/'), strtr($bravo->getRealPath(), '\\', '/'));
        };

        $finder = new Finder();
        $finder->ignoreVCS(true)->name('*.php')
            ->in(__DIR__ . '/../')
            ->notName('Compiler.php')
            ->sort($sort);
        foreach($finder as $file) {
            $this->archive($phar, $file);
        }

        $finder = new Finder();
        $finder->ignoreVCS(true)->name('*.php')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->in(__DIR__ . '/../../vendor/symfony')
            ->sort($sort);
        foreach($finder as $file) {
            $this->archive($phar, $file);
        }

        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/autoload.php'));
        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/autoload_namespaces.php'));
        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/autoload_psr4.php'));
        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/autoload_classmap.php'));
        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/autoload_files.php'));
        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/autoload_real.php'));
        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/autoload_static.php'));
        if (file_exists(__DIR__.'/../../vendor/composer/include_paths.php')) {
            $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/include_paths.php'));
        }
        $this->archive($phar, new SplFileInfo(__DIR__.'/../../vendor/composer/ClassLoader.php'));

        $this->archiveCli($phar);

        $phar->setStub($this->stub());

        $phar->stopBuffering();
        unset($phar);

    }

    private function stub()
    {
        $stub = <<<'EOF'
#!/usr/bin/env php
<?php

if (extension_loaded('apc') && ini_get('apc.enable_cli') && ini_get('apc.cache_by_default')) {
    if (version_compare(phpversion('apc'), '3.0.12', '>=')) {
        ini_set('apc.cache_by_default', 0);
    } else {
        fwrite(STDERR, 'Warning: APC <= 3.0.12 may cause fatal errors when running commands.'.PHP_EOL);
        fwrite(STDERR, 'Update APC, or set apc.enable_cli or apc.cache_by_default to 0 in your php.ini.'.PHP_EOL);
    }
}
Phar::mapPhar('polargraph.phar');
require 'phar://polargraph.phar/cli/polargraph';
__HALT_COMPILER();
EOF;
        return $stub;

    }

    private function getRelativeFilePath(SplFileInfo $file)
    {
        $realPath = $file->getRealPath();
        $pathPrefix = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
        $position = strpos($realPath, $pathPrefix);
        $relativePath = ($position !== false) ? substr_replace($realPath, '', $position, strlen($pathPrefix)) : $realPath;
        return strtr($relativePath, '\\', '/');
    }

    private function archiveCli(Phar $phar)
    {
        $content = file_get_contents(__DIR__.'/../../cli/polargraph');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('cli/polargraph', $content);
    }

    private function archive(Phar $phar, SplFileInfo $file, $strip = true)
    {
        $path = $this->getRelativeFilePath($file);
        $content = file_get_contents($file);
        if($strip) {
            $content = $this->stripWhitespace($content);
        }
        $phar->addFromString($path, $content);
    }

    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }
        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }
        return $output;
    }



}