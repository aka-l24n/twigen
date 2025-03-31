<?php

namespace l24n\Twigen\Plugin\ServerRender;

use l24n\Twigen\Application;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
    
    public function __invoke(Request $request): Response
    {
        $context = $request->get('_front_matter', []);
        $file = $request->get('_file', null);

        if ($file === null) {
            throw new \RuntimeException('File not found in request attributes.');
        }

        $content = $this->twig->render($file['relative_path'] . '/' . $file['filename'], $context);

        return new Response($content, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}