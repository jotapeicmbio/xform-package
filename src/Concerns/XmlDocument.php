<?php

declare(strict_types=1);

namespace Icmbio\Xform\Concerns;

use DOMDocument;
use DOMXPath;

/**
 * Compartilha a infraestrutura básica para classes que trabalham com XML.
 */
trait XmlDocument
{
    protected DOMDocument $domDocument;

    /**
     * Inicializa o DOM a partir do conteúdo XML.
     *
     * Este método também pode ser chamado por classes que possuem um
     * construtor próprio, como Xform.
     */
    protected function boot(string $content): void
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        if ($content === null) {
            throw new \InvalidArgumentException('Invalid content provided to XML document');
        }

        $this->domDocument = $this->loadToDom($content);
    }

    /**
     * Cria uma nova instância a partir de um XML.
     *
     * @return static
     */
    public static function make(string $content): static
    {
        return new static($content);
    }

    /**
     * Carrega o conteúdo XML em um DOMDocument.
     */
    protected function loadToDom(string $content): DOMDocument
    {
        $document = new DOMDocument();

        libxml_use_internal_errors(true);
        $document->loadXML($content, LIBXML_NOBLANKS | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        return $document;
    }

    /**
     * Cria um XPath associado ao documento.
     */
    protected function xpath(): DOMXPath
    {
        $xpath = new DOMXPath($this->domDocument);
        $xpath->registerNamespace('x', 'http://www.w3.org/2002/xforms');
        $xpath->registerNamespace('h', 'http://www.w3.org/1999/xhtml');

        return $xpath;
    }

    /**
     * Remove o primeiro segmento de um XPath absoluto ou relativo.
     */
    public function abbreviatedXpath(string $xpath): string
    {
        $result = preg_replace('#^/?[^/]+/#', '', $xpath);

        return $result !== null ? $result : $xpath;
    }

    /**
     * Resolve chamadas no formato shortNomeMetodo().
     *
     * @param string $name Nome do método chamado
     * @param array<int, mixed> $arguments Argumentos recebidos
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (str_contains($name, 'short')) {
            $method = str_replace('short', '', $name);
            $method = lcfirst($method);

            if (method_exists($this, $method)) {
                $result = $this->$method();

                return array_map(
                    fn($value) => $this->abbreviatedXpath($value),
                    $result
                );
            }
        }

        throw new \BadMethodCallException("Method {$name} does not exist");
    }
}
