<?php

namespace Ilex\ResultOption\Tests\Option;

use Ilex\ResultOption\Error\OptionException;
use Ilex\ResultOption\Option\Option;
use PHPUnit\Framework\TestCase;

class NoneTest extends TestCase
{

    use SimpleData;

    //    public function testOr()
    //    {
    //
    //    }
    //
    //    public function testUnwrapOr()
    //    {
    //
    //    }
    //
    //    public function testTake()
    //    {
    //
    //    }
    //
    //    public function testAndThen()
    //    {
    //
    //    }
    //
    //    public function testReplace()
    //    {
    //
    //    }
    //
    //    public function testOkOr()
    //    {
    //
    //    }
    //
    //    public function testOrElse()
    //    {
    //
    //    }
    //
    //    public function testAnd()
    //    {
    //
    //    }
    //
    //    public function testXor()
    //    {
    //
    //    }
    //
    //    public function testUnwrap()
    //    {
    //
    //    }
    //
    //    public function testOkOrElse()
    //    {
    //
    //    }
    //
    //    public function testFilter()
    //    {
    //
    //    }
    //
    //    public function testUnwrapOrElse()
    //    {
    //
    //    }
    //
    //

    /**
     * @testdox default() return None
     */
    public function testDefault(): void
    {
        $none = Option::default();
        self::assertEquals(true, $none->isNone());
    }

    /**
     * @testdox isSome() return false
     */
    public function testIsSome(): void
    {
        $none = Option::none();
        self::assertEquals(false, $none->isSome());
    }

    /**
     * @testdox isNone() return true
     */
    public function testIsNone(): void
    {
        $none = Option::none();
        self::assertEquals(true, $none->isNone());
    }

    /**
     * @testdox contains() any value return false
     */
    public function testContains(): void
    {
        $x = Option::none();
        self::assertEquals(false, $x->contains('any value'));
    }

    /**
     * @testdox expect() throw if the value is a [None] with a custom message.
     */
    public function testExpect(): void
    {
        $message = 'some message';
        $this->expectExceptionMessage($message);
        $this->expectException(OptionException::class);
        $x = Option::none();
        $x->expect($message);
    }

    /**
     * @testdox unwrap() throw if the self value equals [None].
     */
    public function testUnwrap():void
    {
        $message = 'Cannot called Option::unwrap() on a `None` object';
        $this->expectExceptionMessage($message);
        $this->expectException(OptionException::class);
        $x = Option::none();
        $x->unwrap();
    }

    /**
     *
     * @testdox unwrapOr() Returns a provided default.
     */
    public function testUnwrapOr(): void
    {
        $x = Option::none();
        $default = 'default value';
        self::assertEquals($default, $x->unwrapOr($default));
    }

    /**
     * @testdox unwrapOrElse() computes it from a closure.
     */
    public function testUnwrapOrElse():void{
        $x = Option::none();

        $fn = static fn() => 1;
        self::assertEquals(1, $x->unwrapOrElse($fn));

        $fn = [$this, 'callFn2'];
        self::assertEquals(2, $x->unwrapOrElse($fn));
    }

    /**
     * @dataProvider data
     * @testdox Returns [None] if the option is [None], otherwise returns input.
     */
    public function testAnd(mixed $value): void
    {
        $x = Option::none();
        $y = Option::none();

        $z = $x->and($y);
        self::assertEquals(true, $z->isNone());

        $y = Option::some($value);
        $z = $x->and($y);
        self::assertEquals(true, $z->isNone());
    }

    public function testAndThen(): void{
        $sq = static fn($x) => Option::some($x * $x);

        $x = Option::none();
        $y = $x->andThen($sq)->andThen($sq);
        self::assertEquals(true, $y->isNone());
    }

    /**
     * @testdox filter() Returns [None] if the option is [None]
     */
    public function testFilter():void
    {
        $isEven = static fn(int $x) => $x % 2 === 0;
        $x = Option::none();
        $y = $x->filter($isEven);
        self::assertEquals(true,$y->isNone());
    }

    /**
     * @testdox replace() Replaces the actual value in the option
     *
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testReplace(): void
    {
        $newValue = 'new value';
        $x = Option::none();
        $old = $x->replace($newValue);

        self::assertEquals(true, $x->isSome());
        self::assertEquals($newValue, $x->unwrap());
        self::assertEquals(true, $old->isNone());

    }

    /**
     * @testdox flatten() Converts from Option<Option<T>> to Option<T>
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testFlatten(): void
    {
        $x = Option::none();
        $y = $x->flatten();
        self::assertEquals(true,$y->isNone() );
    }
}
