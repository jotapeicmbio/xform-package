<?php

namespace Icmbio\Xform\Tests\Unit;

use Icmbio\Xform\Xform;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XformSurveyAttachmentTest extends TestCase
{
    #[Test]
    public function shouldReturnFalseWhenXformNotHasAttachment(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_geopoint');
        $this->assertIsString($xform);
        $this->assertFalse((new Xform($xform))->hasAttachments());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasAttachment(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_medias');
        $this->assertIsString($xform);
        $this->assertTrue((new Xform($xform))->hasAttachments());
    }

    #[Test]
    public function shouldReturnArrayWhenXformHasAttachment(): void
    {
        $expected = [
            '/xform_medias/medias/name_image',
            '/xform_medias/medias/name_audio',
            '/xform_medias/medias/name_video',
            '/xform_medias/medias/name_file'
        ];

        $xform = file_get_contents(__DIR__ . '/../xforms/xform_medias');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Xform($xform))->getAttachments());
    }
}
