<?php

namespace Sawarame;

use Sawarame\Optional\Exception\NullPointerException;
use Sawarame\Optional\Exception\NoSuchElementException;

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

    public function map(callable $mapper): self
    {
        if (! is_null($this->value)) {
            $value = $mapper($this->value);
            return new self($value);
        }
        return self::empty();
    }
}
