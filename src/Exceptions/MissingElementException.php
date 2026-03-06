<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção para elementos obrigatórios ausentes no XForm
 */
class MissingElementException extends XformValidationException
{
    /**
     * Exceção para elemento obrigatório ausente
     */
    public static function requiredElement(string $elementName): self
    {
        return new self(
            "Required element '{$elementName}' is missing from XForm",
            3001,
            null,
            ['element' => $elementName]
        );
    }
}