<?php


namespace Eppak\Services;


use Illuminate\Support\Facades\File;
use League\Flysystem\Adapter\Local as LocalFilesystem;
use League\Flysystem\Filesystem;

class Templates
{
    /**
     * @var LocalFilesystem
     */
    private $adapter;

    public function __construct()
    {
        $this->adapter = new LocalFilesystem(base_path('templates'));
    }

    public function read(string $name)
    {
        $custom = getcwd() . "/templates/{$name}";

        if (File::exists($custom)) {
            return File::get($custom);
        }

        return (new Filesystem($this->adapter))->read($name);
    }
}
