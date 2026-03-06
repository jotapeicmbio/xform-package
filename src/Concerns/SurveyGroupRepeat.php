<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

trait SurveyGroupRepeat
{
    public function hasGroupRepeat(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:group[@repeat="true"] | //x:repeat)');
    }

    public function hasGroupRepeatUuid(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:group[@repeat="true"] | //x:repeat)');
    }

    public function getGroupRepeats(): array
    {
        $nodes = $this->xpath()->query('//x:group[@repeat="true"]/@nodeset | //x:repeat/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    public function getSpecificGroupRepeats(): array
    {
        $nodes = $this->xpath()->query("//x:repeat[
            (
                substring(@nodeset, string-length(@nodeset) - string-length('registro') + 1) = 'registro'
                or
                substring(@nodeset, string-length(@nodeset) - string-length('registros') + 1) = 'registros'
            )
            and not(contains(@nodeset, '/registro/'))
            and not(contains(@nodeset, '/registros/'))
        ]/@nodeset
        ");
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    public function getGroupRepeatsWithUuid(): array
    {
        $nodes = $this->xpath()->query("//x:bind[contains(@nodeset, 'uuid') and contains(@nodeset, '{$this->group_repeat}')]/@nodeset");
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    /**
     * Retorna todos os nomes/referências dos elementos XForm no body
     * 
     * @return array<string> Array com as referências dos elementos
     */
    public function getNamesXform(): array
    {
        $nodes = $this->xpath()->query("//h:body//*[self::x:input or self::x:select1 or self::x:select or self::x:group or self::x:repeat or self::x:upload]/@ref");
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }
}
