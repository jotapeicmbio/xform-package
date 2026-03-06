<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção específica para validações geográficas brasileiras
 */
class BrazilGeoException extends XformGeoException
{
    /**
     * Exceção para coordenadas fora do território brasileiro
     */
    public static function outsideBrazilTerritory(float $lat, float $lon): self
    {
        return new self(
            "Coordinates ({$lat}, {$lon}) are outside Brazilian territory",
            2100,
            null,
            [
                'latitude' => $lat,
                'longitude' => $lon, 
                'country_bounds' => [
                    'north' => 5.2719,
                    'south' => -33.7681,
                    'east' => -28.6453,
                    'west' => -73.9872
                ]
            ]
        );
    }
}