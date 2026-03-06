<?php

declare(strict_types=1);

namespace Icmbio\Xform\Exceptions;

use Exception;
use Throwable;

/**
 * Exceção base para todas as exceções da biblioteca XForm
 */
class XformException extends Exception
{
    /**
     * @var array<string, mixed> Contextual information about the exception
     */
    protected array $context = [];

    /**
     * Constructor
     *
     * @param string $message Exception message
     * @param int $code Exception code
     * @param Throwable|null $previous Previous exception
     * @param array<string, mixed> $context Additional context information
     */
    public function __construct(
        string $message = "", 
        int $code = 0, 
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Retorna contexto adicional sobre o erro
     * 
     * @return array<string, mixed> Contexto do erro
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Adiciona contexto ao erro com interface fluente
     */
    public function withContext(string $key, mixed $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }
}