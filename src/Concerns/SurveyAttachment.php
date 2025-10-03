<?php

namespace Icmbio\Xform\Concerns;

trait SurveyAttachment
{
    public function hasAttachments(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="binary"])');
    }

    public function getAttachments(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="binary"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }
}