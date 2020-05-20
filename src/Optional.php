<?php

namespace Sawarame;

use Sawarame\Optional\Exception\NullPointerException;

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

}
