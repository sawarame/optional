<?php

namespace Sawarame;

use Prophecy\Promise\ThrowPromise;
use Sawarame\Optional\Exception\NullPointerException;
use Sawarame\Optional\Exception\NoSuchElementException;
use Throwable;

class Optional
{
    private $value = null;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function empty(): self
    {
        return new self(null);
    }

    public static function of($value): self
    {
        if (is_null($value)) {
            throw new NullPointerException();
        }
        return new self($value);
    }

    public static function ofNullable($value): self
    {
        return new self($value);
    }

    public function get()
    {
        if (is_null($this->value)) {
            throw new NoSuchElementException();
        }
        return $this->value;
    }

    public function isPresent(): bool
    {
        return ! is_null($this->value);
    }

    public function isEmpty(): bool
    {
        return is_null($this->value);
    }

    public function ifPresent(callable $action): void
    {
        if (! is_null($this->value)) {
            $action($this->value);
        }
    }

    public function ifPresentOrElse(callable $action, callable $emptyAction): void
    {
        $this->ifPresent($action);
        if (is_null($this->value)) {
            $emptyAction();
        }
    }

    public function filter(callable $predicate): self
    {
        if ($predicate($this->value)) {
            return self::ofNullable($this->value);
        }
        return self::empty();
    }

    public function map(callable $mapper): self
    {
        if (! is_null($this->value)) {
            $value = $mapper($this->value);
            return new self($value);
        }
        return self::empty();
    }

    public function flatMap(callable $mapper)
    {
        if (! is_null($this->value)) {
            return $this->map($mapper)->get();
        }
        return self::empty();
    }

    public function or(callable $supplier): self
    {
        if (! is_null($this->value)) {
            return self::of($this->value);
        }
        return $supplier();
    }

    public function orElse($other)
    {
        if (! is_null($this->value)) {
            return $this->get();
        }
        return $other;
    }

    public function orElseGet(callable $supplier)
    {
        if (! is_null($this->value)) {
            return $this->get();
        }
        return $supplier();
    }

    public function orElseThrow(?callable $exceptionSupplier = null)
    {
        if (is_null($exceptionSupplier)) {
            throw new NoSuchElementException();
        }
        $exception = $exceptionSupplier();
        if (is_a($exception, 'Exception', true) || is_a($exception, 'Throwable', true)) {
            throw $exception;
        }
        throw new NoSuchElementException();
    }

    public function equals($obj): bool
    {
        if (! $obj instanceof Optional) {
            return false;
        }
        return $this->orElse(null) === $obj->orElse(null);
    }
}
