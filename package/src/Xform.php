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

    public function __construct(string $content)
    {
        $this->domDocument = $this->loadToDom($content);
    }

    public static function make(string $content): static
    {
        return new static($content);
    }

    protected function loadToDom(string $content): DOMDocument
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadXML($content);
        libxml_clear_errors();

        return $document;
    }

    protected function xpath(): DOMXPath
    {
        $xpath = new DOMXPath($this->domDocument);
        $xpath->registerNamespace('x', 'http://www.w3.org/2002/xforms');
        return $xpath;
    }
}

