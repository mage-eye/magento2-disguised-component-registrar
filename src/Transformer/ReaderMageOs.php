<?php
/*
 * Copyright Jerke Combee. All rights reserved.
 * https://www.jcombee.nl/
 */

declare(strict_types=1);

namespace MageEye\MagentoDisguisedComponentRegistrar\Transformer;

class ReaderMageOs extends ReaderAbstract
{
    public static function getPackage(): string
    {
        return 'mage-os/framework';
    }
}