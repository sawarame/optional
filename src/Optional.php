<?php

namespace Sawarame;

class Optional {

    private function __construct($value)
    {
    }

    public static function of($value): self
    {
        return new self($value);
    }

}
