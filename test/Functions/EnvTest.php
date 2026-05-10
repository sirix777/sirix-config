<?php

declare(strict_types=1);

namespace SirixTest\Config\Functions;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function env;
use function putenv;

class EnvTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        putenv('TRUE_VALUE=true');
        putenv('TRUE_VALUE_SPACES=true ');
        putenv('TRUE_VALUE_PARENTHESES=(true)');
        putenv('FALSE_VALUE=false');
        putenv('FALSE_VALUE_PARENTHESES=(false)');
        putenv('EMPTY_VALUE=empty');
        putenv('EMPTY_VALUE_PARENTHESES=(empty)');
        putenv('EMPTY_VALUE_PARENTHESES_SPACES= (empty)  ');
        putenv('NULL_VALUE=null');
        putenv('NULL_VALUE_PARENTHESES=(null)');
        putenv('REGULAR_VALUE=   foo  ');
        putenv('INT_VALUE=100');
        putenv('INT_VALUE_SPACES=  80');
        putenv('NUMERIC_VALUE=58.68');
    }

    public static function tearDownAfterClass(): void
    {
        putenv('TRUE_VALUE');
        putenv('TRUE_VALUE_SPACES');
        putenv('TRUE_VALUE_PARENTHESES');
        putenv('FALSE_VALUE');
        putenv('FALSE_VALUE_PARENTHESES');
        putenv('EMPTY_VALUE');
        putenv('EMPTY_VALUE_PARENTHESES');
        putenv('EMPTY_VALUE_PARENTHESES_SPACES');
        putenv('NULL_VALUE');
        putenv('NULL_VALUE_PARENTHESES');
        putenv('REGULAR_VALUE');
        putenv('INT_VALUE');
        putenv('INT_VALUE_SPACES');
        putenv('NUMERIC_VALUE');
    }

    #[Test]
    public function envReturnsDefaultValueForUndefinedEnvVars(): void
    {
        self::assertEquals(null, env('THIS_DOES_NOT_EXIST'));
        self::assertEquals('default', env('THIS_DOES_NOT_EXIST', 'default'));
    }

    #[Test]
    public function specificValueKeywordsAreParsed(): void
    {
        self::assertTrue(env('TRUE_VALUE'));
        self::assertTrue(env('TRUE_VALUE_PARENTHESES'));
        self::assertFalse(env('FALSE_VALUE'));
        self::assertFalse(env('FALSE_VALUE_PARENTHESES'));
        self::assertEmpty(env('EMPTY_VALUE'));
        self::assertEmpty(env('EMPTY_VALUE_PARENTHESES'));
        self::assertNull(env('NULL_VALUE'));
        self::assertNull(env('NULL_VALUE_PARENTHESES'));
    }

    #[Test]
    public function numbersAreParsed(): void
    {
        self::assertSame(100, env('INT_VALUE'));
        self::assertSame(58, env('NUMERIC_VALUE'));
    }

    #[Test]
    public function valuesAreTrimmed(): void
    {
        self::assertEquals('foo', env('REGULAR_VALUE'));
        self::assertTrue(env('TRUE_VALUE_SPACES'));
        self::assertEquals('', env('EMPTY_VALUE_PARENTHESES_SPACES'));
        self::assertEquals(80, env('INT_VALUE_SPACES'));
    }

    #[Test]
    public function typeCastingToInt(): void
    {
        self::assertSame(100, env('INT_VALUE', null, 'int'));
        self::assertSame(100, env('INT_VALUE', null, 'integer'));
    }

    #[Test]
    public function typeCastingToFloat(): void
    {
        self::assertSame(100.0, env('INT_VALUE', null, 'float'));
        self::assertSame(58.68, env('NUMERIC_VALUE', null, 'float'));
        self::assertSame(58.68, env('NUMERIC_VALUE', null, 'double'));
    }

    #[Test]
    public function typeCastingToString(): void
    {
        self::assertSame('100', env('INT_VALUE', null, 'string'));
        self::assertSame('true', env('TRUE_VALUE', null, 'string'));
        self::assertSame('foo', env('REGULAR_VALUE', null, 'string'));
    }

    #[Test]
    public function typeCastingToBool(): void
    {
        putenv('BOOL_TRUE=true');
        putenv('BOOL_FALSE=false');
        putenv('BOOL_ONE=1');
        self::assertTrue(env('BOOL_TRUE', null, 'bool'));
        self::assertTrue(env('BOOL_ONE', null, 'bool'));
        self::assertTrue(env('BOOL_TRUE', null, 'boolean'));
        self::assertFalse(env('BOOL_FALSE', null, 'bool'));
        self::assertFalse(env('INT_VALUE', null, 'bool'));
        putenv('BOOL_TRUE');
        putenv('BOOL_FALSE');
        putenv('BOOL_ONE');
    }

    #[Test]
    public function typeCastingToArray(): void
    {
        putenv('COMMA_VALUES=foo,bar,baz');
        putenv('EMPTY_ARRAY=');
        self::assertSame(['foo', 'bar', 'baz'], env('COMMA_VALUES', null, 'array'));
        self::assertSame([], env('EMPTY_ARRAY', null, 'array'));
        putenv('COMMA_VALUES');
        putenv('EMPTY_ARRAY');
    }

    #[Test]
    public function typeCastingReturnsDefaultForUndefinedEnv(): void
    {
        self::assertSame(42, env('UNDEFINED_ENV', 42, 'int'));
        self::assertSame(['a', 'b'], env('UNDEFINED_ENV', ['a', 'b'], 'array'));
    }
}
