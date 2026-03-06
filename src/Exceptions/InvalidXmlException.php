<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção específica para XML malformado
 */
class InvalidXmlException extends XformParseException
{
    /**
     * Cria exceção para XML malformado
     */
    public static function malformedXml(string $xmlContent): self
    {
        return new self(
            "XML content is malformed and cannot be parsed",
            1001,
            null,
            ['xml_snippet' => substr($xmlContent, 0, 200)]
        );
    }
}