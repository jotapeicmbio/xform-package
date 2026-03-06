<?php

declare(strict_types=1);

namespace Icmbio\Xform\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Exceptions\XformAttachmentException;
use Icmbio\Xform\Exceptions\AttachmentNotFoundException;
use Icmbio\Xform\Exceptions\UnsupportedMimeTypeException;
use Icmbio\Xform\Exceptions\AttachmentSizeException;
use Icmbio\Xform\Exceptions\XformException;

class XformAttachmentExceptionTest extends TestCase
{
    public function testInheritsFromXformException(): void
    {
        $exception = new XformAttachmentException('Attachment error');
        
        $this->assertInstanceOf(XformAttachmentException::class, $exception);
        $this->assertInstanceOf(XformException::class, $exception);
    }

    public function testAttachmentNotFoundExceptionForMissingFile(): void
    {
        $filename = 'missing_photo.jpg';
        $nodeset = '/data/photo';
        $exception = AttachmentNotFoundException::missingFile($filename, $nodeset);
        
        $this->assertInstanceOf(AttachmentNotFoundException::class, $exception);
        $this->assertInstanceOf(XformAttachmentException::class, $exception);
        $this->assertStringContainsString($filename, $exception->getMessage());
        $this->assertStringContainsString('not found', strtolower($exception->getMessage()));
        $this->assertEquals(4001, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($filename, $context['filename']);
        $this->assertEquals($nodeset, $context['nodeset']);
    }

    public function testUnsupportedMimeTypeExceptionForInvalidType(): void
    {
        $mimeType = 'application/x-executable';
        $filename = 'malicious.exe';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        $exception = UnsupportedMimeTypeException::invalidType($mimeType, $filename, $allowedTypes);
        
        $this->assertInstanceOf(UnsupportedMimeTypeException::class, $exception);
        $this->assertStringContainsString($mimeType, $exception->getMessage());
        $this->assertStringContainsString($filename, $exception->getMessage());
        $this->assertEquals(4002, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($mimeType, $context['mime_type']);
        $this->assertEquals($filename, $context['filename']);
        $this->assertEquals($allowedTypes, $context['allowed_types']);
    }

    public function testAttachmentSizeExceptionForTooLargeFile(): void
    {
        $filename = 'huge_video.mp4';
        $actualSize = 50 * 1024 * 1024; // 50MB
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        $exception = AttachmentSizeException::tooLarge($filename, $actualSize, $maxSize);
        
        $this->assertInstanceOf(AttachmentSizeException::class, $exception);
        $this->assertStringContainsString($filename, $exception->getMessage());
        $this->assertStringContainsString('too large', strtolower($exception->getMessage()));
        $this->assertEquals(4003, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($filename, $context['filename']);
        $this->assertEquals($actualSize, $context['actual_size']);
        $this->assertEquals($maxSize, $context['max_size']);
    }

    public function testAttachmentSizeExceptionWithHumanReadableSizes(): void
    {
        $filename = 'document.pdf';
        $actualSize = 15 * 1024 * 1024; // 15MB
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $exception = AttachmentSizeException::tooLarge($filename, $actualSize, $maxSize);
        
        $context = $exception->getContext();
        $this->assertArrayHasKey('actual_size_human', $context);
        $this->assertArrayHasKey('max_size_human', $context);
        $this->assertEquals('15.0 MB', $context['actual_size_human']);
        $this->assertEquals('5.0 MB', $context['max_size_human']);
    }
}