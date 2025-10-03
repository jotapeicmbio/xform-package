<?php

namespace Icmbio\Xform\Concerns;

// TODO - implementar testes
trait SurveyOsm
{
    public function hasOsm(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="osm"])');
    }

    public function getOsms(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="osm"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }
}