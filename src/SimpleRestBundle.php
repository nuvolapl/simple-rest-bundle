<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Nuvola\SimpleRestBundle\DependencyInjection\SimpleRestExtension;

final class SimpleRestBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SimpleRestExtension();
    }
}
