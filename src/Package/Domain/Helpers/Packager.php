<?php

namespace PhpLab\Dev\Package\Domain\Helpers;

use Phar;

class Packager
{

    /**
     * @var string
     */
    protected $vendor_dir;

    protected $default_excludes = [
        '/.',
        //'/tests',
        '/docs',
        '/examples',
        '/composer.json',
        '/LICENSE',
        '/phpunit.xml',
        '/php7lab/dev',
        '/php7lab/test',
        '/zndoc/rest-api',
        //'/symfony/web-server-bundle',
        '/phpunit/',
        'regex:#[\s\S]+\.(md|bat|dist)#iu',
    ];

    protected $excludes = [];
    /**
     * Packager constructor.
     */
    public function __construct($vendor, $excludes = [])
    {
        $this->vendor_dir = str_replace(DIRECTORY_SEPARATOR, '/', $vendor);

        $this->excludes = array_merge($this->default_excludes, $excludes);
    }


    public function export($outPath) {
        $outPath = rtrim(rtrim($outPath, DIRECTORY_SEPARATOR), '/');
        $outPath = str_replace(DIRECTORY_SEPARATOR, '/', $outPath);

        $phar = new Phar($this->vendor_dir.'/vendor.phar',
            \FilesystemIterator::CURRENT_AS_FILEINFO,
            'vendor.phar');


        /*$vendor_base_path = dirname($this->vendor_dir);
        $vendor_base_path = $this->getRelativePath($outPath, $vendor_base_path);
        $vendor_base_path = '/' . trim($vendor_base_path, '/');*/

        //dd($this->vendor_dir);

        $phar->startBuffering();
        $vendor_list = new \ArrayIterator($this->getVendorFiles($this->vendor_dir, $this->excludes));
        $phar->buildFromIterator($vendor_list, $this->vendor_dir);

        //$phar->buildFromDirectory($this->vendor_dir);

        $this->fixBaseDir($phar);
        $phar->setStub("
<?php
\Phar::interceptFileFuncs();
\Phar::mount(\Phar::running(true) . '/.mount/', __DIR__.'/');
return require_once 'phar://' . __FILE__ . DIRECTORY_SEPARATOR . 'autoload.php';
__HALT_COMPILER();
        ");

        $phar->stopBuffering();
    }

    public function updateBaseDir(Phar $phar, string $file) {
        $content = \file_get_contents($phar[$file]->getPathname());
        if (false !== $content) {
            $phar[$file] = \preg_replace(
                '/\$baseDir\s*=\s*dirname\(\$vendorDir\);/m',
                '$baseDir=\\\\Phar::running(true).\'/.mount\';',
                $content
            );
        }
    }

    public function fixBaseDir(\Phar $phar) {
        if (isset($phar['composer/autoload_classmap.php'])) {
            $this->updateBaseDir($phar, 'composer/autoload_classmap.php');
        }
        if (isset($phar['composer/autoload_files.php'])) {
            $this->updateBaseDir($phar, 'composer/autoload_files.php');
        }
        if (isset($phar['composer/autoload_namespaces.php'])) {
            $this->updateBaseDir($phar, 'composer/autoload_namespaces.php');
        }
        if (isset($phar['composer/autoload_psr4.php'])) {
            $this->updateBaseDir($phar, 'composer/autoload_psr4.php');
        }
        if (isset($phar['composer/autoload_static.php'])) {
            $content = file_get_contents($phar['composer/autoload_static.php']->getPathname());
            if (false !== $content) {
                /**
                 * @var string $autoloadStaticContent
                 */
                $autoloadStaticContent = preg_replace(
                    '/__DIR__\s*\.\s*\'\/..\/..\'\s*\.\s*/m',
                    'PHAR_RUNNING . ',
                    $content,
                    -1,
                    $replaced
                );
                if ($replaced > 0) {
                    $autoloadStaticContent = str_replace(
                        'namespace Composer\Autoload;',
                        'namespace Composer\Autoload;' . PHP_EOL . PHP_EOL . "define('PHAR_RUNNING',\\Phar::running(true).'/.mount');",
                        $autoloadStaticContent
                    );
                }
                $phar['composer/autoload_static.php'] = $autoloadStaticContent;
            }

        }
    }

    public function getVendorFiles($vendor_dir, $exlcudes = []) {
        $directory = new \RecursiveDirectoryIterator($vendor_dir);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = [];
        foreach ($iterator as $info) {
            /**
             * @var $info \SplFileInfo
             */
            if ($info->isDir()) {
                continue;
            }
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $info->getRealPath());
            $path = str_replace($vendor_dir, '', $path);


            if ($this->matchExclude($path, $exlcudes)) {
                continue;
            }

            $files[] = $info;
        }

        return $files;
    }

    public function matchExclude($path, $exlcudes = []) {
        foreach ($exlcudes as $rule) {
            if (stripos($rule, 'regex') !== false) {
                $rule = str_replace('regex:', '', $rule);
                if (preg_match($rule, $path)) {
                    return true;
                }
            }

            if (stripos($path, $rule) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }

}
