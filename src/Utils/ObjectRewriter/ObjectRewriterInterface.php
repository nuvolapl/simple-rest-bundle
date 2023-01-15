<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\Utils\ObjectRewriter;

interface ObjectRewriterInterface
{
    /**
     * @throw InvalidArgumentException
     *
     * @param array<mixed> $normalizerContext
     * @param array<mixed> $denormalizerContext
     */
    public function rewrite(object $object, string $to, array $normalizerContext = [], array $denormalizerContext = []): object;
}
