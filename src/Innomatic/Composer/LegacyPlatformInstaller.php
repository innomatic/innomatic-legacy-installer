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

/**
 * Installer for Innomatic legacy platform.
 * Allows soft updates, ensuring that an existing installation is not wiped out.
 */
class LegacyPlatformInstaller extends LegacyInstaller
{
    public function supports($packageType)
    {
        return $packageType === 'innomatic-legacy-platform';
    }

    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        return parent::isInstalled($repo, $package) && is_dir($this->innomaticLegacyDir . '/innomatic/core');
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $downloadPath = $this->getInstallPath($package);
        $fileSystem = new Filesystem();
        $actualLegacyDir = $this->innomaticLegacyDir;
        $this->innomaticLegacyDir = $this->generateTempDirName();
    
        if (!is_dir($downloadPath) || $fileSystem->isDirEmpty($downloadPath)) {
            if ($this->io->isVerbose()) {
                $this->io->write("Installing in temporary directory.");
            }

            parent::install($repo, $package);

            if ($this->io->isVerbose()) {
                $this->io->write("Copying to the Innomatic legacy directory.");
            }

            $fileSystem->copyThenRemove($this->innomaticLegacyDir . '/source/', $actualLegacyDir);
            $this->innomaticLegacyDir = $actualLegacyDir;
        }
    }
    
    public function updateCode(PackageInterface $initial, PackageInterface $target)
    {
    }

    protected function generateTempDirName()
    {
        $tmpDir = sys_get_temp_dir() . '/' . uniqid('composer_innomaticlegacy_');
        if ($this->io->isVerbose()) {
            $this->io->write("Temporary directory for innomatic legacy platform updates: $tmpDir");
        }

        return $tmpDir;
    }
}
