<?php
/*
 * Copyright Jerke Combee. All rights reserved.
 * https://www.jcombee.nl/
 */

declare(strict_types=1);

namespace MageEye\MagentoDisguisedComponentRegistrar\Transformer;

class WriterMagento extends WriterAbstract
{
    public static function getPackage(): string
    {
        return 'magento/framework';
    }
}
