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
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use Innomatic\Core\MVC\Legacy\Kernel;

class LegacyApplicationInstaller extends LegacyInstaller
{
    public function supports($packageType)
    {
        return $packageType === 'innomatic-legacy-application';
    }

    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return parent::isInstalled($repo, $package) && is_dir($this->innomaticLegacyDir . '/innomatic/core/applications/');
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $downloadPath = $this->getInstallPath($package);

        $fileSystem = new Filesystem();
        $actualLegacyDir = $this->innomaticLegacyDir;
        $this->innomaticLegacyDir = $packageDir = $this->generateTempDirName();

        if ($this->io->isVerbose()) {
            $this->io->write("Installing in temporary directory.");
        }

        parent::install($repo, $package);

        if ($this->io->isVerbose()) {
            $this->io->write("Installing inside Innomatic legacy.");
        }

        // Add vendor autoloads to access Innomatic Legacy Kernel bridge
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        require $vendorDir.'/autoload.php';

        $legacyKernel = new Kernel();
        $legacyKernel->runCallback(
            function () use ($packageDir) {
                $app = new \Innomatic\Application\Application(InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess());
                $result = $app->install($packageDir);
            }
        );

        $fileSystem->remove($this->innomaticLegacyDir);
        $this->innomaticLegacyDir = $actualLegacyDir;
    }

    public function updateCode(PackageInterface $initial, PackageInterface $target)
    {
        $actualLegacyDir = $this->innomaticLegacyDir;
        $this->innomaticLegacyDir = $packageDir = $this->generateTempDirName();
        if ($this->io->isVerbose()) {
            $this->io->write( "Installing in temporary directory." );
        }
        $this->installCode($target);
        $fileSystem = new Filesystem();
        if ($this->io->isVerbose()) {
            $this->io->write( "Updating Innomatic application over existing installation." );
        }

        // Add vendor autoloads to access Innomatic Legacy Kernel bridge
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        require $vendorDir.'/autoload.php';

        $legacyKernel = new Kernel();
        $legacyKernel->runCallback(
            function () use ($packageDir) {
                $app = new \Innomatic\Application\Application(InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess());
                $result = $app->install($packageDir);
            }
        );

        if ($this->io->isVerbose()) {
            $this->io->write( "Innomatic application upgrade finished." );
        }

        $fileSystem->remove($this->innomaticLegacyDir);
    }
}
