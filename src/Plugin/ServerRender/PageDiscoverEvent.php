<?php

namespace l24n\Twigen\Plugin\ServerRender;

use l24n\Twigen\Plugin\ServerRender\PageRoutes;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Contracts\EventDispatcher\Event;

class PageDiscoverEvent extends Event
{
    public function __construct(
        private SplFileInfo $file,
        private PageRoutes $pageRoutes,
    ) 
    {
        //
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    public function getPageRoutes(): PageRoutes
    {
        return $this->pageRoutes;
    }
}
