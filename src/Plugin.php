<?php

/*
 * Copyright Jerke Combee. All rights reserved.
 * https://www.jcombee.nl/
 */

declare(strict_types=1);

namespace MageEye\MagentoDisguisedComponentRegistrar;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\InstalledVersions;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use MageEye\MagentoDisguisedComponentRegistrar\Transformer\TransformerInterface;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    const PACKAGES_PATH = __DIR__ . '/../packages';
    const VERSIONS_PATH = __DIR__ . '/../versions.json';

    private Composer $composer;
    private IOInterface $io;
    private ?array $versions = null;

    public function activate(Composer $composer, IOInterface $io): void
    {
        // ...
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // ...
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // ...
    }

    public static function getSubscribedEvents()
    {
        return [
            'post-autoload-dump' => 'cleanupPackages',
        ];
    }

    public function cleanupPackages($event)
    {
        $composer = $event->getComposer();
        $installedRepo = $composer->getRepositoryManager()->getLocalRepository();

        $installedPackages = $installedRepo->getPackages();

        foreach ($installedPackages as $package) {
            foreach (TransformerInterface::TRANSFORMERS as $transformer) {
                /** @var TransformerInterface $transformer */
                if ($package->getName() === $transformer::getPackage()) {
                    if ($this->getVersion($transformer) === $package->getVersion()) {
                        continue;
                    }
                    $this->saveVersion($transformer, $package->getVersion());
                    $this->cleanupDir(static::PACKAGES_PATH . DIRECTORY_SEPARATOR . $transformer::getPackage() . DIRECTORY_SEPARATOR . $transformer::getPath());
                    $this->prepareTransformer($transformer);
                    $this->handleTransformer($transformer);
                }
            }
        }
    }

    private function cleanupDir($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        unlink($dir);
    }

    /**
     * @throws \Exception
     */
    public function transferAndParse()
    {
        foreach (TransformerInterface::TRANSFORMERS as $transformer) {
            $this->handleTransformer($transformer);
        }
    }

    private function prepareTransformer(string $transformer)
    {
        /** @var TransformerInterface $transformer */

        $package     = $transformer::getPackage();
        $packagePath = InstalledVersions::getInstallPath($package);

        $newFilePath = static::PACKAGES_PATH . '/' . $package . '/' . $transformer::getPath();

        if (!file_exists(dirname($newFilePath))) {
            mkdir(dirname($newFilePath), 0755, true);
        }

        $originalFilePath = $packagePath . '/' . $transformer::getPath();

        $contents = file_get_contents($originalFilePath);
        file_put_contents($newFilePath, $contents);
    }

    private function handleTransformer(string $transformer)
    {
        /** @var TransformerInterface $transformer */

        $package     = $transformer::getPackage();
        $packagePath = InstalledVersions::getInstallPath($package);
        if (!$packagePath) {
            return;
        }

        $newFilePath = __DIR__ . '/../packages/' . $package . '/' . $transformer::getPath();
        $originalFilePath = $packagePath . '/' . $transformer::getPath();

        if (!file_exists($newFilePath)) {
            $this->prepareTransformer($transformer);
        }

        $contents = file_get_contents($newFilePath);
        $contents = $transformer::transform($contents);
        file_put_contents($originalFilePath, $contents);
    }

    private function getVersion(string $transformer)
    {
        return $this->getVersions()[$transformer] ?? '';
    }

    private function getVersions(): array
    {
        if (null === $this->versions) {
            if (!file_exists(static::VERSIONS_PATH)) {
                return $this->versions = [];
            }
            $this->versions = json_decode(
                file_get_contents(static::VERSIONS_PATH) ?: '{}',
                true
            );
        }
        return $this->versions;
    }

    private function saveVersion(string|TransformerInterface $transformer, string $version)
    {
        $this->versions[$transformer] = $version;
        file_put_contents(
            static::VERSIONS_PATH,
            json_encode($this->versions)
        );
    }
}
