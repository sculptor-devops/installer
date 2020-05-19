<?php namespace Eppak\Stages;

class Resolver
{
    public static function prepare(array $stages, string $version): array
    {
        $resolved = [];
        $version = 'V' . str_replace('.', '', $version) ;

        foreach ($stages as $stage) {
            $name = "Eppak\Stages\\{$version}\\{$stage}";
            $resolved[] = $name;
        }

        return $resolved;
    }

    public static function make(string $class)
    {
        return resolve($class);
    }
}
