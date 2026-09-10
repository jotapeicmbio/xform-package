<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

trait SurveyGroup
{
    /**
     * Verifica se o formulário possui grupos repetitivos
     */
    public function hasGroup(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:group)');
        return $result === true;
    }

    /**
     * Retorna array de nodesets dos grupos repetitivos
     * 
     * @return array<string> Array com nodesets dos grupos repetitivos
     */
    public function getGroups(): array
    {
        $nodes = $this->xpath()->query('//x:group/@ref');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }

    /**
     * Retorna array de nodesets dos sem grupo repetitivos
     * 
     * @return array<string> Array com nodesets sem grupos repetitivos
     */
    public function getWithoutRepeatGroups(): array
    {
        $nodes = $this->xpath()->query('//x:group[not(@repeat="true")]//@ref');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }
}
