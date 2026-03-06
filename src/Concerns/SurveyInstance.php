<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

trait SurveyInstance
{
    /**
     * Verifica se o formúlário possui ID definido
     */
    public function hasId(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//*[@id][parent::x:instance])');
        return $result === true;
    }

    /**
     * Retorna o ID do formulário se existir
     */
    public function getId(): ?string
    {
        if (!$this->hasId()) {
            return null;
        }
        
        $result = $this->xpath()->evaluate('string(//*[@id][parent::x:instance]/@id)');
        return is_string($result) && $result !== '' ? $result : null;
    }

    /**
     * Verifica se o formulário possui versão definida
     */
    public function hasVersion(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//*[@version][parent::x:instance])');
        return $result === true;
    }

    /**
     * Retorna a versão do formulário se existir
     */
    public function getVersion(): ?string
    {
        $result = $this->xpath()->evaluate('string(//*[@version][parent::x:instance]/@version)');
        return is_string($result) && $result !== '' ? $result : null;
    }

    /**
     * Retorna array de nodesets dos elementos bind no XForm
     * 
     * @return array<string> Array de strings com os nodesets
     */
    public function getNodeset(): array
    {
        $nodes = $this->xpath()->query('//x:bind/@nodeset');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }

    /**
     * Extrai informações básicas dos campos do formulário
     * 
     * @return array<array{name: string, label: ?string}> Array com informações dos campos
     */
    public function getSimpleInfoSurvey(): array
    {
        $fields = [];

        $nodes = $this->xpath()->query(
            '//h:body//*[self::x:input or self::x:select1 or self::x:select or self::x:textarea]'
        );
        
        if ($nodes === false) {
            return [];
        }

        foreach ($nodes as $node) {
            $ref = $node->getAttribute('ref');
            $name = basename($ref);
            
            $labelQuery = $this->xpath()->query('.//x:label', $node);
            $labelNode = $labelQuery !== false ? $labelQuery->item(0) : null;
            $label = $labelNode ? trim($labelNode->textContent) : null;

            $fields[] = [
                'name' => $name,
                'label' => $label,
            ];
        }

        return $fields;
    }
}