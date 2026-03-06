<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

// TODO - implementar testes
trait SurveyOsm
{
    /**
     * Verifica se o formulário possui campos OpenStreetMap
     */
    public function hasOsm(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:bind[@type="osm"])');
        return $result === true;
    }

    /**
     * Retorna array de nodesets dos campos OSM
     * 
     * @return array<string> Array com nodesets dos campos OSM
     */
    public function getOsms(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="osm"]/@nodeset');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }
}
