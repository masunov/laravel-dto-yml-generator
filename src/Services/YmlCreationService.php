<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 2019-01-02
 * Time: 23:47
 */

namespace Masunov\LaravelDtoYmlGenerator\Services;

class YmlCreationService
{
    private const DEFAULT_NAMESPACE = 'App';

    private const BASE_YML_RESOURSE = __DIR__ . '/../Resources/BaseYml';

    /**
     * @param string      $className
     * @param string|null $namespace
     * @return bool
     */
    public function execute(string $className, string $namespace = null): bool
    {

        $resourseContent = file_get_contents(self::BASE_YML_RESOURSE);

        $ymlName = $namespace ?? self::DEFAULT_NAMESPACE . '.' . $className . '.yml';

        $classPath = resource_path('serializer/' . $ymlName);

        if (file_exists($classPath)) {
            return false;
        }

        $content = str_replace(
            'App\BaseClassnameDto',
            $namespace ?? self::DEFAULT_NAMESPACE . '\\' . $className,
            $resourseContent
        );

        file_put_contents($classPath, $content);

        return true;
    }
}