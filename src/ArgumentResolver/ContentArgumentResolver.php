<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContentArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly bool $isEnabled,
        private readonly string $attributeName,
        private readonly bool $isValidationEnabled,
        private readonly string $validationAttributeName,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return
            $this->isEnabled
            and $this->attributeName === $argument->getName()
            and false                === empty($request->getContent());
    }

    /**
     * @return iterable<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (false === $this->supports($request, $argument)) {
            return;
        }

        $payload = $this->serializer->deserialize((string) $request->getContent(), (string) $argument->getType(), (string) $request->getPreferredFormat('json'));

        if ($this->isValidationEnabled and ($violations = $this->validator->validate($payload))->count()) {
            $request->attributes->set($this->validationAttributeName, $violations);
        }

        yield $payload;
    }
}
