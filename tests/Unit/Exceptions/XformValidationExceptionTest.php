<?php

declare(strict_types=1);

namespace Icmbio\Xform\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Exceptions\XformValidationException;
use Icmbio\Xform\Exceptions\MissingElementException;
use Icmbio\Xform\Exceptions\InvalidElementException;
use Icmbio\Xform\Exceptions\XformException;

class XformValidationExceptionTest extends TestCase
{
    public function testInheritsFromXformException(): void
    {
        $exception = new XformValidationException('Validation error');
        
        $this->assertInstanceOf(XformValidationException::class, $exception);
        $this->assertInstanceOf(XformException::class, $exception);
    }

    public function testMissingElementExceptionForRequiredElement(): void
    {
        $elementName = 'x:model';
        $exception = MissingElementException::requiredElement($elementName);
        
        $this->assertInstanceOf(MissingElementException::class, $exception);
        $this->assertInstanceOf(XformValidationException::class, $exception);
        $this->assertStringContainsString($elementName, $exception->getMessage());
        $this->assertStringContainsString('required', strtolower($exception->getMessage()));
        $this->assertEquals(3001, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($elementName, $context['element']);
    }

    public function testInvalidElementExceptionForInvalidStructure(): void
    {
        $elementName = 'x:bind';
        $expectedAttributes = ['nodeset', 'type'];
        $exception = InvalidElementException::invalidStructure($elementName, $expectedAttributes);
        
        $this->assertInstanceOf(InvalidElementException::class, $exception);
        $this->assertStringContainsString($elementName, $exception->getMessage());
        $this->assertEquals(3002, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($elementName, $context['element']);
        $this->assertEquals($expectedAttributes, $context['expected_attributes']);
    }

    public function testInvalidElementExceptionForUnsupportedAttribute(): void
    {
        $elementName = 'x:input';
        $attribute = 'unknown-attr';
        $exception = InvalidElementException::unsupportedAttribute($elementName, $attribute);
        
        $this->assertStringContainsString($elementName, $exception->getMessage());
        $this->assertStringContainsString($attribute, $exception->getMessage());
        $this->assertEquals(3003, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($elementName, $context['element']);
        $this->assertEquals($attribute, $context['attribute']);
    }
}