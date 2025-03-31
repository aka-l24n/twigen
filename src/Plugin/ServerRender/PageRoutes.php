<?php

namespace l24n\Twigen\Plugin\ServerRender;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\RouteCollection;

class PageRoutes extends RouteCollection
{
    public function __construct(
        private string $basePath, 
        private EventDispatcher $eventDispatcher
    ) {
        //
    }

    public function discover(): void
    {
        $finder = new Finder;
        $finder->files()->in($this->basePath . '/pages');

        foreach ($finder as $file) {
            $this
                ->eventDispatcher
                ->dispatch(new PageDiscoverEvent($file, $this), 'page.discover');
        }
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }
}
