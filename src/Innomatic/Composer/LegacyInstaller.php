<?php
/**
 * Innomatic
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  2015 Innoteam Srl
 * @license    http://www.innomatic.io/license/ New BSD License
 * @link       http://www.innomatic.io
 */
namespace Innomatic\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Innomatic\Core\MVC\Legacy\Kernel;

abstract class LegacyInstaller extends LibraryInstaller
{
    protected $innomaticLegacyDir;

    public function __construct(IOInterface $io, Composer $composer, $type = '')
    {
        parent::__construct($io, $composer, $type);
        $options = $composer->getPackage()->getExtra();
        $this->innomaticLegacyDir = isset($options['innomatic-legacy-dir']) ? rtrim($options['innomatic-legacy-dir'], '/') : 'innomatic_legacy';
    }

    public function getInstallPath(PackageInterface $package)
    {
        if ($this->io->isVerbose()) {
            $this->io->write("Innomatic legacy directory is '$this->innomaticLegacyDir/'");
        }

        return $this->innomaticLegacyDir;
    }

    protected function generateTempDirName()
    {
        $tmpDir = sys_get_temp_dir() . '/' . uniqid('composer_innomaticlegacy_');
        if ($this->io->isVerbose()) {
            $this->io->write("Temporary directory for Innomatic legacy platform updates: $tmpDir");
        }

        return $tmpDir;
    }

    protected function deployApplication($packageDir)
    {
        // Add vendor autoloads to access Innomatic Legacy Kernel bridge
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        require $vendorDir.'/autoload.php';

        $legacyKernel = new Kernel();
        $result = $legacyKernel->runCallback(
            function () use ($packageDir) {
                $app = new \Innomatic\Application\Application(InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess());

                $result['status'] = true;
                $result['unmetdeps'] = '';

                if (!$app->install($packageDir)) {
                    $unmetDeps = $app->getLastActionUnmetDeps();
                    $unmetDepsStr = '';

                    while (list(, $val) = each($unmetDeps)) {
                        $unmetDepsStr .= ' '.$val;
                    }

                    $result['status'] = false;
                    $result['unmetdeps'] = $unmetDepsStr;
                }

                return $result;
            }
        );

        if (!$result['status']) {
            throw new \RuntimeException("Dependencies error:".$result['unmetdeps']);
        }
    }
}
