<?php
namespace Icmbio\Xform;

use DOMDocument;
use DOMXPath;

class Xform
{
    use Concerns\SurveyInstance,
        Concerns\SurveyGeo,
        Concerns\SurveyAttachment,
        Concerns\SurveyGroupRepeat;

    private DOMDocument $domDocument;
    protected ?string $group_repeat = null;

    public function __construct(string $content, ?string $group_repeat = null)
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $this->domDocument = $this->loadToDom($content);
        $this->group_repeat = $group_repeat;
    }

    public static function make(string $content): static
    {
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
        return preg_replace('#^/?[^/]+/#', '', $xpath);
    }

    public function __call($name, $arguments)
    {
        if (str_contains($name, 'short')) {
            $method = str_replace('short', '', $name);
            if (method_exists($this, $method)) {
                $result = $this->$method();
                return array_map(fn($n) => $this->abbreviatedXpath($n), $result);
            }
        }
    }
}

