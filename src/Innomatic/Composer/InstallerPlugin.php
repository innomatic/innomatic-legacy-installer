<?php

namespace Innomatic\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class InstallerPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        // Add the Innomatic legacy platform installer
        $composer
            ->getInstallationManager()
            ->addInstaller(new LegacyPlatformInstaller($io, $composer));
        
        // Add the Innomatic legacy application installer
        $composer
            ->getInstallationManager()
            ->addInstaller(new LegacyApplicationInstaller($io, $composer));
    }
}
