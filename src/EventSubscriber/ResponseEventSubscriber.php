<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Nuvola\SimpleRestBundle\Exception\ConstraintViolationHttpException;

final class ResponseEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    public static function getSubscribedEvents(): iterable
    {
        yield KernelEvents::VIEW => 'createJsonResponseFromControllerResult';

        yield KernelEvents::EXCEPTION => 'createJsonResponseFromConstraintViolationException';
    }

    public function createJsonResponseFromControllerResult(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if (null === $result) {
            $response = new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } else {
            $response = new JsonResponse(
                $this->serializer->serialize($result, 'json'),
                JsonResponse::HTTP_OK,
                [],
                true
            );
        }

        $event->setResponse($response);
    }

    public function createJsonResponseFromConstraintViolationException(ExceptionEvent $event): void
    {
        /** @var ConstraintViolationHttpException|mixed $exception */
        $exception = $event->getThrowable();

        if (false === $exception instanceof ConstraintViolationHttpException) {
            return;
        }

        $contextBuilder = (new ObjectNormalizerContextBuilder())
            ->withGroups([$exception->getSerializationGroup()])
        ;

        $event->stopPropagation();
        $event->setResponse(
            new JsonResponse(
                $this->serializer->serialize($exception, 'json', $contextBuilder->toArray()),
                $exception->getStatusCode(),
                [],
                true
            )
        );
    }
}
