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
}
