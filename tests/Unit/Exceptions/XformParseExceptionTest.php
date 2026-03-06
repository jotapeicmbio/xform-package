<?php

declare(strict_types=1);

namespace Icmbio\Xform\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Exceptions\XformParseException;
use Icmbio\Xform\Exceptions\InvalidXmlException;
use Icmbio\Xform\Exceptions\XformException;

class XformParseExceptionTest extends TestCase
{
    public function testInheritsFromXformException(): void
    {
        $exception = new XformParseException('Parse error');
        
        $this->assertInstanceOf(XformParseException::class, $exception);
        $this->assertInstanceOf(XformException::class, $exception);
    }

    public function testInvalidXmlExceptionCanBeCreatedForMalformedXml(): void
    {
        $xmlContent = '<invalid><xml without closing tag';
        $exception = InvalidXmlException::malformedXml($xmlContent);
        
        $this->assertInstanceOf(InvalidXmlException::class, $exception);
        $this->assertInstanceOf(XformParseException::class, $exception);
        $this->assertStringContainsString('malformed', $exception->getMessage());
        $this->assertEquals(1001, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertArrayHasKey('xml_snippet', $context);
        $this->assertIsString($context['xml_snippet']);
        $this->assertStringContainsString('<invalid>', $context['xml_snippet']);
    }

    public function testInvalidXmlExceptionTruncatesLongXmlContent(): void
    {
        $longXml = str_repeat('<tag>content</tag>', 50); // > 200 chars
        $exception = InvalidXmlException::malformedXml($longXml);
        
        $context = $exception->getContext();
        $this->assertIsString($context['xml_snippet']);
        $this->assertLessThanOrEqual(200, strlen($context['xml_snippet']));
    }

    public function testInvalidXmlExceptionWithLibxmlErrors(): void
    {
        $xmlContent = '<invalid xml';
        $libxmlErrors = [
            (object) ['message' => 'Tag not closed', 'line' => 1]
        ];
        
        $exception = InvalidXmlException::malformedXml($xmlContent)
                        ->withContext('libxml_errors', $libxmlErrors);
        
        $context = $exception->getContext();
        $this->assertArrayHasKey('libxml_errors', $context);
        $this->assertEquals($libxmlErrors, $context['libxml_errors']);
    }
}