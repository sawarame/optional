<?php

declare(strict_types=1);

namespace SawarameTest;

use PHPUnit\Framework\TestCase;
use Sawarame\Optional;

use function PHPUnit\Framework\assertInfinite;
use function PHPUnit\Framework\assertInstanceOf;

class OptionalTest extends TestCase
{
   public function testOf() {
       $optional = Optional::of("test");
       assertInstanceOf(Optional::class, $optional);

   }
}
