<?php

declare(strict_types=1);

namespace Icmbio\Xform;

use DOMDocument;
use DOMXPath;

/**
 * Biblioteca PHP para processamento XForm
 * 
 * @method array<string> shortGetNodeset() Retorna nomes dos campos abreviados
 * @method array<string> shortGetAttachments() Retorna anexos com nomes abreviados
 * @method array<string> shortGetGeopoints() Retorna geopoints com nomes abreviados
 * @method array<string> shortGetGeotraces() Retorna geotraces com nomes abreviados  
 * @method array<string> shortGetGeoshapes() Retorna geoshapes com nomes abreviados
 * @method array<string> shortGetGroupRepeats() Retorna grupos repetitivos com nomes abreviados
 * @method array<string> shortGetGroupRepeatsWithUuid() Retorna grupos com UUID abreviados
 * @method array<string> shortGetNamesXform() Retorna nomes do Xform abreviados
 */
class Xform
{
    use Concerns\SurveyInstance,
        Concerns\SurveyGeo,
        Concerns\SurveyAttachment,
        Concerns\SurveyGroupRepeat;

    private DOMDocument $domDocument;
    protected ?string $group_repeat = null;
    protected ?string $group_repeat_plural = null;

    public function __construct(string $content, ?string $group_repeat = null, ?string $group_repeat_plural = null)
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        // Ensure content is valid string after BOM removal
        if ($content === null) {
            throw new \InvalidArgumentException('Invalid content provided to Xform constructor');
        }
        $this->domDocument = $this->loadToDom($content);
        $this->group_repeat = $group_repeat;
        $this->group_repeat_plural = $group_repeat_plural;
    }

    /**
     * Create new instance
     * 
     * @return static
     */
    public static function make(string $content): static
    {
        // @phpstan-ignore-next-line new.static
        return new static($content);
    }

    protected function loadToDom(string $content): DOMDocument
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadXML($content, LIBXML_NOBLANKS | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        return $document;
    }

    protected function xpath(): DOMXPath
    {
        $xpath = new DOMXPath($this->domDocument);
        $xpath->registerNamespace('x', 'http://www.w3.org/2002/xforms');
        $xpath->registerNamespace('h', 'http://www.w3.org/1999/xhtml');
        return $xpath;
    }

    public function abbreviatedXpath(string $xpath): string
    {
        $result = preg_replace('#^/?[^/]+/#', '', $xpath);
        return $result !== null ? $result : $xpath;
    }

    /**
     * Magic method to handle short methods
     * 
     * @param string $name Method name
     * @param array<int, mixed> $arguments Method arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (str_contains($name, 'short')) {
            $method = str_replace('short', '', $name);
            $method = lcfirst($method); // Ensure first letter is lowercase
            if (method_exists($this, $method)) {
                $result = $this->$method();
                return array_map(fn($n) => $this->abbreviatedXpath($n), $result);
            }
        }
        
        throw new \BadMethodCallException("Method {$name} does not exist");
    }
}

