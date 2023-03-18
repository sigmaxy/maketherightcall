<?php
declare(strict_types=1);

namespace Ilex\ResultOption\Error;

final class ResultException extends \Exception
{

    public static function customMessage(
        string $message,
        \Throwable $throwable = null
    ): self {
        return new self(message: $message, previous: $throwable);
    }

    public static function unwrap(\Throwable $throwable): self
    {
        $message = 'called `Result::unwrap()` on an `Err` value';
        return new self(message: $message, previous: $throwable);
    }

    public static function unwrapErr(): self
    {
        $message = 'called `Result::unwrap_err()` on an `Ok` value';
        return new self(message: $message);
    }

    public static function ShouldNotHappen():self{
        $message = 'Should Not Happen...need check more';
        return new self(message: $message);
    }
}
