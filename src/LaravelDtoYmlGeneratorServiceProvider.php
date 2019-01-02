<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 2019-01-02
 * Time: 20:27
 */

namespace Masunov\PhpLibFilestorage;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use GuzzleHttp\Client;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Illuminate\Support\ServiceProvider;

class LaravelDtoYmlGeneratorServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
    }

    public function register()
    {
        $this->app->singleton('CloudFileStorageClient', function () {
            return new StorageClient(new Client());
        });

        $this->app->singleton(IStorageSerializerInterface::class, function ($app) {
            $builder           = new SerializerBuilder();
            $debug             = config('app.debug');
            $environment       = $app->environment();
            $serializerBuilder = $builder::create();
            $apiVersion        = null;

            if ($environment == 'production') {
                $serializerBuilder->setCacheDir(config('cloudstorage.serializer_cache_dir'));
            }

            $serializerBuilder->addMetadataDir(config('cloudstorage.serializer_definition_dir'))
                              ->setAnnotationReader(new SimpleAnnotationReader())
                              ->setSerializationContextFactory(
                                  function () use ($apiVersion) {
                                      $context = SerializationContext::create();
                                      $context->setSerializeNull(false);
                                      if (!is_null($apiVersion)) {
                                          $context->setVersion($apiVersion);
                                      }

                                      return $context;
                                  })
                              ->setDebug($debug)
                              ->setAnnotationReader(new SimpleAnnotationReader());

            return $serializerBuilder->build();
        });

    }

    private function handleConfigs()
    {

        $configPath = __DIR__ . '/../config/cloudstorage.php';

        $this->publishes([$configPath => config_path('ticket_system.php')]);

        $this->mergeConfigFrom($configPath, 'cloudstorage');
    }
}
