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
     * Extrai labels e hints dos elementos do body, incluindo resolução de referências itext
     * 
     * @return array<string, array{label: string|null, hint: string|null}> Array indexado por ref
     */
    public function getBodyLabels(): array
    {
        $labels = [];
        
        // Extrai traduções do itext uma vez
        $itextTranslations = $this->getItextTranslations();
        
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
                    $labelNode = $labelNodes->item(0);
                    
                    // Verifica se é referência itext
                    $refAttribute = $labelNode->getAttribute('ref');
                    if ($refAttribute !== '') {
                        // Resolve referência itext
                        $label = $this->resolveItextReference($refAttribute, $itextTranslations);
                    } else {
                        // Texto direto
                        $label = trim($labelNode->textContent);
                    }
                    break;
                }
            }
            
            // Extrai hint - tenta ambos os namespaces
            $hint = null;
            $hintQueries = ['.//x:hint', './/hint'];
            foreach ($hintQueries as $hintQuery) {
                $hintNodes = $this->xpath()->query($hintQuery, $node);
                if ($hintNodes !== false && $hintNodes->length > 0) {
                    $hintNode = $hintNodes->item(0);
                    
                    // Verifica se é referência itext
                    $refAttribute = $hintNode->getAttribute('ref');
                    if ($refAttribute !== '') {
                        // Resolve referência itext
                        $hint = $this->resolveItextReference($refAttribute, $itextTranslations);
                    } else {
                        // Texto direto
                        $hint = trim($hintNode->textContent);
                    }
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

    /**
     * Retorna informações hierárquicas dos campos do survey com suporte a grupos repetitivos e choices
     * 
     * Este método processa todos os campos do formulário e retorna uma estrutura hierárquica que inclui:
     * - Campos simples com seus tipos, labels e choices (quando aplicável)
     * - Grupos repetitivos com seus filhos aninhados 
     * - Diferenciação entre tipos select1 (uma escolha) e select (múltiplas escolhas)
     * - Choices extraídos tanto de elementos estáticos (item/label/value) quanto dinâmicos (instances)
     * 
     * @return array<int, array{name: string, label: string|null, type: string, choices?: array<int, array{value: string, label: string}>, children?: array<int, array{name: string, label: string|null, type: string, choices?: array<int, array{value: string, label: string}>}>}> Array hierárquico com informações dos campos
     */
    public function getSurveyHierarchical(): array
    {
        $survey = $this->getSurvey();
        $hierarchical = [];
        $processedRepeats = [];
        
        foreach ($survey as $field) {
            $nodeset = $field['nodeset'];
            $fieldName = $field['name'];
            
            // Verifica se é um campo dentro de um grupo repetitivo
            if ($this->isRepeatField($nodeset)) {
                $repeatGroupPath = $this->getRepeatGroupPath($nodeset);
                $repeatGroupName = basename($repeatGroupPath);
                
                // Se ainda não processamos este grupo repetitivo
                if (!isset($processedRepeats[$repeatGroupName])) {
                    $processedRepeats[$repeatGroupName] = true;
                    
                    // Cria o grupo repetitivo
                    $repeatgroup = [
                        'name' => $repeatGroupName,
                        'label' => $this->getRepeatGroupLabel($repeatGroupPath),
                        'type' => 'repeat',
                        'children' => []
                    ];
                    
                    // Adiciona todos os filhos deste grupo repetitivo
                    foreach ($survey as $childField) {
                        if ($this->belongsToRepeatGroup($childField['nodeset'], $repeatGroupPath)) {
                            $childNode = [
                                'name' => $repeatGroupName . '/' . $childField['name'],
                                'label' => $childField['label'],
                                'type' => $this->mapFieldType($childField['type'], $childField['nodeset'])
                            ];
                            
                            // Adiciona choices se o campo tiver
                            $choices = $this->getChoicesForField($childField['nodeset']);
                            if ($choices !== null) {
                                $childNode['choices'] = $choices;
                            }
                            
                            $repeatgroup['children'][] = $childNode;
                        }
                    }
                    
                    $hierarchical[] = $repeatgroup;
                }
            } 
            // Campo simples (não está em grupo repetitivo)
            elseif (!$this->isChildOfRepeatGroup($nodeset, $survey)) {
                // Define o nome do campo
                $finalFieldName = $fieldName;
                
                // Se é campo de grupo normal, adiciona prefixo do grupo
                if ($this->isNormalGroupField($nodeset)) {
                    $groupPath = $this->getNormalGroupPath($nodeset);
                    $groupName = basename($groupPath);
                    $finalFieldName = $groupName . '/' . $fieldName;
                }
                
                $fieldNode = [
                    'name' => $finalFieldName,
                    'label' => $field['label'],
                    'type' => $this->mapFieldType($field['type'], $nodeset)
                ];
                
                // Adiciona choices se o campo tiver
                $choices = $this->getChoicesForField($nodeset);
                if ($choices !== null) {
                    $fieldNode['choices'] = $choices;
                }
                
                $hierarchical[] = $fieldNode;
            }
        }
        
        return $hierarchical;
    }
    
    /**
     * Verifica se um campo pertence a um grupo repetitivo
     */
    private function isRepeatField(string $nodeset): bool
    {
        // Busca por elementos <repeat> no XML
        $repeatNodes = $this->xpath()->query('//x:repeat');
        if ($repeatNodes === false) {
            return false;
        }
        
        foreach ($repeatNodes as $repeatNode) {
            $repeatNodeset = $repeatNode->getAttribute('nodeset');
            if (str_starts_with($nodeset, $repeatNodeset . '/')) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtém o caminho do grupo repetitivo pai
     */
    private function getRepeatGroupPath(string $nodeset): string
    {
        $repeatNodes = $this->xpath()->query('//x:repeat');
        if ($repeatNodes === false) {
            return '';
        }
        
        foreach ($repeatNodes as $repeatNode) {
            $repeatNodeset = $repeatNode->getAttribute('nodeset');
            if (str_starts_with($nodeset, $repeatNodeset . '/')) {
                return $repeatNodeset;
            }
        }
        
        return '';
    }
    
    /**
     * Obtém o label de um grupo repetitivo
     */
    private function getRepeatGroupLabel(string $groupPath): ?string
    {
        // Busca o grupo correspondente no body
        $groupNodes = $this->xpath()->query('//x:group[@ref="' . $groupPath . '"]');
        if ($groupNodes === false || $groupNodes->length === 0) {
            return null;
        }
        
        $groupNode = $groupNodes->item(0);
        $labelQueries = ['.//x:label', './/label'];
        
        foreach ($labelQueries as $labelQuery) {
            $labelNodes = $this->xpath()->query($labelQuery, $groupNode);
            if ($labelNodes !== false && $labelNodes->length > 0) {
                return trim($labelNodes->item(0)->textContent) ?: null;
            }
        }
        
        return null;
    }
    
    /**
     * Verifica se um campo pertence a um grupo repetitivo específico
     */
    private function belongsToRepeatGroup(string $fieldNodeset, string $groupPath): bool
    {
        return str_starts_with($fieldNodeset, $groupPath . '/');
    }
    
    /**
     * Verifica se um campo é filho de qualquer grupo repetitivo
     */
    private function isChildOfRepeatGroup(string $nodeset, array $allFields): bool
    {
        foreach ($allFields as $field) {
            if ($this->isRepeatField($field['nodeset']) && 
                $this->belongsToRepeatGroup($nodeset, $this->getRepeatGroupPath($field['nodeset']))) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Verifica se um campo pertence a um grupo normal (não repetitivo)
     */
    private function isNormalGroupField(string $nodeset): bool
    {
        // Busca por elementos <group> no body (não repeat)
        $groupNodes = $this->xpath()->query('//x:group[@ref]');
        if ($groupNodes === false) {
            return false;
        }
        
        foreach ($groupNodes as $groupNode) {
            $groupRef = $groupNode->getAttribute('ref');
            // Verifica se não é um repeat group
            if (!$this->isRepeatGroupRef($groupRef) && str_starts_with($nodeset, $groupRef . '/')) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtém o caminho do grupo normal que contém o campo
     */
    private function getNormalGroupPath(string $nodeset): string
    {
        $groupNodes = $this->xpath()->query('//x:group[@ref]');
        if ($groupNodes === false) {
            return '';
        }
        
        foreach ($groupNodes as $groupNode) {
            $groupRef = $groupNode->getAttribute('ref');
            // Verifica se não é um repeat group e se o campo pertence a este grupo
            if (!$this->isRepeatGroupRef($groupRef) && str_starts_with($nodeset, $groupRef . '/')) {
                return $groupRef;
            }
        }
        
        return '';
    }
    
    /**
     * Verifica se um grupo é um repeat group
     */
    private function isRepeatGroupRef(string $groupRef): bool
    {
        $repeatNodes = $this->xpath()->query('//x:repeat[@nodeset="' . $groupRef . '"]');
        return $repeatNodes !== false && $repeatNodes->length > 0;
    }
    
    /**
     * Mapeia tipos de campo XForm para tipos mais amigáveis, incluindo detecção de select1/select
     * 
     * Primeiro verifica se o campo é um elemento de seleção (select1/select) através do nodeset.
     * Se não for, aplica mapeamento padrão de tipos XForm para tipos user-friendly.
     * 
     * @param string|null $xformType Tipo do bind XForm (string, int, decimal, etc.)
     * @param string $nodeset Nodeset do campo para detecção de elementos select
     * @return string Tipo amigável mapeado (text, integer, select1, select, etc.)
     */
    private function mapFieldType(?string $xformType, string $nodeset = ''): string
    {
        // Primeiro verifica se é um campo de seleção
        $selectType = $this->getSelectElementType($nodeset);
        if ($selectType !== null) {
            return $selectType;
        }
        
        if ($xformType === null) {
            return 'text';
        }
        
        $typeMap = [
            'string' => 'text',
            'int' => 'integer',
            'decimal' => 'decimal',
            'binary' => 'file',
            'geopoint' => 'geopoint',
            'geotrace' => 'geotrace', 
            'geoshape' => 'geoshape',
            'barcode' => 'barcode',
            'date' => 'date',
            'time' => 'time',
            'dateTime' => 'dateTime'
        ];
        
        return $typeMap[$xformType] ?? 'text';
    }
    
    /**
     * Determina se um campo é select1 ou select baseado no elemento do body
     * 
     * @return string|null Retorna 'select1', 'select' ou null se não for um campo de seleção
     */
    private function getSelectElementType(string $nodeset): ?string
    {
        if ($nodeset === '') {
            return null;
        }
        
        // Busca por elementos select1 e select no body
        $queries = [
            '//h:body//*[self::x:select1][@ref="' . $nodeset . '"]',   // Namespace prefixado
            '//h:body//*[self::select1][@ref="' . $nodeset . '"]',      // Namespace padrão
            '//h:body//*[self::x:select][@ref="' . $nodeset . '"]',     // Namespace prefixado
            '//h:body//*[self::select][@ref="' . $nodeset . '"]'        // Namespace padrão
        ];
        
        foreach ($queries as $query) {
            $nodes = $this->xpath()->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                $tagName = $nodes->item(0)->localName;
                return $tagName; // Retorna 'select1' ou 'select'
            }
        }
        
        return null;
    }
    
    /**
     * Extrai todas as instâncias secundárias do modelo
     * 
     * @return array<string, array<int, array{name: string, label: string}>> Array indexado por ID da instância
     */
    private function getSecondaryInstances(): array
    {
        $instances = [];
        
        // Busca por instâncias secundárias (com ID)
        $queries = [
            '//x:model/x:instance[@id]',  // Namespace prefixado
            '//model/instance[@id]'       // Namespace padrão
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
            $instanceId = $node->getAttribute('id');
            if ($instanceId === '') {
                continue;
            }
            
            $instances[$instanceId] = [];
            
            // Busca por itens dentro da instância
            $itemQueries = ['.//item', './/x:item'];
            
            foreach ($itemQueries as $itemQuery) {
                $itemNodes = $this->xpath()->query($itemQuery, $node);
                if ($itemNodes !== false && $itemNodes->length > 0) {
                    foreach ($itemNodes as $itemNode) {
                        $nameQueries = ['.//name', './/x:name'];
                        $labelQueries = ['.//label', './/x:label'];
                        
                        $name = '';
                        $label = '';
                        
                        // Extrai name
                        foreach ($nameQueries as $nameQuery) {
                            $nameNodes = $this->xpath()->query($nameQuery, $itemNode);
                            if ($nameNodes !== false && $nameNodes->length > 0) {
                                $name = trim($nameNodes->item(0)->textContent);
                                break;
                            }
                        }
                        
                        // Extrai label
                        foreach ($labelQueries as $labelQuery) {
                            $labelNodes = $this->xpath()->query($labelQuery, $itemNode);
                            if ($labelNodes !== false && $labelNodes->length > 0) {
                                $label = trim($labelNodes->item(0)->textContent);
                                break;
                            }
                        }
                        
                        if ($name !== '') {
                            $instances[$instanceId][] = [
                                'value' => $name,
                                'label' => $label
                            ];
                        }
                    }
                    break;
                }
            }
        }
        
        return $instances;
    }
    
    /**
     * Extrai choices estáticas de um elemento select (item/label/value)
     * 
     * @return array<int, array{value: string, label: string}> Array de choices
     */
    private function getStaticChoices(string $nodeset): array
    {
        $choices = [];
        
        // Busca o elemento select/select1 correspondente
        $queries = [
            '//h:body//*[self::x:select1 or self::x:select][@ref="' . $nodeset . '"]',
            '//h:body//*[self::select1 or self::select][@ref="' . $nodeset . '"]'
        ];
        
        $selectNode = null;
        foreach ($queries as $query) {
            $nodes = $this->xpath()->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                $selectNode = $nodes->item(0);
                break;
            }
        }
        
        if ($selectNode === null) {
            return [];
        }
        
        // Busca por elementos <item> dentro do select
        $itemQueries = ['.//x:item', './/item'];
        
        foreach ($itemQueries as $itemQuery) {
            $itemNodes = $this->xpath()->query($itemQuery, $selectNode);
            if ($itemNodes !== false && $itemNodes->length > 0) {
                foreach ($itemNodes as $itemNode) {
                    $labelQueries = ['.//x:label', './/label'];
                    $valueQueries = ['.//x:value', './/value'];
                    
                    $label = '';
                    $value = '';
                    
                    // Extrai label
                    foreach ($labelQueries as $labelQuery) {
                        $labelNodes = $this->xpath()->query($labelQuery, $itemNode);
                        if ($labelNodes !== false && $labelNodes->length > 0) {
                            $label = trim($labelNodes->item(0)->textContent);
                            break;
                        }
                    }
                    
                    // Extrai value
                    foreach ($valueQueries as $valueQuery) {
                        $valueNodes = $this->xpath()->query($valueQuery, $itemNode);
                        if ($valueNodes !== false && $valueNodes->length > 0) {
                            $value = trim($valueNodes->item(0)->textContent);
                            break;
                        }
                    }
                    
                    if ($value !== '') {
                        $choices[] = [
                            'value' => $value,
                            'label' => $label
                        ];
                    }
                }
                break;
            }
        }
        
        return $choices;
    }
    
    /**
     * Extrai choices dinâmicas de um elemento que usa itemset
     * 
     * @return array<int, array{value: string, label: string}> Array de choices
     */
    private function getDynamicChoices(string $nodeset): array
    {
        // Busca o elemento select/select1 correspondente
        $queries = [
            '//h:body//*[self::x:select1 or self::x:select][@ref="' . $nodeset . '"]',
            '//h:body//*[self::select1 or self::select][@ref="' . $nodeset . '"]'
        ];
        
        $selectNode = null;
        foreach ($queries as $query) {
            $nodes = $this->xpath()->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                $selectNode = $nodes->item(0);
                break;
            }
        }
        
        if ($selectNode === null) {
            return [];
        }
        
        // Busca por elemento <itemset>
        $itemsetQueries = ['.//x:itemset', './/itemset'];
        
        $itemsetNode = null;
        foreach ($itemsetQueries as $itemsetQuery) {
            $itemsetNodes = $this->xpath()->query($itemsetQuery, $selectNode);
            if ($itemsetNodes !== false && $itemsetNodes->length > 0) {
                $itemsetNode = $itemsetNodes->item(0);
                break;
            }
        }
        
        if ($itemsetNode === null) {
            return [];
        }
        
        // Extrai o nodeset do itemset para identificar a instância
        $itemsetNodeset = $itemsetNode->getAttribute('nodeset');
        if ($itemsetNodeset === '') {
            return [];
        }
        
        // Parse do nodeset para extrair o ID da instância
        // Formato: instance('instanceId')/root/item
        if (preg_match("/instance\('([^']+)'\)/", $itemsetNodeset, $matches)) {
            $instanceId = $matches[1];
            $instances = $this->getSecondaryInstances();
            
            if (isset($instances[$instanceId])) {
                return $instances[$instanceId];
            }
        }
        
        return [];
    }
    
    /**
     * Obtém choices para um campo específico (static ou dynamic)
     * 
     * @return array<int, array{value: string, label: string}>|null Array de choices ou null se não for um campo de escolha
     */
    private function getChoicesForField(string $nodeset): ?array
    {
        // Primeiro tenta choices estáticas
        $staticChoices = $this->getStaticChoices($nodeset);
        if (!empty($staticChoices)) {
            return $staticChoices;
        }
        
        // Se não encontrou estáticas, tenta dinâmicas
        $dynamicChoices = $this->getDynamicChoices($nodeset);
        if (!empty($dynamicChoices)) {
            return $dynamicChoices;
        }
        
        // Se não é um campo de choice, retorna null
        return null;
    }
    
    /**
     * Extrai todas as traduções da seção itext do XForm
     * 
     * O itext é o sistema de internacionalização do XForm que permite definir textos
     * em múltiplas línguas. Este método extrai a tradução padrão ou primeira disponível.
     * Suporta tanto elementos simples quanto elementos com imagens (ignora form="image").
     * 
     * @return array<string, string> Array com ID do texto como chave e valor traduzido
     * 
     * @example
     * // Retorna: ['/path/field:label' => 'Nome do Campo', ...]
     */
    private function getItextTranslations(): array
    {
        $translations = [];
        
        // Busca pela seção itext no modelo
        $queries = [
            '//x:model/x:itext',  // Namespace prefixado
            '//model/itext'       // Namespace padrão
        ];
        
        $itextNode = null;
        foreach ($queries as $query) {
            $nodes = $this->xpath()->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                $itextNode = $nodes->item(0);
                break;
            }
        }
        
        if ($itextNode === null) {
            return [];
        }
        
        // Busca pela tradução padrão ou primeira tradução
        $translationQueries = [
            './/x:translation[@default="true()"]',  // Tradução padrão prefixada
            './/translation[@default="true()"]',    // Tradução padrão não prefixada
            './/x:translation[1]',                   // Primeira tradução prefixada
            './/translation[1]'                      // Primeira tradução não prefixada
        ];
        
        $translationNode = null;
        foreach ($translationQueries as $translationQuery) {
            $translationNodes = $this->xpath()->query($translationQuery, $itextNode);
            if ($translationNodes !== false && $translationNodes->length > 0) {
                $translationNode = $translationNodes->item(0);
                break;
            }
        }
        
        if ($translationNode === null) {
            return [];
        }
        
        // Extrai todos os textos da tradução
        $textQueries = ['.//x:text[@id]', './/text[@id]'];
        
        foreach ($textQueries as $textQuery) {
            $textNodes = $this->xpath()->query($textQuery, $translationNode);
            if ($textNodes !== false && $textNodes->length > 0) {
                foreach ($textNodes as $textNode) {
                    $id = $textNode->getAttribute('id');
                    if ($id === '') {
                        continue;
                    }
                    
                    // Busca por elemento value (sem form="image")
                    $valueQueries = ['.//x:value[not(@form="image")]', './/value[not(@form="image")]'];
                    
                    foreach ($valueQueries as $valueQuery) {
                        $valueNodes = $this->xpath()->query($valueQuery, $textNode);
                        if ($valueNodes !== false && $valueNodes->length > 0) {
                            $value = trim($valueNodes->item(0)->textContent);
                            if ($value !== '') {
                                $translations[$id] = $value;
                                break;
                            }
                        }
                    }
                }
                break;
            }
        }
        
        return $translations;
    }
    
    /**
     * Resolve uma referência itext para seu valor de texto
     * 
     * O itext permite referenciar textos através de expressões como jr:itext('/path:label').
     * Este método faz o parse da referência e busca o valor correspondente nas traduções.
     * 
     * @param string $itextRef Referência no formato "jr:itext('/path:label')"
     * @param array<string, string> $translations Array de traduções disponíveis
     * @return string|null Texto resolvido ou null se não encontrado
     * 
     * @example
     * // $itextRef = "jr:itext('/PEIXES/uc:label')"
     * // $translations = ['/PEIXES/uc:label' => 'Unidade de Conservação']
     * // Retorna: 'Unidade de Conservação'
     */
    private function resolveItextReference(string $itextRef, array $translations): ?string
    {
        // Parse da referência itext
        // Formato: jr:itext('/PEIXES_IGARAPERIACHO_01SET25/uc:label')
        if (preg_match("/jr:itext\('([^']+)'\)/", $itextRef, $matches)) {
            $itextId = $matches[1];
            return $translations[$itextId] ?? null;
        }
        
        // Formato: jr:itext(variable) - mais complexo, não implementado ainda
        return null;
    }
}