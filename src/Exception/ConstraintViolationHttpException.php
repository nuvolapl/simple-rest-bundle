<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\Exception;

use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ConstraintViolationHttpException extends \Exception
{
    public const DEFAULT_SERIALIZATION_GROUP = 'basic';

    public readonly string $serializationGroup;

    /**
     * @var string
     */
    #[Serializer\Groups([self::DEFAULT_SERIALIZATION_GROUP])]
    protected $message;

    #[
        Serializer\Groups([self::DEFAULT_SERIALIZATION_GROUP]),
        Serializer\SerializedName('violation'),
    ]
    private readonly ConstraintViolationListInterface $violations;

    #[Serializer\Groups([self::DEFAULT_SERIALIZATION_GROUP])]
    private readonly int $statusCode;

    public function __construct(
        ConstraintViolationListInterface $violations,
        ?\Throwable $previous = null,
        string $message = 'An error occurred.',
        int $statusCode = 400,
        string $serializationGroup = self::DEFAULT_SERIALIZATION_GROUP,
    ) {
        $this->serializationGroup = $serializationGroup;
        $this->violations         = $violations;
        $this->statusCode         = $statusCode;

        parent::__construct($message, 0, $previous);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getSerializationGroup(): string
    {
        return $this->serializationGroup;
    }
}
