<?php namespace Sculptor\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Env
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var array<int, string>|false
     */
    private $content = [];

    /**
     * Env constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $this->parse();
    }

    /**
     *
     */
    private function parse(): void
    {
        $content = File::get($this->filename);

        if ($content) {
            $this->content = preg_split("/\r\n|\n|\r/", $content);

            return;
        }
    }

    /**
     * @param string $key
     * @param bool $quoted
     * @return string|null
     */
    public function get(string $key, bool $quoted = true): ?string
    {
        if (!$this->content) {

            return null;
        }

        foreach ($this->content as $line) {
            if (Str::startsWith($line, $key)) {
                $value = Str::after($line, '=');

                return $quoted ? quoted($value): $value;
            }
        }

        return null;
    }
}
