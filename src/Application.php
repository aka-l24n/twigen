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

    /**
     * Constructor for the Application class.
     * Initializes the application with the base path and optional parameter bag.
     */
    public function __construct(private string $basePath, ?ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);

        $this->registerApplicationServices();

        $this->setAlias(EventDispatcher::class, 'event_dispatcher');
        $this->setAlias(ContainerInterface::class, 'service_container');
        $this->setAlias(self::class, 'service_container');

        $this->setParameter('kernel.project_dir', $this->basePath);
    }

    /**
     * Boot the application.
     * This method is called to initialize the application and register all plugins.
     * 
     * @todo: probably I should move this to a compiler pass, because it's a bit weird...
     */
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

    /**
     * Get the base path of the application.
     *
     * @return string The base path.
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Set the base path of the application.
     *
     * @param string $basePath The new base path.
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * Get the version of the application.
     *
     * @return string The application version or 'unknown' if not available.
     */
    public function getVersion(): string
    {
        return InstalledVersions::getVersion('l24n/twigen') ?: 'unknown';
    }

    /**
     * Register all plugins defined in the application.
     */
    protected function registerPlugins(): void
    {
        foreach ($this->plugins as $plugin) {
            $plugin::register($this);
        }
    }

    /**
     * Register core application services such as the event dispatcher and kernel.
     */
    private function registerApplicationServices()
    {
        $this->register('event_dispatcher', EventDispatcher::class)->setAutowired(true);
        
        $this->register('kernel', Kernel::class)
            ->setPublic(true)
            ->setAutowired(true);
    }
}
