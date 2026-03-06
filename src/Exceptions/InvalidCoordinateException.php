<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção para coordenadas geográficas inválidas
 */
class InvalidCoordinateException extends XformGeoException
{
    /**
     * Exceção para latitude fora do range válido
     */
    public static function latitudeOutOfRange(float $latitude): self
    {
        return new self(
            "Latitude {$latitude} is outside valid range (-90, 90)",
            2001,
            null,
            ['latitude' => $latitude, 'valid_range' => [-90, 90]]
        );
    }

    /**
     * Exceção para longitude fora do range válido
     */
    public static function longitudeOutOfRange(float $longitude): self
    {
        return new self(
            "Longitude {$longitude} is outside valid range (-180, 180)", 
            2002,
            null,
            ['longitude' => $longitude, 'valid_range' => [-180, 180]]
        );
    }

    /**
     * Exceção para coordenadas insuficientes
     */
    public static function insufficientCoordinates(string $coordinates): self
    {
        return new self(
            "Insufficient coordinate data provided: {$coordinates}",
            2003,
            null,
            ['coordinates' => $coordinates]
        );
    }
}