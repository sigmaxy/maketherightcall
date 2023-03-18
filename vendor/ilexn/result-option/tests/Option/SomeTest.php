<?php

namespace Ilex\ResultOption\Tests\Option;

use Ilex\ResultOption\Error\OptionException;
use Ilex\ResultOption\Option\Option;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{

    use SimpleData;

    //    public function testOr()
    //    {
    //
    //    }
    //

    //
    //    public function testTake()
    //    {
    //
    //    }
    //

    //
    //
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

    //
    //    public function testXor()
    //    {
    //
    //    }
    //

    //
    //    public function testOkOrElse()
    //    {
    //
    //    }
    //

    //
    //
    //
    //

    /**
     * @dataProvider data
     * @testdox from() return Some
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testFrom(mixed $value): void
    {
        $some = Option::from($value);
        self::assertEquals(true, $some->isSome());
        self::assertEquals($value, $some->unwrap());
    }


    /**
     * @dataProvider data
     * @testdox isSome() return true
     */
    public function testIsSome(mixed $value): void
    {
        $some = Option::some($value);
        self::assertEquals(true, $some->isSome());
    }

    /**
     * @dataProvider data
     * @testdox isNone() return false
     */
    public function testIsNone(mixed $value): void
    {
        $some = Option::some($value);
        self::assertEquals(false, $some->isNone());
    }

    /**
     * @dataProvider data
     * @testdox contain() with same value return true
     */
    public function testContainsSameValue(mixed $value): void
    {
        $x = Option::some($value);
        self::assertEquals(true, $x->contains($value));
        self::assertEquals(false, $x->contains('not same value'));
    }

    /**
     * @dataProvider data
     * @testdox contain() with other $value return false
     */
    public function testContainsOtherValue(mixed $value): void
    {
        $x = Option::some($value);
        self::assertEquals(false, $x->contains('not same value'));
    }

    /**
     * @dataProvider data
     * @testdox expect() returns the contained [Some] value.
     *
     * @param mixed $value
     *
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testExpect(mixed $value): void
    {
        $x = Option::some($value);
        self::assertEquals($value, $x->expect('some message'));
    }

    /**
     * @dataProvider data
     * @testdox unwrap() Returns the contained [Some] value.
     *
     * @param mixed $value
     *
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testUnwrap(mixed $value): void
    {
        $x = Option::some($value);
        self::assertEquals($value, $x->unwrap());
    }

    /**
     * @dataProvider data
     * @testdox unwrapOr() Returns the contained [Some] value.
     */
    public function testUnwrapOr(mixed $value): void
    {
        $x = Option::some($value);
        self::assertEquals($value, $x->unwrapOr('default value'));
    }

    /**
     * @dataProvider data
     * @testdox unwrapOrElse() Returns the contained [Some] value
     *
     */
    public function testUnwrapOrElse(mixed $value): void
    {
        $x = Option::some($value);

        $fn = static fn() => 1;
        self::assertEquals($value, $x->unwrapOrElse($fn));

        $fn = [$this, 'callFn2'];
        self::assertEquals($value, $x->unwrapOrElse($fn));
    }

    /**
     * @dataProvider data
     * @testdox Returns [None] if the option is [None], otherwise returns input.
     *
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testAnd(mixed $value): void
    {
        $x = Option::some($value);
        $y = Option::none();

        $z = $x->and($y);
        self::assertEquals(true, $z->isNone());

        $newValue = 'new value';
        $y = Option::some($newValue);
        $z = $x->and($y);
        self::assertEquals(true, $z->isSome());
        self::assertEquals($newValue, $z->unwrap());
    }

    /**
     * @testdox Returns [None] if the option is [None], otherwise calls f with
     *     the wrapped value and returns the result.
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testAndThen(): void
    {
        $sq = static fn($x) => Option::some($x * $x);
        $nope = static fn($x) => Option::none();

        $x = Option::some(2);
        self::assertEquals(16, $x->andThen($sq)->andThen($sq)->unwrap());

        $x = Option::some(2);
        $y = $x->andThen($sq)->andThen($nope);
        self::assertEquals(true, $y->isNone());

        $x = Option::some(2);
        $y = $x->andThen($nope)->andThen($sq);
        self::assertEquals(true, $y->isNone());
    }

    /**
     * @testdox filter() Returns [None] if the option is [None], otherwise calls
     *     predicate with the wrapped value and returns.
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testFilter(): void
    {
        $isEven = static fn(int $x) => $x % 2 === 0;

        $x = Option::some(4);
        $y = $x->filter($isEven);
        self::assertEquals(true, $y->isSome());
        self::assertEquals(4, $y->unwrap());

        $x = Option::some(3);
        $y = $x->filter($isEven);
        self::assertEquals(true, $y->isNone());
    }


    /**
     * @dataProvider data
     * @testdox replace() Replaces the actual value in the option by the value
     *
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testReplace(mixed $value): void
    {
        $newValue = 'new value';
        $x = Option::some($value);
        $y = $x->replace($newValue);

        self::assertEquals($newValue, $x->unwrap());
        self::assertEquals($value, $y->unwrap());

    }

    /**
     * @dataProvider data
     * @testdox flatten() Converts from Option<Option<T>> to Option<T>
     * @throws \Ilex\ResultOption\Error\OptionException
     */
    public function testFlatten(mixed $value): void
    {
        $x = Option::some(Option::some($value));
        $y = $x->flatten();
        self::assertEquals(true, $y->isSome());
        self::assertEquals($value, $y->unwrap());

        $x = Option::some(Option::none());
        $y = $x->flatten();
        self::assertEquals(true, $y->isNone());

    }

    /**
     * @dataProvider data
     * @testdox flatten() throw when value is not [option]
     */
    public function testFlattenValueIsNotOption(mixed $value): void
    {
        $message = 'flatten() only allow value is Option Object';
        $this->expectExceptionMessage($message);
        $this->expectException(OptionException::class);

        $x = Option::some($value);
        $x->flatten();
    }
}
