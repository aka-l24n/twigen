<?php

namespace l24n\Twigen;

use Composer\InstalledVersions;
use l24n\Twigen\Plugin\AssetMapper\AssetMapperPlugin;
use l24n\Twigen\Plugin\ServerRender\ServerRenderPlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends ContainerBuilder
{
    private bool $booted = false;

    protected array $plugins = [
        ServerRenderPlugin::class,
        AssetMapperPlugin::class,
    ];

    public function __construct(private string $basePath, ?ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);

        $this->registerApplicationServices();

        $this->setAlias(EventDispatcher::class, 'event_dispatcher');
        $this->setAlias(ContainerInterface::class, 'service_container');
        $this->setAlias(self::class, 'service_container');

        $this->setParameter('kernel.project_dir', $this->basePath);
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->registerPlugins();
        $services = $this->findTaggedServiceIds('application.boot');
        
        $this->compile();

        foreach (array_keys($services) as $id) {
            ($this->get($id))->boot();
        }

        $this->booted = true;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    public function getVersion(): string
    {
        return InstalledVersions::getVersion('l24n/twigen') ?: 'unknown';
    }

    protected function registerPlugins(): void
    {
        foreach ($this->plugins as $plugin) {
            $plugin::register($this);
        }
    }

    private function registerApplicationServices()
    {
        $this->register('event_dispatcher', EventDispatcher::class)->setAutowired(true);
        
        $this->register('kernel', Kernel::class)
            ->setPublic(true)
            ->setAutowired(true);
    }
}
