<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 2019-01-02
 * Time: 23:47
 */

namespace Masunov\LaravelDtoYmlGenerator\Services;

use Masunov\LaravelDtoYmlGenerator\ExtendedReflection;

class DtoParser
{

    /**
     * @param string $class
     * @return array
     * @throws \ReflectionException
     */
    public function execute(string $class): array
    {
        $reflection = new ExtendedReflection($class);

        $props = [
            'class_name' => $reflection->getName(),
            'props'      => []
        ];

        foreach ($reflection->getProperties() as $property) {
            preg_match('/\s\@var\s(.*)\n/is', $property->getDocComment(), $matchType);

            $type = $matchType[1] ?? 'string';

            $type = str_replace('null', '', $type);
            $type = str_replace('|', '', $type);

            if (stripos($type, '[]') !== false) {
                $clearClassName  = str_replace('[]', '', $type);
                if ($reflection->hasUseStatement($clearClassName)) {
                    $type = 'array<'.$reflection->getUseStatements()[0]['class'].'>';
                } else {
                    $explodedNamespace = explode('\\', $reflection->getName());
                    array_pop($explodedNamespace);
                    $explodedNamespace[] = $clearClassName;
                    $implodedNamespace = implode('\\', $explodedNamespace);
                    $type = 'array<'.$implodedNamespace.'>';
                }
            }

            if ($type === '\DateTime') {
                $type = str_replace('\\', '', $type);
            }

            $props['props'][] = [
                'name' => $property->getName(),
                'type' => $type
            ];
        }

        return $props;
    }
}