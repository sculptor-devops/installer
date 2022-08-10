<?php

namespace Sculptor\Stages;

use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class StageResolver
{
    /**
     * @var string
     */
    private string $version = '18.04';

    public function versions(): array
    {
        return collect(APP_COMPATIBLE_VERSION)
            ->filter(function ($version) {
                return version_compare($version, $this->version) <= 0;
            })
            ->reverse()
            ->flatten()
            ->toArray();
    }

    /**
     * @param string $stage
     * @return string
     * @throws Exception
     */
    public function resolve(string $stage): string
    {
        foreach ($this->versions() as $version) {
            $version = 'V' . str_replace('.', '', $version);

            $name = "Sculptor\Stages\\{$version}\\{$stage}";

            if ($this->exists($name)) {
                return $name;
            }
        }

        throw new Exception("Stage $stage not found");
    }

    private function exists(object|string $name): bool
    {
        try {
            return (new ReflectionClass($name)) != null;
        } catch (ReflectionException $ex) {
            return false;
        }
    }

    public function version(string $version): StageResolver
    {
        $this->version = $version;

        return $this;
    }
}
