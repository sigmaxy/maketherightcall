<?php
declare(strict_types=1);

namespace Ilex\ResultOption\Option;

use Ilex\ResultOption\Error\OptionException;

/**
 * @template T
 */
final class Option
{

    /**
     * @param T $value
     */
    private function __construct(
        private bool $isSome,
        private mixed $value,
    ) {
    }

    /**
     * @template T3
     * @param T3 $value
     * @return \Ilex\ResultOption\Option\Option<T3>
     */
    public static function some(mixed $value): self
    {
        return new self(true, $value);
    }

    /**
     * @template T3
     * @return \Ilex\ResultOption\Option\Option<T3>
     */
    public static function none(): self
    {
        return new self(false, null);
    }

    /**
     * @template T3
     * @return \Ilex\ResultOption\Option\Option<T3>
     */
    public static function default(): self
    {
        return self::none();
    }

    /**
     * @template T3
     * @param T3 $value
     * @return \Ilex\ResultOption\Option\Option<T3>
     */
    public static function from(mixed $value): self
    {
        return self::some($value);
    }

    public function isSome(): bool
    {
        return $this->isSome;
    }

    public function isNone(): bool
    {
        return !$this->isSome();
    }

    /**
     * @param T $value
     */
    public function contains(mixed $value): bool
    {
        if ($this->isNone()) {
            return false;
        }

        return $value === $this->value;
    }

    /**
     * @return T
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function expect(string $message): mixed
    {
        if ($this->isSome()) {
            return $this->value;
        }

        throw OptionException::customMessage($message);
    }

    /**
     * @return T
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function unwrap():mixed
    {
        if ($this->isSome()) {
            return $this->value;
        }

        throw OptionException::unwrap();
    }

    /**
     * @param T $value
     *
     * @return T
     */
    public function unwrapOr(mixed $value): mixed
    {
        if ($this->isSome()) {
            return $this->value;
        }

        return $value;
    }

    /**
     * @return T|mixed
     */
    public function unwrapOrElse(callable $callable): mixed
    {
        if ($this->isSome()) {
            return $this->value;
        }

        return $callable();
    }

    //    public function okOr(mixed $value)
    //    {
    //        //todo
    //    }
    //
    //    public function okOrElse(callable $callable)
    //    {
    //        //todo
    //    }

    /**
     * @param \Ilex\ResultOption\Option\Option<T> $option
     *
     * @return \Ilex\ResultOption\Option\Option<T>
     */
    public function and(self $option): Option
    {
        if ($this->isSome()) {
            return $option;
        }

        return self::none();
    }


    /**
     * @param callable(T):Option<T> $callable
     *
     * @return \Ilex\ResultOption\Option\Option<T>
     */
    public function andThen(callable $callable): Option
    {
        if ($this->isNone()) {
            return $this;
        }

        return $callable($this->value);
    }


    /**
     * @param callable(T):bool $callable
     *
     * @return \Ilex\ResultOption\Option\Option<T>
     */
    public function filter(callable $callable): Option
    {
        if ($this->isNone()) {
            return $this;
        }

        if ($callable($this->value)) {
            return $this;
        }

        return self::none();
    }

    //
    //    public function or(self $option)
    //    {
    //
    //    }
    //
    //    public function orElse(self $option)
    //    {
    //
    //    }
    //
    //    public function xor(self $option)
    //    {
    //
    //    }
    //
    //    public function take()
    //    {
    //
    //    }
    //

    /**
     * @param T $value
     * @return \Ilex\ResultOption\Option\Option<T>
     */
    public function replace(mixed $value): Option
    {
        $old = $this->isSome ? self::some($this->value) : self::none();

        $this->toSome($value);
        return $old;
    }

    /**
     * @param T $value
     */
    private function toSome(mixed $value): void
    {
        $this->value = $value;
        $this->isSome = true;
    }

    //    public function transpose(){
    //
    //
    //    }

    /**
     * @return \Ilex\ResultOption\Option\Option<T>
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function flatten(): self
    {
        if ($this->isNone()) {
            return $this;
        }

        if ($this->value instanceof self) {
            return $this->value;
        }

        throw OptionException::flattenError();
    }
}
