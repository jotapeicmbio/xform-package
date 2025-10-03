<?php

namespace Icmbio\Xform\Tests\Unit;

use Icmbio\Xform\Xform;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XformSurveyGeoTest extends TestCase
{
    #[Test]
    public function shouldReturnFalseWhenXformHasGeopoint(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_medias');
        $this->assertFalse((new Xform($xform))->hasGeopoint());
    }

    #[Test]
    public function shouldReturnFalseWhenXformHasGeotrace(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_medias');
        $this->assertFalse((new Xform($xform))->hasGeotrace());
    }

    #[Test]
    public function shouldReturnFalseWhenXformHasGeoshape(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_medias');
        $this->assertFalse((new Xform($xform))->hasGeoshape());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasGeopoint(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_geopoint');
        $this->assertTrue((new Xform($xform))->hasGeopoint());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasGeotrace(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_geopoint');
        $this->assertTrue((new Xform($xform))->hasGeotrace());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasGeoshape(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_geopoint');
        $this->assertTrue((new Xform($xform))->hasGeoshape());
    }

    public function shouldReturnArrayWhenXformHasGeopoint(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xlsform_geopoint');
        $this->assertEquals("['/xlsform_geopoint/geopoint']", (new Xform($xform))->getGeopoints());
    }

    public function shouldReturnArrayWhenXformHasGeotrace(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xlsform_geopoint');
        $this->assertEquals("['/xlsform_geopoint/geotrace']", (new Xform($xform))->getGeotraces());
    }

    public function shouldReturnArrayWhenXformHasGeoshape(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xlsform_geopoint');
        $this->assertEquals("['/xlsform_geopoint/geoshape']", (new Xform($xform))->getGeoshapes());
    }
}