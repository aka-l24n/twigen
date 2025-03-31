<?php

namespace l24n\Twigen;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class Kernel
{
    public function __construct(
        private EventDispatcher $eventDispatcher,
        private UrlMatcherInterface $matcher,
        private ContainerControllerResolver $controllerResolver,
        private ArgumentResolverInterface $argumentResolver,
    ) {
        // 
    }

    public function handle($request): Response
    {
        $responseEvent = new ResponseEvent(new Response(), $request);
        $this->eventDispatcher->dispatch($responseEvent, 'kernel.response');

        return $responseEvent->getResponse();
    }
}
