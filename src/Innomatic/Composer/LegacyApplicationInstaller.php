<?php

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
