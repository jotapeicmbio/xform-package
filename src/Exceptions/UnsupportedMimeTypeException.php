<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção para tipos MIME não suportados
 */
class UnsupportedMimeTypeException extends XformAttachmentException
{
    /**
     * Exceção para tipo MIME inválido/não suportado
     * 
     * @param array<string> $allowedTypes
     */
    public static function invalidType(string $mimeType, string $filename, array $allowedTypes): self
    {
        return new self(
            "File '{$filename}' has unsupported MIME type '{$mimeType}'",
            4002,
            null,
            [
                'mime_type' => $mimeType,
                'filename' => $filename,
                'allowed_types' => $allowedTypes
            ]
        );
    }
}