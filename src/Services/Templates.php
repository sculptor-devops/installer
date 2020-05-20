<?php


namespace Eppak\Services;


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
        return (new Filesystem($this->adapter))->read($name);
    }
}
