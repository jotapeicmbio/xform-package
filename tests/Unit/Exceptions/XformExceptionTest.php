<?php

declare(strict_types=1);

namespace Icmbio\Xform\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Exceptions\XformException;
use Exception;

class XformExceptionTest extends TestCase
{
    public function testCanCreateBasicException(): void
    {
        $exception = new XformException('Test message');
        
        $this->assertInstanceOf(XformException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertEquals([], $exception->getContext());
    }

    public function testCanCreateExceptionWithCodeAndPrevious(): void
    {
        $previous = new Exception('Previous exception');
        $exception = new XformException('Test message', 100, $previous);
        
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(100, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testCanCreateExceptionWithContext(): void
    {
        $context = ['file' => 'test.xml', 'line' => 42];
        $exception = new XformException('Test message', 0, null, $context);
        
        $this->assertEquals($context, $exception->getContext());
    }

    public function testCanAddContextFluently(): void
    {
        $exception = new XformException('Test message');
        $result = $exception->withContext('key1', 'value1');
        
        $this->assertSame($exception, $result);
        $this->assertEquals(['key1' => 'value1'], $exception->getContext());
    }

    public function testCanAddMultipleContextEntries(): void
    {
        $exception = new XformException('Test message');
        $exception->withContext('file', 'test.xml')
                 ->withContext('line', 42)
                 ->withContext('xpath', '//x:bind');
        
        $expected = [
            'file' => 'test.xml',
            'line' => 42,
            'xpath' => '//x:bind'
        ];
        
        $this->assertEquals($expected, $exception->getContext());
    }

    public function testContextOverwritesExistingKeys(): void
    {
        $initial = ['key1' => 'old_value'];
        $exception = new XformException('Test message', 0, null, $initial);
        
        $exception->withContext('key1', 'new_value');
        
        $this->assertEquals(['key1' => 'new_value'], $exception->getContext());
    }
}