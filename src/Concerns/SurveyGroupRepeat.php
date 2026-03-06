<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

trait SurveyGroupRepeat
{
    /**
     * Verifica se o formulário possui grupos repetitivos
     */
    public function hasGroupRepeat(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:group[@repeat="true"] | //x:repeat)');
        return $result === true;
    }

    /**
     * Verifica se o formulário possui grupos repetitivos com UUID
     */
    public function hasGroupRepeatUuid(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:group[@repeat="true"] | //x:repeat)');
        return $result === true;
    }

    /**
     * Retorna array de nodesets dos grupos repetitivos
     * 
     * @return array<string> Array com nodesets dos grupos repetitivos
     */
    public function getGroupRepeats(): array
    {
        $nodes = $this->xpath()->query('//x:group[@repeat="true"]/@nodeset | //x:repeat/@nodeset');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }

    /**
     * Retorna grupos repetitivos específicos (registro/registros)
     * 
     * @return array<string> Array com nodesets específicos
     */
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
        
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }

    /**
     * Retorna grupos repetitivos com UUID
     * 
     * @return array<string> Array com nodesets que contêm UUID
     */
    public function getGroupRepeatsWithUuid(): array
    {
        $nodes = $this->xpath()->query("//x:bind[contains(@nodeset, 'uuid') and contains(@nodeset, '{$this->group_repeat}')]/@nodeset");
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }

    /**
     * Retorna todos os nomes/referências dos elementos XForm no body
     * 
     * @return array<string> Array com as referências dos elementos
     */
    public function getNamesXform(): array
    {
        $nodes = $this->xpath()->query("//h:body//*[self::x:input or self::x:select1 or self::x:select or self::x:group or self::x:repeat or self::x:upload]/@ref");
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }
}
