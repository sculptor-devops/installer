<?php

namespace Tests\Unit;

use Exception;
use Sculptor\Stages\StageResolver;
use Tests\TestCase;

class StageResolverTest extends TestCase
{
    public function versions(): array
    {
        return [
            ['18.04', ['18.04']],
            ['20.04', ['20.04', '18.04']],
            ['22.04', ['22.04', '20.04', '18.04']],
        ];
    }

    /**
     * @dataProvider versions
     */
    function test_resolve_version($version, $result): void
    {
        $resolver = new StageResolver();

        $resolver->version($version);

        $this->assertEquals($result, $resolver->versions());

        $this->assertEquals($resolver->versions()[0], $version);
    }

    /**
     * @throws Exception
     */
    function test_resolve_class(): void
    {
        $resolver = new StageResolver();

        $resolver->version('20.04');

        $this->assertEquals('Sculptor\Stages\V1804\Agent', $resolver->resolve('Agent'));

        $this->assertEquals('Sculptor\Stages\V2004\LetsSEncrypt', $resolver->resolve('LetsSEncrypt'));
    }
}
