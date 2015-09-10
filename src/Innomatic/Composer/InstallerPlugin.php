<?php
/**
 * Innomatic
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  1999-2014 Innoteam Srl
 * @license    http://www.innomatic.io/license/ New BSD License
 * @link       http://www.innomatic.io
 */
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
