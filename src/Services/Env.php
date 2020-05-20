<?php namespace Eppak\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Env
{
    private $filename;

    private $content = [];

    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $this->parse();
    }

    private function parse(): void
    {
        $content = File::get($this->filename);

        $this->content = preg_split("/\r\n|\n|\r/", $content);
    }

    public function get(string $key, bool $quoted = true): ?string
    {
        foreach ($this->content as $line) {
            if (Str::startsWith($line, $key)) {
                $value = Str::after($line, '=');

                return $quoted ? quoted($value): $value;
            }
        }

        return null;
    }
}
