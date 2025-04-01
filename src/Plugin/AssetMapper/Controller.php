<?php

namespace l24n\Twigen\Plugin\AssetMapper;

use l24n\Twigen\Application;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Controller
{
    public function __construct(
        #[Autowire('@twig')] protected Environment $twig,
        protected Application $application,
    ) {
        // 
    }
    
    public function __invoke(Request $request, string $path): BinaryFileResponse|Response
    {
        $realFilePath = $this->application->getBasePath() . '/pages/' . $path;
        
        if (!file_exists($realFilePath)) {
            return new Response('File not found', 404);
        }

        return new BinaryFileResponse($realFilePath, 200, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
