<?php

namespace Icmbio\Xform\Concerns;

trait SurveyInstance
{
    public function hasId(): bool
    {
        return $this->xpath()->evaluate('boolean(//*[@id][parent::x:instance])');
    }

    public function getId(): ?string
    {
        return $this->hasId()
            ? $this->xpath()->evaluate('string(//*[@id][parent::x:instance]/@id)')
            : null;
    }

    public function hasVersion(): bool
    {
        return $this->xpath()->evaluate('boolean(//*[@version][parent::x:instance])');
    }

    public function getVersion(): ?string
    {
        $version = $this->xpath()->evaluate('string(//*[@version][parent::x:instance]/@version)');
        return $version !== '' ? $version : null;
    }

    public function getNodeset(): ?array
    {
        $nodes = $this->xpath()->query('//x:bind/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    public function getSimpleInfoSurvey(): ?array
    {
        $fields = [];

        $nodes = $this->xpath()->query(
            '//h:body//*[self::x:input or self::x:select1 or self::x:select or self::x:textarea]'
        );

        foreach ($nodes as $node) {
            $ref = $node->getAttribute('ref');
            $name = basename($ref);
            
            $labelNode = $this->xpath()->query('.//x:label', $node)->item(0);
            $label = $labelNode ? trim($labelNode->textContent) : null;

            $fields[] = [
                'name' => $name,
                'label' => $label,
            ];
        }

        return $fields;
    }
}