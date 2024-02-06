<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class QueryStringArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly ValidatorInterface $validator,
        private readonly bool $isEnabled,
        private readonly string $queryStringKeyName,
        private readonly bool $isValidationEnabled,
        private readonly string $validationAttributeName,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return
            $this->isEnabled
            and $this->queryStringKeyName === $argument->getName()
            and $request->query->has($this->queryStringKeyName);
    }

    /**
     * @return iterable<mixed>
     *
     * @throws ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (false === $this->supports($request, $argument)) {
            return;
        }

        $parameters = $request->query->all($this->queryStringKeyName);

        if (empty($parameters)) {
            return;
        }

        $result = $this->denormalizer->denormalize($parameters, (string) $argument->getType());

        if ($this->isValidationEnabled and ($violations = $this->validator->validate($result))->count()) {
            $request->attributes->set($this->validationAttributeName, $violations);
        }

        yield $result;
    }
}
