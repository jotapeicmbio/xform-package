<?php

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
        $nodes = $this->xpath()->query("//x:repeat[contains(@nodeset, '{$this->group_repeat}')]/@nodeset");
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }

    public function getGroupRepeatsWithUuid(): array
    {
        $nodes = $this->xpath()->query("//x:bind[contains(@nodeset, 'uuid') and contains(@nodeset, '{$this->group_repeat}')]/@nodeset");
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }
}
