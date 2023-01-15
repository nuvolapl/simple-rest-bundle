<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\Utils\ObjectRewriter;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SymfonySerializerBasedRewriter implements ObjectRewriterInterface
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    public function rewrite(object $object, string $to, array $normalizerContext = [], array $denormalizerContext = []): object
    {
        try {
            return $this->denormalizer->denormalize(
                $this->normalizer->normalize($object, null, $normalizerContext),
                $to,
                null,
                $denormalizerContext
            );
        } catch (ExceptionInterface $e) {
            throw new \InvalidArgumentException(sprintf('Rewrite "%s" to "%s" not possible.', get_class($object), $to), 0, $e);
        }
    }
}
