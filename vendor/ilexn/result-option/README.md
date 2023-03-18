# Result-Option
> Just for fun, Just feel Rust, Rust like enum Result and Option.

[![Latest Stable Version](https://poser.pugx.org/ilexn/result-option/v/stable)](https://packagist.org/packages/ilexn/result-option)
[![Total Downloads](https://poser.pugx.org/ilexn/result-option/downloads)](https://packagist.org/packages/ilexn/result-option)

![GitHub Action](https://github.com/iLexN/result-option/workflows/CI%20Check/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/iLexN/result-option/badge.svg?branch=master)](https://coveralls.io/github/iLexN/result-option?branch=master)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/iLexN/result-option/master)](https://infection.github.io)

## Installation
```sh
composer require ilexn/result-option
```

## Usage example
```php
<?php
declare(strict_types=1);

use Ilex\ResultOption\Option\Option;

require_once __DIR__ . '/../vendor/autoload.php';


$some = Option::some('any_value');
if ($some->isSome()){
   $value = $some->unwrap();
}

```

## API
> Take a look in the test folder for details
```
None (Ilex\ResultOption\Tests\Option\None)
✔ default() return None
✔ isSome() return false
✔ isNone() return true
✔ contains() any value return false
✔ expect() throw if the value is a [None] with a custom message.
✔ unwrap() throw if the self value equals [None].
✔ unwrapOr() Returns a provided default.
✔ unwrapOrElse() computes it from a closure.
✔ Returns [None] if the option is [None], otherwise returns input. with data set "null"
✔ Returns [None] if the option is [None], otherwise returns input. with data set "int"
✔ Returns [None] if the option is [None], otherwise returns input. with data set "string"
✔ Returns [None] if the option is [None], otherwise returns input. with data set "object"
✔ And then
✔ filter() Returns [None] if the option is [None]
✔ replace() Replaces the actual value in the option
✔ flatten() Converts from Option<Option<T>> to Option<T>

Some (Ilex\ResultOption\Tests\Option\Some)
✔ from() return Some with data set "null"
✔ from() return Some with data set "int"
✔ from() return Some with data set "string"
✔ from() return Some with data set "object"
✔ isSome() return true with data set "null"
✔ isSome() return true with data set "int"
✔ isSome() return true with data set "string"
✔ isSome() return true with data set "object"
✔ isNone() return false with data set "null"
✔ isNone() return false with data set "int"
✔ isNone() return false with data set "string"
✔ isNone() return false with data set "object"
✔ contain() with same value return true with data set "null"
✔ contain() with same value return true with data set "int"
✔ contain() with same value return true with data set "string"
✔ contain() with same value return true with data set "object"
✔ contain() with other NULL return false
✔ contain() with other 2 return false
✔ contain() with other hello return false
✔ contain() with other stdClass return false
✔ expect() returns the contained [Some] value. with data set "null"
✔ expect() returns the contained [Some] value. with data set "int"
✔ expect() returns the contained [Some] value. with data set "string"
✔ expect() returns the contained [Some] value. with data set "object"
✔ unwrap() Returns the contained [Some] value. with data set "null"
✔ unwrap() Returns the contained [Some] value. with data set "int"
✔ unwrap() Returns the contained [Some] value. with data set "string"
✔ unwrap() Returns the contained [Some] value. with data set "object"
✔ unwrapOr() Returns the contained [Some] value. with data set "null"
✔ unwrapOr() Returns the contained [Some] value. with data set "int"
✔ unwrapOr() Returns the contained [Some] value. with data set "string"
✔ unwrapOr() Returns the contained [Some] value. with data set "object"
✔ unwrapOrElse() Returns the contained [Some] value with data set "null"
✔ unwrapOrElse() Returns the contained [Some] value with data set "int"
✔ unwrapOrElse() Returns the contained [Some] value with data set "string"
✔ unwrapOrElse() Returns the contained [Some] value with data set "object"
✔ Returns [None] if the option is [None], otherwise returns input. with data set "null"
✔ Returns [None] if the option is [None], otherwise returns input. with data set "int"
✔ Returns [None] if the option is [None], otherwise returns input. with data set "string"
✔ Returns [None] if the option is [None], otherwise returns input. with data set "object"
✔ Returns [None] if the option is [None], otherwise calls f with
✔ filter() Returns [None] if the option is [None], otherwise calls
✔ replace() Replaces the actual value in the option by the value with data set "null"
✔ replace() Replaces the actual value in the option by the value with data set "int"
✔ replace() Replaces the actual value in the option by the value with data set "string"
✔ replace() Replaces the actual value in the option by the value with data set "object"
✔ flatten() Converts from Option<Option<T>> to Option<T> with data set "null"
✔ flatten() Converts from Option<Option<T>> to Option<T> with data set "int"
✔ flatten() Converts from Option<Option<T>> to Option<T> with data set "string"
✔ flatten() Converts from Option<Option<T>> to Option<T> with data set "object"
✔ flatten() throw when value is not [option] with data set "null"
✔ flatten() throw when value is not [option] with data set "int"
✔ flatten() throw when value is not [option] with data set "string"
✔ flatten() throw when value is not [option] with data set "object"

Err (Ilex\ResultOption\Tests\Result\Err)
✔ Is ok
✔ Is err
✔ Expect
✔ Unwrap
✔ Expect err
✔ Unwrap err
✔ Unwrap or
✔ Ok
✔ Err

Ok (Ilex\ResultOption\Tests\Result\Ok)
✔ Is ok with data set "null"
✔ Is ok with data set "int"
✔ Is ok with data set "string"
✔ Is ok with data set "object"
✔ Is err with data set "null"
✔ Is err with data set "int"
✔ Is err with data set "string"
✔ Is err with data set "object"
✔ Expect with data set "null"
✔ Expect with data set "int"
✔ Expect with data set "string"
✔ Expect with data set "object"
✔ Unwrap with data set "null"
✔ Unwrap with data set "int"
✔ Unwrap with data set "string"
✔ Unwrap with data set "object"
✔ Expect err with data set "null"
✔ Expect err with data set "int"
✔ Expect err with data set "string"
✔ Expect err with data set "object"
✔ Unwrap err with data set "null"
✔ Unwrap err with data set "int"
✔ Unwrap err with data set "string"
✔ Unwrap err with data set "object"
✔ Unwrap or with data set "null"
✔ Unwrap or with data set "int"
✔ Unwrap or with data set "string"
✔ Unwrap or with data set "object"
✔ Ok with data set "null"
✔ Ok with data set "int"
✔ Ok with data set "string"
✔ Ok with data set "object"
✔ Err with data set "null"
✔ Err with data set "int"
✔ Err with data set "string"
✔ Err with data set "object"
```
