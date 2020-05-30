<?php

declare(strict_types=1);

namespace SawarameTest;

use PHPUnit\Framework\TestCase;
use Sawarame\Optional;
use Sawarame\Optional\Exception\NullPointerException;
use Sawarame\Optional\Exception\NoSuchElementException;

class OptionalTest extends TestCase
{
    public function testOf()
    {
        $optional = Optional::of("test");
        $this->assertInstanceOf(Optional::class, $optional);
        $this->assertEquals("test", $optional->get());
    }

    public function testOf__NullPointerException()
    {
        $this->expectException(NullPointerException::class);
        Optional::of(null);
    }

    public function testOfNullable()
    {
        $optional = Optional::ofNullable("test");
        $this->assertInstanceOf(Optional::class, $optional);
        $this->assertEquals("test", $optional->get());

        $optional = Optional::ofNullable(null);
        $this->assertInstanceOf(Optional::class, $optional);
        $this->assertEquals(Optional::empty(), $optional);
    }

    public function testGet()
    {
        $optional = Optional::ofNullable("test");
        $this->assertEquals("test", $optional->get());
    }

    public function testGet__NoSuchElementException()
    {
        $this->expectException(NoSuchElementException::class);
        $optional = Optional::ofNullable(null);
        $optional->get();
    }

    public function testIsPresent()
    {
        $optional = Optional::ofNullable("test");
        $this->assertTrue($optional->isPresent());
        $optional = Optional::ofNullable(null);
        $this->assertFalse($optional->isPresent());
    }

    public function testIsEmpty()
    {
        $optional = Optional::ofNullable("test");
        $this->assertFalse($optional->isEmpty());
        $optional = Optional::ofNullable(null);
        $this->assertTrue($optional->isEmpty());
    }

    public function testIfPresent()
    {
        ob_start();
        $optional = Optional::ofNullable("test");
        $optional->ifPresent(function($value) {
            echo "present";
        });
        $actual = ob_get_clean();
        $this->assertEquals("present", $actual);

        ob_start();
        $optional = Optional::ofNullable(null);
        $optional->ifPresent(function($value) {
            echo "present";
        });
        $actual = ob_get_clean();
        $this->assertEquals(null, $actual);
    }

    public function testIfPresentOrElse()
    {
        ob_start();
        $optional = Optional::ofNullable("test");
        $optional->ifPresentOrElse(function($value) {
            echo "present";
        }, function(){
            echo "not present";
        });
        $actual = ob_get_clean();
        $this->assertEquals("present", $actual);

        ob_start();
        $optional = Optional::ofNullable(null);
        $optional->ifPresentOrElse(function($value) {
            echo "present";
        }, function(){
            echo "not present";
        });
        $actual = ob_get_clean();
        $this->assertEquals("not present", $actual);
    }
}
