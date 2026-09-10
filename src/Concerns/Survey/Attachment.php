<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns\Survey;

trait Attachment
{
    /**
     * Verifica se o formulário possui campos de attachment
     */
    public function hasAttachments(): bool
    {
        $result = $this->xpath()->evaluate('boolean(//x:bind[@type="binary"])');
        return $result === true;
    }

    /**
     * Retorna array de nodesets dos campos de attachment
     * 
     * @return array<string> Array com nodesets dos attachments
     */
    public function getAttachments(): array
    {
        $nodes = $this->xpath()->query('//x:bind[@type="binary"]/@nodeset');
        if ($nodes === false) {
            return [];
        }
        
        return array_map(
            fn($n) => (string) $n->nodeValue, 
            iterator_to_array($nodes)
        );
    }
}
