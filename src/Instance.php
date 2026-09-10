<?php

declare(strict_types=1);

namespace Icmbio\Xform;

class Instance
{
    use Concerns\XmlDocument,
        Concerns\Instance\Instance;

    public function __construct(string $content)
    {
        $this->boot($content);
    }
}
