<?php

namespace Ilex\ResultOption\Tests\Result;

use Ilex\ResultOption\Error\ResultException;
use Ilex\ResultOption\Result\Result;
use Ilex\ResultOption\Tests\Option\SimpleData;
use PHPUnit\Framework\TestCase;

/**
 * @group result
 */
class OkTest extends TestCase
{

    use SimpleData;

    /**
     * @dataProvider data
     */
    public function testIsOk(mixed $value): void
    {
        $result = Result::makeOk($value);
        self::assertEquals(true, $result->isOk());
    }

    /**
     * @dataProvider data
     */
    public function testIsErr(mixed $value): void
    {
        $result = Result::makeOk($value);
        self::assertEquals(false, $result->isErr());
    }

    /**
     * @dataProvider data
     */
    public function testExpect(mixed $value)
    {
        $result = Result::makeOk($value);
        self::assertEquals($value, $result->expect('some message'));
    }

    /**
     * @dataProvider data
     * @throws \Ilex\ResultOption\Error\ResultException
     */
    public function testUnwrap(mixed $value): void
    {
        $result = Result::makeOk($value);
        self::assertEquals($value, $result->unwrap());
    }

    /**
     * @dataProvider data
     */
    public function testExpectErr(mixed $value): void
    {
        $message = 'some message';
        $this->expectExceptionMessage($message);
        $this->expectException(ResultException::class);

        $result = Result::makeOk($value);
        $result->expectErr($message);
    }


    /**
     * @dataProvider data
     */
    public function testUnwrapErr(mixed $value): void
    {
        $message = 'called `Result::unwrap_err()` on an `Ok` value';
        $this->expectExceptionMessage($message);
        $this->expectException(ResultException::class);

        $result = Result::makeOk($value);
        $result->unwrap_err();
    }

    /**
     * @dataProvider data
     */
    public function testUnwrapOr(mixed $value):void{
        $result = Result::makeOk($value);
        $default = 2;
        self::assertEquals($value, $result->unwrap_or($default));
    }

    /**
     * @dataProvider data
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testOk(mixed $value): void
    {
        $result = Result::makeOk($value);
        $option = $result->ok();
        self::assertEquals(true, $option->isSome());
        self::assertEquals($value, $option->unwrap());
    }

    /**
     * @dataProvider data
     */
    public function testErr(mixed $value):void{
        $result = Result::makeOk($value);
        $option = $result->err();
        self::assertEquals(true, $option->isNone());
    }
}
