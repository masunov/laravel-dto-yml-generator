<?php

namespace Masunov\LaravelDtoYmlGenerator\Commands;

use Illuminate\Console\Command;
use Masunov\LaravelDtoYmlGenerator\Services\DtoCreationService;
use Masunov\LaravelDtoYmlGenerator\Services\YmlCreationService;

class MakeDto extends Command
{
    /**
     * @var DtoCreationService
     */
    private $dtoCreationService;

    /**
     * @var YmlCreationService
     */
    private $ymlCreationService;

    /**
     * @var string
     */
    protected $signature = 'make:dto {class} {--yml}';

    /**
     * @var string
     */
    protected $description = 'Command description';

    /**
     * MakeDto constructor.
     *
     * @param DtoCreationService $dtoCreationService
     * @param YmlCreationService $ymlCreationService
     */
    public function __construct(
        DtoCreationService $dtoCreationService,
        YmlCreationService $ymlCreationService
    ) {
        parent::__construct();
        $this->dtoCreationService = $dtoCreationService;
        $this->ymlCreationService = $ymlCreationService;
    }

    public function handle()
    {

        $withYml = $this->option('yml');

        $originalClassName = $this->argument('class');

        $parsedName = $this->parseClassName($this->argument('class'));

        if ($this->dtoCreationService->execute($parsedName['class'], $parsedName['namespace'])) {
            $this->info($originalClassName . ' successfuly created');

            if (true === $withYml) {
                if ($this->ymlCreationService->execute($parsedName['class'], $parsedName['namespace'])) {
                    $this->info('YML for class ' . $originalClassName . ' successfuly created');
                } else {
                    $this->warn('YML for class ' . $originalClassName . ' already exists');
                }
            }
        } else {
            $this->warn($originalClassName . ' already exists');
        }
    }

    private function parseClassName(string $className): array
    {
        $explodedName = explode('/', $className);

        $name = end($explodedName);

        array_pop($explodedName);

        $namespace = implode('/', $explodedName);

        return [
            'class'     => ucfirst($name),
            'namespace' => empty($namespace) ? null : $namespace
        ];
    }
}
