<?php namespace Sculptor\Services;

use Illuminate\Support\Facades\File;
use League\Flysystem\Adapter\Local as LocalFilesystem;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

class Templates
{
    /**
     * @var LocalFilesystem
     */
    private $adapter;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Templates constructor.
     */
    public function __construct()
    {
        $this->adapter = new LocalFilesystem(base_path('templates'));

        $this->filesystem = new Filesystem($this->adapter);
    }

    /**
     * @param string $name
     * @return string|null
     * @throws FileNotFoundException
     */
    public function read(string $name): ?string
    {
        $custom = getcwd() . "/" . APP_CONFIG_CUSTOM_TEMPLATE . "/{$name}";

        if (File::exists($custom)) {
            return File::get($custom);
        }

        return $this->filesystem->read($name);
    }

    /**
     * @return array
     * @throws FileNotFoundException
     */
    public function all(): array
    {
        $result = [];

        foreach ($this->filesystem->listContents('/') as $file) {

            if ($file['basename'] != APP_CONFIG_FILENAME && $file['type'] == 'file') {
                $result[] = [
                    'name' => $file['basename'],
                    'content' => $this->filesystem->read($file['basename'])
                ];
            }
        }

        return $result;
    }
}
