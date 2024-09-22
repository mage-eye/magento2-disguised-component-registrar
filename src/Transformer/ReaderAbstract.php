<?php
/*
 * Copyright Jerke Combee. All rights reserved.
 * https://www.jcombee.nl/
 */

declare(strict_types=1);

namespace MageEye\MagentoDisguisedComponentRegistrar\Transformer;

abstract class ReaderAbstract implements TransformerInterface
{
    public static function getPath(): string
    {
        return 'App/DeploymentConfig/Reader.php';
    }

    public static function transform($content): string
    {
        $insert = <<<READER_INSERT
\$modules = \MageEye\MagentoDisguisedComponentRegistrar\Component\ComponentRegistrar::getPaths(
            \MageEye\MagentoDisguisedComponentRegistrar\Component\ComponentRegistrar::MODULE
        );
        foreach (array_keys(\$modules) as \$module) {
            \$result['modules'][\$module] = 1;
        }
        return \$result ?: [];
READER_INSERT;

        return str_replace("return \$result ?: [];", $insert, $content);
    }
}
