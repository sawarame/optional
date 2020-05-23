<?php

declare(strict_types=1);

namespace SawarameTest;

use PHPUnit\Framework\TestCase;
use Sawarame\Optional;
use Sawarame\Optional\Exception\NullPointerException;

class OptionalTest extends TestCase
{
    public function testOf()
    {
        $optional = Optional::of("test");
        $this->assertInstanceOf(Optional::class, $optional);
    }

    public function testOf__NullPointerException()
    {
        $this->expectException(NullPointerException::class);
        Optional::of(null);
    }
}
