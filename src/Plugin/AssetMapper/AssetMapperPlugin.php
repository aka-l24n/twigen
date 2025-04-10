<?php

namespace l24n\Twigen\Plugin\AssetMapper;

use l24n\Twigen\Application;
use l24n\Twigen\Plugin\ServerRender\PageRoutes;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Route;
use Twig\Environment;

class AssetMapperPlugin
{
    public function __construct(
        protected EventDispatcher $eventDispatcher,
        #[Autowire('@page_routes')] protected PageRoutes $pageRoutes,
        #[Autowire('@twig')] protected Environment $twig,
    ) {
        // 
    }

    public static function register(Application $application)
    {
        $application->register('plugin.resource_loader', self::class)
            ->addArgument(new Reference('event_dispatcher'))
            ->setAutowired(true)
            ->addTag('application.boot')
            ->setPublic(true);
        
        $application->register('resource_controller', Controller::class)
            ->setAutowired(true)
            ->setPublic(true)
            ->addTag('controller.service_arguments');

        $application->register('plugin.generate_command', GenerateCommand::class)
            ->addTag('console.command');
    }

    public function boot()
    {
        $this->twig->addFunction(new \Twig\TwigFunction('asset', function (string $path) {
            return '/_asset/' . $path;
        }));
        
        $route = new Route('/_asset/{path}', [
            '_controller' => ['resource_controller', '__invoke'],
        ]);
        $route->addRequirements(['path' => '.+']);

        $this->pageRoutes->add('resource', $route);
    }
}
