<?php

declare(strict_types=1);

namespace SawarameTest;

use PHPUnit\Framework\TestCase;
use Sawarame\Optional\Optional;
use Sawarame\Optional\Optional\Exception\NullPointerException;
use Sawarame\Optional\Optional\Exception\NoSuchElementException;

class OptionalTest extends TestCase
{
    /**
     * test empty().
     */
    public function testEmpty()
    {
        $optional = Optional::empty();
        $this->assertInstanceOf(Optional::class, $optional);
        $this->assertEquals(Optional::ofNullable(null), $optional);
    }

    /**
     * test of().
     */
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
        $result = ob_get_clean();
        $this->assertEquals("present", $result);

        ob_start();
        $optional = Optional::ofNullable(null);
        $optional->ifPresent(function($value) {
            echo "present";
        });
        $result = ob_get_clean();
        $this->assertEquals(null, $result);
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
        $result = ob_get_clean();
        $this->assertEquals("present", $result);

        ob_start();
        $optional = Optional::ofNullable(null);
        $optional->ifPresentOrElse(function($value) {
            echo "present";
        }, function(){
            echo "not present";
        });
        $result = ob_get_clean();
        $this->assertEquals("not present", $result);
    }

    public function testFilter()
    {
        $optional = Optional::ofNullable("test");
        $result = $optional->filter(function($value) {
            return "test" === $value;
        });
        $this->assertEquals($optional, $result);

        $optional = Optional::ofNullable("test");
        $result = $optional->filter(function($value) {
            return "testa" === $value;
        });
        $this->assertEquals(Optional::empty(), $result);
    }

    public function testFilter__TypeError() {
        $this->expectException(\TypeError::class);
        $optional = Optional::ofNullable("test");
        $optional->filter(function() {
            return "test filter";
        });
    }

    public function testMap()
    {
        $optional = Optional::ofNullable("test");
        $result = $optional->map(function($value) {
            return $value . " map";
        });
        $this->assertEquals(Optional::of("test map"), $result);

        $optional = Optional::ofNullable(null);
        $result = $optional->map(function($value) {
            return $value . " map";
        });
        $this->assertEquals(Optional::empty(), $result);
    }

    public function testFlatMap()
    {
        $optional = Optional::ofNullable("test");
        $result = $optional->flatMap(function($value) {
            return Optional::ofNullable($value . " flat map");
        });
        $this->assertEquals(Optional::of("test flat map"), $result);

        $optional = Optional::ofNullable(null);
        $result = $optional->flatMap(function($value) {
            return Optional::ofNullable($value . " flat map");
        });
        $this->assertEquals(Optional::empty(), $result);
    }

    public function testFlatMap__TypeError()
    {
        $this->expectException(\TypeError::class);
        $optional = Optional::ofNullable("test");
        $optional->flatMap(function($value) {
            return $value . " flat map";
        });
    }

    public function testOr()
    {
        $optional = Optional::ofNullable("test");
        $result = $optional->or(function() {
            return Optional::ofNullable("test or");
        });
        $this->assertEquals(Optional::of("test"), $result);

        $optional = Optional::ofNullable(null);
        $result = $optional->or(function() {
            return Optional::ofNullable("test or");
        });
        $this->assertEquals(Optional::of("test or"), $result);
    }

    public function testOr__TypeError()
    {
        $this->expectException(\TypeError::class);
        $optional = Optional::ofNullable(null);
        $optional->or(function() {
            return "test or";
        });
    }

    public function testOr_ArgumentCountError()
    {
        $this->expectException(\ArgumentCountError::class);
        $optional = Optional::ofNullable(null);
        $optional->or(function($value) {
            return "test or";
        });
    }

    public function testOrElse()
    {
        $optional = Optional::ofNullable("test");
        $result = $optional->orElse("test or else");
        $this->assertEquals("test", $result);

        $optional = Optional::ofNullable(null);
        $result = $optional->orElse("test or else");
        $this->assertEquals("test or else", $result);
    }

    public function testOrElseGet()
    {
        $optional = Optional::ofNullable("test");
        $result = $optional->orElseGet(function() {
            return "test or else get";
        });
        $this->assertEquals("test", $result);

        $optional = Optional::ofNullable(null);
        $result = $optional->orElseGet(function() {
            return "test or else get";
        });
        $this->assertEquals("test or else get", $result);
    }

    public function testOrElseGet_ArgumentCountError()
    {
        $this->expectException(\ArgumentCountError::class);
        $optional = Optional::ofNullable(null);
        $optional->orElseGet(function($value) {
            return "test or else get";
        });
    }


    public function testOrElseThrow()
    {
        $optional = Optional::ofNullable("test");
        $result = $optional->orElseThrow();
        $this->assertEquals("test", $result);
    }

    public function testOrElseThrow_NoSuchElementException()
    {
        $this->expectException(NoSuchElementException::class);
        $optional = Optional::ofNullable(null);
        $optional->orElseThrow();
    }

    public function testOrElseThrow_ErrorException()
    {
        $this->expectException(\ErrorException::class);
        $optional = Optional::ofNullable(null);
        $optional->orElseThrow(function () {
            return new \ErrorException();
        });
    }

    public function testEquals()
    {
        $this->assertTrue(Optional::empty()->equals(Optional::empty()));
        $this->assertTrue(Optional::ofNullable("test")->equals(Optional::ofNullable("test")));

        $this->assertFalse(Optional::empty()->equals("test"));
        $this->assertFalse(Optional::ofNullable("test")->equals(Optional::ofNullable("test equals")));
    }
}
