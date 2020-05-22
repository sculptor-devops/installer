<?php namespace Eppak;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Replacer
{
    /**
     * @var string
     */
    private $string;

    /**
     * Replacer constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function replace(string $key, string $value): self
    {
        $this->string = str_replace("{$key}", $value, $this->string);

        return $this;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->string;
    }
}
