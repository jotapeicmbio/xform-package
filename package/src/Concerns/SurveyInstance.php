<?php

namespace Icmbio\Xform\Concerns;

trait SurveyInstance
{
    public function hasId(): bool
    {
        return $this->xpath()->evaluate('boolean(//*[@id][parent::x:instance])');
    }

    public function getId(): ?string
    {
        return $this->hasId()
            ? $this->xpath()->evaluate('string(//*[@id][parent::x:instance]/@id)')
            : null;
    }

    public function hasVersion(): bool
    {
        return $this->xpath()->evaluate('boolean(//*[@version][parent::x:instance])');
    }

    public function getVersion(): ?string
    {
        $version = $this->xpath()->evaluate('string(//*[@version][parent::x:instance]/@version)');
        return $version !== '' ? $version : null;
    }
}