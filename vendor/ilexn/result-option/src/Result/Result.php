<?php
declare(strict_types=1);

namespace Ilex\ResultOption\Result;

use Ilex\ResultOption\Error\ResultException;
use Ilex\ResultOption\Option\Option;

/**
 * @template T
 */
final class Result
{

    /**
     * @param T $value
     */
    private function __construct(
        private readonly mixed $value,
        private readonly bool $ok,
    ) {
    }

    /**
     * @template T2
     * @param T2 $value
     * @return \Ilex\ResultOption\Result\Result<T2>
     */
    public static function makeOk(mixed $value): Result
    {
        return new self($value, true);
    }

    /**
     * @param \Throwable $throwable
     *
     * @return \Ilex\ResultOption\Result\Result<\Throwable>
     */
    public static function makeErr(\Throwable $throwable): Result
    {
        return new self($throwable, false);
    }

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function isErr(): bool
    {
        return !$this->ok;
    }

    /**
     * @return \Ilex\ResultOption\Option\Option<T>
     */
    public function ok(): Option
    {
        if ($this->isOk()) {
            return Option::some($this->value);
        }

        return Option::none();
    }

    /**
     * @return \Ilex\ResultOption\Option\Option<T>
     */
    public function err(): Option
    {
        if ($this->isOk()) {
            return Option::none();
        }

        return Option::some($this->value);
    }

    //    public function and(mixed $value){
    //
    //    }

    //    public function andThen(){
    //
    //    }
    //
    //    public function or(){
    //
    //    }

    //    public function orElse(){}
    /**
     * @template T3
     * @param T3 $value
     *
     * @return T|T3
     */
    public function unwrap_or(mixed $value)
    {
        if ($this->isOk()){
            return $this->value;
        }

        return $value;
    }

    //    public function unwrap_or_else{}

    /**
     * @return T
     * @throws \Ilex\ResultOption\Error\ResultException
     */
    public function expect(string $message): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        if ($this->value instanceof \Throwable) {
            throw ResultException::customMessage($message, $this->value);
        }

        throw ResultException::ShouldNotHappen();
    }

    /**
     * @return T
     * @throws \Ilex\ResultOption\Error\ResultException
     */
    public function unwrap(): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        if ($this->value instanceof \Throwable) {
            throw ResultException::unwrap($this->value);
        }

        throw ResultException::ShouldNotHappen();
    }

    /**
     * @param string $message
     *
     * @return T
     * @throws \Ilex\ResultOption\Error\ResultException
     */
    public function expectErr(string $message)
    {
        if ($this->isOk()) {
            throw ResultException::customMessage($message);
        }

        return $this->value;
    }

    /**
     * @return T
     * @throws \Ilex\ResultOption\Error\ResultException
     */
    public function unwrap_err(): \Throwable
    {
        if ($this->isOk()) {
            throw ResultException::unwrapErr();
        }

        return $this->value;
    }

    //    public function transpose()
    //    {
    //    }
    //
    //    public function flatten()
    //    {
    //    }


}
