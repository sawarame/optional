<?php

declare(strict_types=1);

namespace Sawarame\Optional;

use Sawarame\Optional\Optional\Exception\NullPointerException;
use Sawarame\Optional\Optional\Exception\NoSuchElementException;

/**
 * Optional class.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Optional
{
    private $value = null;

    /**
     * Constructor.
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns an empty Optional instance.
     *
     * @return empty an empty Optional
     */
    public static function empty(): self
    {
        return new self(null);
    }

    /**
     * Returns an Optional describing the specified value, if non-null, otherwise returns an empty Optional.
     *
     * @param mixed $value the value to be present, which must be non-null
     * @return self an Optional with the value present
     * @throws NullPointerException if value is null
     */
    public static function of($value): self
    {
        if (is_null($value)) {
            throw new NullPointerException();
        }
        return new self($value);
    }

    /**
     * Returns an Optional describing the specified value, if non-null, otherwise returns an empty Optional.
     *
     * @param mixed $value the possibly-null value to describe
     * @return self an Optional with a present value if the specified value is non-null, otherwise an empty Optional
     */
    public static function ofNullable($value): self
    {
        return new self($value);
    }

    /**
     * If a value is present in this Optional, returns the value, otherwise throws NoSuchElementException.
     *
     * @return mixed the non-null value held by this Optional
     * @throws NoSuchElementException if there is no value present
     */
    public function get()
    {
        if (is_null($this->value)) {
            throw new NoSuchElementException();
        }
        return $this->value;
    }

    /**
     * Return true if there is a value present, otherwise false.
     *
     * @return boolean true if there is a value present, otherwise false
     */
    public function isPresent(): bool
    {
        return ! is_null($this->value);
    }

    /**
     * If a value is present, invoke the specified consumer with the value, otherwise do nothing.
     *
     * @return boolean true if a value is not present, otherwise false
     */
    public function isEmpty(): bool
    {
        return is_null($this->value);
    }

    /**
     * If a value is present, performs the given action with the value, otherwise does nothing.
     *
     * @param callable $action the action to be performed, if a value is present
     */
    public function ifPresent(callable $action): void
    {
        if (! is_null($this->value)) {
            $action($this->value);
        }
    }

    /**
     * If a value is present, performs the given action with the value, otherwise performs the given empty-based action.
     *
     * @param callable $action the action to be performed, if a value is present
     * @param callable $emptyAction the empty-based action to be performed, if no value is present
     */
    public function ifPresentOrElse(callable $action, callable $emptyAction): void
    {
        if (is_null($this->value)) {
            $emptyAction();
        } else {
            $this->ifPresent($action);
        }
    }

    /**
     * If a value is present, and the value matches the given predicate, returns an Optional describing the value,
     * otherwise returns an empty Optional.
     *
     * @param callable $predicate the predicate to apply to a value, if present
     * @return self an Optional describing the value of this Optional, if a value is present and the value matches the
     * given predicate, otherwise an empty Optional
     */
    public function filter(callable $predicate): self
    {
        $filterClass = new class {
            public function filter(callable $predicate, $value): bool
            {
                return $predicate($value);
            }
        };
        if ($filterClass->filter($predicate, $this->value)) {
            return self::ofNullable($this->value);
        }
        return self::empty();
    }

    /**
     * If a value is present, returns an Optional describing (as if by ofNullable(T)) the result of applying the given
     * mapping function to the value, otherwise returns an empty Optional.
     *
     * @param callable $mapper the mapping function to apply to a value, if present
     * @return self an Optional describing the result of applying a mapping function to the value of this Optional,
     * if a value is present, otherwise an empty Optional
     */
    public function map(callable $mapper): self
    {
        if (! is_null($this->value)) {
            $value = $mapper($this->value);
            return new self($value);
        }
        return self::empty();
    }

    /**
     * If a value is present, returns the result of applying the given Optional-bearing mapping function to the value,
     * otherwise returns an empty Optional.
     * This method is similar to map(Function), but the mapping function is one whose result is already an Optional,
     * and if invoked, flatMap does not wrap it within an additional Optional.
     *
     * @param callable $mapper the mapping function to apply to a value, if present
     * @return self the result of applying an Optional-bearing mapping function to the value of this Optional, if a
     * value is present, otherwise an empty Optional
     */
    public function flatMap(callable $mapper): self
    {
        if (! is_null($this->value)) {
            $mapperClass = new class {
                public function flatMap(callable $mapper, $value): Optional
                {
                    return $mapper($value);
                }
            };
            return $mapperClass->flatMap($mapper, $this->value);
        }
        return self::empty();
    }

    /**
     * If a value is present, returns an Optional describing the value, otherwise returns an Optional produced by the
     * supplying function.
     *
     * @param callable $supplier the supplying function that produces an Optional to be returned
     * @return self returns an Optional describing the value of this Optional, if a value is present, otherwise an
     * Optional produced by the supplying function.
     */
    public function or(callable $supplier): self
    {
        if (! is_null($this->value)) {
            return self::of($this->value);
        }
        $supplierClass = new class {
            public function or(callable $supplier): Optional
            {
                return $supplier();
            }
        };
        return $supplierClass->or($supplier);
    }

    /**
     * If a value is present, returns an Optional describing the value, otherwise returns an Optional produced by the
     * supplying function.
     *
     * @param mixed $other the value to be returned, if no value is present. May be null
     * @return mixed the value, if present, otherwise other
     */
    public function orElse($other)
    {
        if (! is_null($this->value)) {
            return $this->get();
        }
        return $other;
    }

    /**
     * Return the value if present, otherwise invoke other and return the result of that invocation.
     *
     * @param $supplier a Supplier whose result is returned if no value is present
     * @return mixed the value if present otherwise the result of other.get()
     */
    public function orElseGet(callable $other)
    {
        if (! is_null($this->value)) {
            return $this->get();
        }
        return $other();
    }

    /**
     * Return the contained value, if present, otherwise throw an exception to be created by the provided supplier.
     *
     * @param callable $exceptionSupplier The supplier which will return the exception to be thrown
     * @return mixed the present value
     */
    public function orElseThrow(callable $exceptionSupplier)
    {
        if (! is_null($this->value)) {
            return $this->get();
        }
        $exception = $exceptionSupplier();
        if (is_a($exception, 'Exception', true) || is_a($exception, 'Throwable', true)) {
            throw $exception;
        }
        throw new NoSuchElementException();
    }

    /**
     * Indicates whether some other object is "equal to" this Optional. The other object is considered equal if:
     * - it is also an Optional and;
     * - both instances have no value present or;
     * - the present values equal to another present value.
     *
     * @param mixed $obj an object to be tested for equality
     * @return boolean if the other object is "equal to" this object otherwise false
     */
    public function equals($obj): bool
    {
        if (! $obj instanceof Optional) {
            return false;
        }
        return $this->orElse(null) === $obj->orElse(null);
    }
}
