<?php

declare(strict_types=1);

namespace MageEye\MagentoDisguisedComponentRegistrar\Component;

use Magento\Framework\Component\ComponentRegistrar as ComponentRegistrarMagento;

class ComponentRegistrar
{
    /**#@+
     * Different types of components
     */
    public const string MODULE = 'module';
    public const string LIBRARY = 'library';
    public const string THEME = 'theme';
    public const string LANGUAGE = 'language';
    public const string SETUP = 'setup';
    /**#@- */

    /**#@- */
    private static $paths = [
        self::MODULE => [],
        self::LIBRARY => [],
        self::LANGUAGE => [],
        self::THEME => [],
        self::SETUP => []
    ];

    /**
     * Sets the location of a component.
     *
     * @param string $type component type
     * @param string $componentName Fully-qualified component name
     * @param string $path Absolute file path to the component
     * @throws \LogicException
     * @return void
     */
    public static function register($type, $componentName, $path)
    {
        ComponentRegistrarMagento::register($type, $componentName, $path);
        self::validateType($type);
        if (isset(self::$paths[$type][$componentName])) {
            throw new \LogicException(
                ucfirst($type) . ' \'' . $componentName . '\' from \'' . $path . '\' '
                . 'has been already defined in \'' . self::$paths[$type][$componentName] . '\'.'
            );
        }
        self::$paths[$type][$componentName] = str_replace('\\', '/', $path);
    }

    public static function removeDisguise($type, $componentName)
    {
        unset(static::$paths[$type][$componentName]);
    }

    /**
     * @inheritdoc
     */
    public static function getPaths($type)
    {
        self::validateType($type);
        return self::$paths[$type];
    }

    /**
     * @inheritdoc
     */
    public static function getPath($type, $componentName)
    {
        self::validateType($type);
        return self::$paths[$type][$componentName] ?? null;
    }

    /**
     * Checks if type of component is valid
     *
     * @param string $type
     * @return void
     * @throws \LogicException
     */
    private static function validateType($type)
    {
        if (!isset(self::$paths[$type])) {
            throw new \LogicException('\'' . $type . '\' is not a valid component type');
        }
    }
}
