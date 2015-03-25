<?php
/**
 * Innomatic
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  2015 Innomatic Company
 * @license    http://www.innomatic.io/license/ New BSD License
 * @link       http://www.innomatic.io
 */
namespace Innomatic\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;

class LegacyApplicationInstaller extends LegacyInstaller
{
    public function supports($packageType)
    {
        return $packageType === 'innomatic-legacy-application';
    }

    public function getInstallPath(PackageInterface $package)
    {
    }
}
