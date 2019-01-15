<?php
/**
 * Created by PhpStorm.
 * User: vladislav
 * Date: 2019-01-02
 * Time: 23:48
 */

namespace Masunov\LaravelDtoYmlGenerator\Services;

use Symfony\Component\Yaml\Yaml;

class YmlBuilder
{
    public function execute(string $ymlPath, array $params)
    {
        if (file_exists($ymlPath)) {
            $parsedYml = Yaml::parseFile($ymlPath);
            $ymlOld    = $parsedYml[$params['class_name']] ?? null;
        }

        $props = [];

        foreach ($params['props'] as $param) {
            if (isset($ymlOld['properties'][$param['name']]) && count($ymlOld['properties'][$param['name']]) > 1) {
                if (isset($ymlOld['properties'][$param['name']])) {
                    $ymlOld['properties'][$param['name']]['type'] = $param['type'];
                    $props[$param['name']]                        = $ymlOld['properties'][$param['name']];
                } else {
                    $props[$param['name']] = [
                        'type' => $param['type']
                    ];
                }
            } else {
                $props[$param['name']] = [
                    'type' => $param['type']
                ];
            }
        }

        $ymlDefinition = [
            $params['class_name'] => [
                'exclusion_policy' => $ymlOld['exclusion_policy'] ?? 'all',
                'properties'       => $props
            ]
        ];

        $ymlDump = Yaml::dump($ymlDefinition, 4);
        file_put_contents($ymlPath, $ymlDump);
    }
}