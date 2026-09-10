<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns\Survey;

trait Geo
{
    /**
     * Verifica se o formulário possui campos geopoint
     */
    public function hasGeopoint(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:bind[@type="geopoint"])');
        return $result === true;
    }

    /**
     * Verifica se o formulário possui campos geotrace
     */
    public function hasGeotrace(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:bind[@type="geotrace"])');
        return $result === true;
    }

    /**
     * Verifica se o formulário possui campos geoshape
     */
    public function hasGeoshape(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:bind[@type="geoshape"])');
        return $result === true;
    }

    /**
     * Retorna array de nodesets dos campos geopoint
     * 
     * @return array<string> Array com nodesets dos geopoints
     */
    public function getGeopoints(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geopoint"]/@nodeset');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }

    /**
     * Retorna array de nodesets dos campos geotrace
     * 
     * @return array<string> Array com nodesets dos geotraces
     */
    public function getGeotraces(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geotrace"]/@nodeset');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }

    /**
     * Retorna array de nodesets dos campos geoshape
     * 
     * @return array<string> Array com nodesets dos geoshapes
     */
    public function getGeoshapes(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geoshape"]/@nodeset');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }
}
