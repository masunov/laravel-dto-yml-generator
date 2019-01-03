<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 2019-01-02
 * Time: 20:27
 */

namespace Masunov\LaravelDtoYmlGenerator;

use Illuminate\Support\ServiceProvider;
use Masunov\LaravelDtoYmlGenerator\Commands\BuildYml;
use Masunov\LaravelDtoYmlGenerator\Commands\MakeDto;

class LaravelDtoYmlGeneratorServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    
    public function boot()
    {
        $this->handleConfigs();
    }

    public function register()
    {
        $this->commands(
            [
                MakeDto::class,
                BuildYml::class
            ]
        );
    }

    private function handleConfigs()
    {
        $configPath = __DIR__ . '/../config/laravel-dto-yml-generator.php';

        $this->publishes([$configPath => config_path('laravel-dto-yml-generator.php')]);

        $this->mergeConfigFrom($configPath, 'laravel-dto-yml-generator');
    }
}
