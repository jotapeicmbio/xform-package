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

    /**
     * Extrai todos os atributos dos elementos bind
     * 
     * @return array<string, array<string, string|null>> Array indexado por nodeset com atributos
     */
    public function getBindAttributes(): array
    {
        $binds = [];
        
        // Tenta ambos os namespaces: prefixado (x:) e padrão
        $queries = [
            '//x:bind[@nodeset]',      // Namespace prefixado
            '//bind[@nodeset]'         // Namespace padrão (pyxform)
        ];
        
        $nodes = null;
        foreach ($queries as $query) {
            $nodes = $this->xpath()->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                break;
            }
        }
        
        if ($nodes === false || $nodes->length === 0) {
            return [];
        }
        
        foreach ($nodes as $node) {
            $nodeset = (string) $node->getAttribute('nodeset');
            if ($nodeset === '') {
                continue;
            }
            
            $binds[$nodeset] = [
                'type' => $node->getAttribute('type') ?: null,
                'required' => $node->getAttribute('required') ?: null,
                'readonly' => $node->getAttribute('readonly') ?: null,
                'calculate' => $node->getAttribute('calculate') ?: null,
                'constraint' => $node->getAttribute('constraint') ?: null,
                'relevant' => $node->getAttribute('relevant') ?: null,
            ];
        }
        
        return $binds;
    }

    /**
     * Extrai labels e hints dos elementos do body
     * 
     * @return array<string, array{label: string|null, hint: string|null}> Array indexado por ref
     */
    public function getBodyLabels(): array
    {
        $labels = [];
        
        // Tenta ambos os namespaces para elements do body
        $queries = [
            '//h:body//*[self::x:input or self::x:select1 or self::x:select or self::x:textarea or self::x:trigger][@ref]',  // Namespace prefixado
            '//h:body//*[self::input or self::select1 or self::select or self::textarea or self::trigger][@ref]'            // Namespace padrão (pyxform)
        ];
        
        $nodes = null;
        foreach ($queries as $query) {
            $nodes = $this->xpath()->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                break;
            }
        }
        
        if ($nodes === false) {
            return [];
        }
        
        foreach ($nodes as $node) {
            $ref = (string) $node->getAttribute('ref');
            if ($ref === '') {
                continue;
            }
            
            // Extrai label - tenta ambos os namespaces
            $label = null;
            $labelQueries = ['.//x:label', './/label'];
            foreach ($labelQueries as $labelQuery) {
                $labelNodes = $this->xpath()->query($labelQuery, $node);
                if ($labelNodes !== false && $labelNodes->length > 0) {
                    $label = trim($labelNodes->item(0)->textContent);
                    break;
                }
            }
            
            // Extrai hint - tenta ambos os namespaces
            $hint = null;
            $hintQueries = ['.//x:hint', './/hint'];
            foreach ($hintQueries as $hintQuery) {
                $hintNodes = $this->xpath()->query($hintQuery, $node);
                if ($hintNodes !== false && $hintNodes->length > 0) {
                    $hint = trim($hintNodes->item(0)->textContent);
                    break;
                }
            }
            
            $labels[$ref] = [
                'label' => $label,
                'hint' => $hint,
            ];
        }
        
        return $labels;
    }

    /**
     * Extrai constraints de validação dos campos
     * 
     * @return array<string, array{constraint: string|null, constraintMsg: string|null}> Constraints por nodeset
     */
    public function getFieldConstraints(): array
    {
        $constraints = [];
        
        $nodes = $this->xpath()->query('//x:bind[@constraint or @jr:constraintMsg]');
        if ($nodes === false) {
            return [];
        }
        
        foreach ($nodes as $node) {
            $nodeset = (string) $node->getAttribute('nodeset');
            if ($nodeset === '') {
                continue;
            }
            
            $constraints[$nodeset] = [
                'constraint' => $node->getAttribute('constraint') ?: null,
                'constraintMsg' => $node->getAttributeNS('http://openrosa.org/javarosa', 'constraintMsg') ?: null,
            ];
        }
        
        return $constraints;
    }

    /**
     * Retorna informações completas de todos os campos do survey
     * 
     * @return array<int, array{name: string, nodeset: string, type: string|null, required: string|null, readonly: string|null, calculate: string|null, label: string|null, hint: string|null, constraint: string|null, constraintMsg: string|null, relevant: string|null}> Array com informações dos campos
     */
    public function getSurvey(): array
    {
        $binds = $this->getBindAttributes();
        $labels = $this->getBodyLabels();
        $constraints = $this->getFieldConstraints();
        
        $survey = [];
        
        // Combina informações de bind com labels
        foreach ($binds as $nodeset => $bindData) {
            $survey[] = [
                'name' => basename($nodeset),
                'nodeset' => $nodeset,
                'type' => $bindData['type'],
                'required' => $bindData['required'],
                'readonly' => $bindData['readonly'],
                'calculate' => $bindData['calculate'],
                'constraint' => $bindData['constraint'],
                'relevant' => $bindData['relevant'],
                'label' => $labels[$nodeset]['label'] ?? null,
                'hint' => $labels[$nodeset]['hint'] ?? null,
                'constraintMsg' => $constraints[$nodeset]['constraintMsg'] ?? null,
            ];
        }
        
        // Adiciona campos que só existem no body (sem bind)
        foreach ($labels as $ref => $labelData) {
            // Verifica se já adicionamos este campo
            $alreadyAdded = false;
            foreach ($survey as $field) {
                if ($field['nodeset'] === $ref) {
                    $alreadyAdded = true;
                    break;
                }
            }
            
            if (!$alreadyAdded) {
                $survey[] = [
                    'name' => basename($ref),
                    'nodeset' => $ref,
                    'type' => null,
                    'required' => null,
                    'readonly' => null,
                    'calculate' => null,
                    'constraint' => null,
                    'relevant' => null,
                    'label' => $labelData['label'],
                    'hint' => $labelData['hint'],
                    'constraintMsg' => null,
                ];
            }
        }
        
        return $survey;
    }
}