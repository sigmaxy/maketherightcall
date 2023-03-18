<?php
declare(strict_types=1);

namespace Ilex\ResultOption\Error;

final class OptionException extends \Exception
{
    public static function unwrap():self{
        return new self('Cannot called Option::unwrap() on a `None` object');
    }

    public static function customMessage(string $message):self{
        return new self($message);
    }

    public static function flattenError():self{
        $message = 'flatten() only allow value is Option Object';
        return new self($message);
    }

}
