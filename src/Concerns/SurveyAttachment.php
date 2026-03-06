<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

trait SurveyAttachment
{
    /**
     * Verifica se o formulário possui campos de attachment
     */
    public function hasAttachments(): bool
    {
        return $this->xpath()->evaluate('boolean(//x:bind[@type="binary"])');
    }

    /**
     * Retorna array de nodesets dos campos de attachment
     * 
     * @return array<string> Array com nodesets dos attachments
     */
    public function getAttachments(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="binary"]/@nodeset');
        return array_map(fn($n) => $n->nodeValue, iterator_to_array($nodes));
    }
}