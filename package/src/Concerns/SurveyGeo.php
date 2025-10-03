<?php

namespace Icmbio\Xform\Concerns;

trait SurveyGeo
{
    public function hasGeopoint(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="geopoint"])');
    }

    public function hasGeotrace(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="geotrace"])');
    }

    public function hasGeoshape(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="geoshape"])');
    }

    public function getGeopoints(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geopoint"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    public function getGeotraces(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geotrace"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    public function getGeoshapes(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="geoshape"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }
}