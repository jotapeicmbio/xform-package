<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção para attachment não encontrado
 */
class AttachmentNotFoundException extends XformAttachmentException
{
    /**
     * Exceção para arquivo de attachment não encontrado
     */
    public static function missingFile(string $filename, string $nodeset): self
    {
        return new self(
            "Attachment file '{$filename}' not found",
            4001,
            null,
            [
                'filename' => $filename,
                'nodeset' => $nodeset
            ]
        );
    }
}