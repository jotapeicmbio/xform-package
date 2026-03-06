<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

/**
 * Exceção para problemas de tamanho de attachment
 */
class AttachmentSizeException extends XformAttachmentException
{
    /**
     * Exceção para arquivo muito grande
     */
    public static function tooLarge(string $filename, int $actualSize, int $maxSize): self
    {
        return new self(
            "File '{$filename}' is too large ({$actualSize} bytes, max: {$maxSize} bytes)",
            4003,
            null,
            [
                'filename' => $filename,
                'actual_size' => $actualSize,
                'max_size' => $maxSize,
                'actual_size_human' => self::formatBytes($actualSize),
                'max_size_human' => self::formatBytes($maxSize)
            ]
        );
    }

    /**
     * Formata bytes em formato legível
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * (int)$pow));
        
        return number_format($bytes, 1) . ' ' . $units[(int)$pow];
    }
}