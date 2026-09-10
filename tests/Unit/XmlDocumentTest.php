<?php

declare(strict_types=1);

namespace Icmbio\Xform\Tests\Unit;

use Icmbio\Xform\Concerns\XmlDocument;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XmlDocumentTest extends TestCase
{
    #[Test]
    public function shouldCreateDocumentFromXmlWithBom(): void
    {
        $document = new class ("\xEF\xBB\xBF<root><item/></root>") {
            use XmlDocument;

            public function __construct(string $content)
            {
                $this->boot($content);
            }
        };

        $this->assertContains(XmlDocument::class, class_uses($document));
    }

    #[Test]
    public function shouldRegisterNamespacesForXpathQueries(): void
    {
        $document = new class ('<h:html xmlns:h="http://www.w3.org/1999/xhtml"><h:body/></h:html>') {
            use XmlDocument;

            public function __construct(string $content)
            {
                $this->boot($content);
            }

            public function hasBody(): bool
            {
                return $this->xpath()->evaluate('boolean(//h:body)') === true;
            }
        };

        $this->assertTrue($document->hasBody());
    }

    #[Test]
    public function shouldAbbreviateXpath(): void
    {
        $document = new class ('<root/>') {
            use XmlDocument;

            public function __construct(string $content)
            {
                $this->boot($content);
            }
        };

        $this->assertSame('group/field', $document->abbreviatedXpath('/survey/group/field'));
        $this->assertSame('field', $document->abbreviatedXpath('survey/field'));
        $this->assertSame('/field', $document->abbreviatedXpath('/field'));
    }

    #[Test]
    public function shouldResolveShortMethodsFromChildClasses(): void
    {
        $document = new class ('<root/>') {
            use XmlDocument;

            public function __construct(string $content)
            {
                $this->boot($content);
            }

            /**
             * @return array<string>
             */
            public function getPaths(): array
            {
                return ['/survey/group', '/survey/group/field'];
            }
        };

        $this->assertSame(
            ['group', 'group/field'],
            $document->shortGetPaths()
        );
    }
}
