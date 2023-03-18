<?php

namespace Ilex\ResultOption\Tests\Result;

use Ilex\ResultOption\Error\OptionException;
use Ilex\ResultOption\Error\ResultException;
use Ilex\ResultOption\Result\Result;
use PHPUnit\Framework\TestCase;

/**
 * @group result
 */
class ErrTest extends TestCase
{

    public function testIsOk(): void
    {
        $result = Result::makeErr(new \Exception('Error message'));
        self::assertEquals(false, $result->isOk());
    }

    public function testIsErr(): void
    {
        $result = Result::makeErr(new \Exception('Error message'));
        self::assertEquals(true, $result->isErr());
    }

    public function testExpect()
    {
        $message = 'some message';
        $this->expectExceptionMessage($message);
        $this->expectException(ResultException::class);

        $result = Result::makeErr(new \Exception('Error message'));
        $result->expect($message);
    }

    public function testUnwrap(): void
    {
        $this->expectExceptionMessage('called `Result::unwrap()` on an `Err` value');
        $this->expectException(ResultException::class);

        $result = Result::makeErr(new \Exception('Error message'));
        $result->unwrap();
    }

    public function testExpectErr(): void
    {
        $e = new \Exception('hello');
        $result = Result::makeErr($e);
        self::assertEquals($e, $result->expectErr('error?'));
    }

    public function testUnwrapErr(): void
    {
        $e = new \Exception('hello');
        $result = Result::makeErr($e);
        self::assertEquals($e, $result->unwrap_err('error?'));
    }

    public function testUnwrapOr(): void
    {
        $result = Result::makeErr(new \Exception('Error message'));
        $default = 2;
        self::assertEquals($default, $result->unwrap_or($default));
    }

    public function testOk(): void
    {
        $result = Result::makeErr(new \Exception('Error message'));
        $option = $result->ok();
        self::assertEquals(true, $option->isNone());
    }

    public function testErr():void{
        $e = new \Exception('hello');
        $result = Result::makeErr($e);
        $option = $result->err();
        self::assertEquals(true, $option->isSome());
        self::assertEquals($e, $option->unwrap());
    }
}
