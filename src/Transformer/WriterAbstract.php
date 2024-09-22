<?php
/*
 * Copyright Jerke Combee. All rights reserved.
 * https://www.jcombee.nl/
 */

declare(strict_types=1);

namespace MageEye\MagentoDisguisedComponentRegistrar\Transformer;

abstract class WriterAbstract implements TransformerInterface
{
    public static function getPath(): string
    {
        return 'App/DeploymentConfig/Writer.php';
    }

    public static function transform(string $content): string
    {
        $insert = <<<WRITER_INSERT
\$modules = \MageEye\MagentoDisguisedComponentRegistrar\Component\ComponentRegistrar::getPaths(
                    \MageEye\MagentoDisguisedComponentRegistrar\Component\ComponentRegistrar::MODULE
                );
                foreach (\$modules as \$module => \$modulePath) {
                    unset(\$config['modules'][\$module]);
                }

                \$contents = \$this->formatter->format(\$config, \$comments);
WRITER_INSERT;

        return str_replace("\$contents = \$this->formatter->format(\$config, \$comments);", $insert, $content);
    }
}
