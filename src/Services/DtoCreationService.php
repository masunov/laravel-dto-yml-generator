<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 2019-01-02
 * Time: 23:46
 */

namespace Masunov\LaravelDtoYmlGenerator\Services;

class DtoCreationService
{
    private const DEFAULT_NAMESPACE = 'App';

    private const BASE_DTO_RESOURSE = __DIR__ . '/../Resources/BaseDto';

    /**
     * @param string      $className
     * @param string|null $namespace
     * @return bool
     */
    public function execute(string $className, string $namespace = null): bool
    {

        $resourseContent = file_get_contents(self::BASE_DTO_RESOURSE);

        $classPath = lcfirst($namespace ?? self::DEFAULT_NAMESPACE . '/' . $className) . '.php';

        if (file_exists($classPath)) {
            return false;
        }

        $content = str_replace('BaseClassnameDto', $className, $resourseContent);

        if (null !== $namespace) {
            $content = str_replace(self::DEFAULT_NAMESPACE, str_replace('/', '\\', $namespace), $content);
        }

        file_put_contents($classPath, $content);

        return true;

    }
}