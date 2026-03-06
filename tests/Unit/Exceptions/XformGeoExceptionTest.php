<?php

declare(strict_types=1);

namespace Icmbio\Xform\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Exceptions\XformGeoException;
use Icmbio\Xform\Exceptions\InvalidCoordinateException;
use Icmbio\Xform\Exceptions\XformException;

class XformGeoExceptionTest extends TestCase
{
    public function testInheritsFromXformException(): void
    {
        $exception = new XformGeoException('Geo error');
        
        $this->assertInstanceOf(XformGeoException::class, $exception);
        $this->assertInstanceOf(XformException::class, $exception);
    }

    public function testInvalidCoordinateExceptionForLatitudeOutOfRange(): void
    {
        $latitude = 95.5;
        $exception = InvalidCoordinateException::latitudeOutOfRange($latitude);
        
        $this->assertInstanceOf(InvalidCoordinateException::class, $exception);
        $this->assertInstanceOf(XformGeoException::class, $exception);
        $this->assertStringContainsString('95.5', $exception->getMessage());
        $this->assertStringContainsString('(-90, 90)', $exception->getMessage());
        $this->assertEquals(2001, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($latitude, $context['latitude']);
        $this->assertEquals([-90, 90], $context['valid_range']);
    }

    public function testInvalidCoordinateExceptionForLongitudeOutOfRange(): void
    {
        $longitude = -200.0;
        $exception = InvalidCoordinateException::longitudeOutOfRange($longitude);
        
        $this->assertStringContainsString('-200', $exception->getMessage());
        $this->assertStringContainsString('(-180, 180)', $exception->getMessage());
        $this->assertEquals(2002, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($longitude, $context['longitude']);
        $this->assertEquals([-180, 180], $context['valid_range']);
    }

    public function testInvalidCoordinateExceptionForInsufficientCoordinates(): void
    {
        $coordinates = '45.123'; // Missing longitude
        $exception = InvalidCoordinateException::insufficientCoordinates($coordinates);
        
        $this->assertStringContainsString('insufficient', strtolower($exception->getMessage()));
        $this->assertEquals(2003, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($coordinates, $context['coordinates']);
    }

    public function testBrazilGeoExceptionForCoordinatesOutsideBrazil(): void
    {
        $lat = 50.0; // Europe
        $lon = 2.0;
        $exception = \Icmbio\Xform\Exceptions\BrazilGeoException::outsideBrazilTerritory($lat, $lon);
        
        $this->assertStringContainsString('Brazilian territory', $exception->getMessage());
        $this->assertStringContainsString('50', $exception->getMessage());
        $this->assertEquals(2100, $exception->getCode());
        
        $context = $exception->getContext();
        $this->assertEquals($lat, $context['latitude']);
        $this->assertEquals($lon, $context['longitude']);
        $this->assertArrayHasKey('country_bounds', $context);
        
        $bounds = $context['country_bounds'];
        $this->assertIsArray($bounds);
        $this->assertArrayHasKey('north', $bounds);
        $this->assertArrayHasKey('south', $bounds);
        $this->assertArrayHasKey('east', $bounds);
        $this->assertArrayHasKey('west', $bounds);
    }
}