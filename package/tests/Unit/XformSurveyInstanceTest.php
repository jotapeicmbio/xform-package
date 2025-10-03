<?php

namespace Icmbio\Xform\Tests\Unit;

use Icmbio\Xform\Xform;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XformSurveyInstanceTest extends TestCase
{
    #[Test]
    public function shouldReturnTrueWhenXformHasId(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_medias');
        $this->assertTrue((new Xform($xform))->hasId());
    }

    #[Test]
    public function shouldReturnStringWhenXformHasId(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_medias');
        $this->assertEquals('xform_medias', (new Xform($xform))->getId());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasVersion(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_medias');
        $this->assertTrue((new Xform($xform))->hasVersion());
    }

    #[Test]
    public function shouldReturnStringWhenXformHasVersion(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_medias');
        $this->assertEquals('20250930', (new Xform($xform))->getVersion());
    }
}