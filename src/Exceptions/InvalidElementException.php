<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção para elementos com estrutura inválida
 */
class InvalidElementException extends XformValidationException
{
    /**
     * Exceção para estrutura de elemento inválida
     * 
     * @param array<string> $expectedAttributes
     */
    public static function invalidStructure(string $elementName, array $expectedAttributes): self
    {
        return new self(
            "Element '{$elementName}' has invalid structure",
            3002,
            null,
            [
                'element' => $elementName,
                'expected_attributes' => $expectedAttributes
            ]
        );
    }

    /**
     * Exceção para atributo não suportado
     */
    public static function unsupportedAttribute(string $elementName, string $attribute): self
    {
        return new self(
            "Element '{$elementName}' contains unsupported attribute '{$attribute}'",
            3003,
            null,
            [
                'element' => $elementName,
                'attribute' => $attribute
            ]
        );
    }
}