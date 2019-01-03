<?php

namespace Masunov\LaravelDtoYmlGenerator\Commands;

use Gears\ClassFinder;
use Illuminate\Console\Command;
use Masunov\LaravelDtoYmlGenerator\Services\DtoParser;
use Masunov\LaravelDtoYmlGenerator\Services\YmlBuilder;

class BuildYml extends Command
{


    private $finder;
    private $dtoParser;
    private $ymlBuilder;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:yml {--class=} {--namespace=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        DtoParser $dtoParser,
        YmlBuilder $ymlBuilder
    ) {
        parent::__construct();
        $composer         = require(__DIR__ . '/../../../../autoload.php');
        $this->finder     = new ClassFinder($composer);
        $this->dtoParser  = $dtoParser;
        $this->ymlBuilder = $ymlBuilder;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $ymlPath = config('laravel-dto-yml-generator.serializer_definition_path');

        if (null === $this->option('class') && null === $this->option('namespace')) {
            $this->error('Use with option --class or --namespace');
            exit;
        }

        if (null !== $this->option('class')) {
            $preparedClass = str_replace('/', '\\', $this->option('class'));
            $preparedYml   = $ymlPath . str_replace('/', '.', $this->option('class') . '.yml');
            $params        = $this->dtoParser->execute($preparedClass);
            $this->ymlBuilder->execute($preparedYml, $params);
        }

        if (null !== $this->option('namespace')) {

            $preparedNamespace = str_replace('/', '\\', $this->option('namespace'));

            try {
                $classes = array_values($this->finder->namespace($preparedNamespace)->search());
                foreach ($classes as $class) {
                    $preparedYml   = $ymlPath . str_replace('\\', '.', $class . '.yml');
                    $params        = $this->dtoParser->execute($class);
                    $this->ymlBuilder->execute($preparedYml, $params);
                }
            } catch (\Throwable $exception) {
                $this->error($exception->getMessage());
                $this->error('Classes not found in namespace ' . $this->option('namespace'));
            }

        }
    }
}
