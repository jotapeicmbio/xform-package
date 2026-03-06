<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

trait SurveyGeo
{
    /**
     * Verifica se o formulário possui campos geopoint
     */
    public function hasGeopoint(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="geopoint"])');
    }

    /**
     * Verifica se o formulário possui campos geotrace
     */
    public function hasGeotrace(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="geotrace"])');
    }

    /**
     * Verifica se o formulário possui campos geoshape
     */
    public function hasGeoshape(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="geoshape"])');
    }

    /**
     * Retorna array de nodesets dos campos geopoint
     * 
     * @return array<string> Array com nodesets dos geopoints
     */
    public function getGeopoints(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geopoint"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    /**
     * Retorna array de nodesets dos campos geotrace
     * 
     * @return array<string> Array com nodesets dos geotraces
     */
    public function getGeotraces(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geotrace"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    /**
     * Retorna array de nodesets dos campos geoshape
     * 
     * @return array<string> Array com nodesets dos geoshapes
     */
    public function getGeoshapes(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geoshape"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }
}