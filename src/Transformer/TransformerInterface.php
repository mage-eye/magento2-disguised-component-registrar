<?php
/*
 * Copyright Jerke Combee. All rights reserved.
 * https://www.jcombee.nl/
 */

declare(strict_types=1);

namespace MageEye\MagentoDisguisedComponentRegistrar\Transformer;

interface TransformerInterface
{
    public const TRANSFORMERS = [
        ReaderMagento::class,
        ReaderMageOs::class,
        WriterMagento::class,
        WriterMageOs::class,
    ];

    public static function getPackage(): string;

    public static function getPath(): string;

    public static function transform(string $content): string;
}
