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

    #[Test]
    public function shouldReturnAllNodesetAsArray(): void
    {
        $expected = [
            '/xlsform_group_repeat_nested/first_group/first_name',
            '/xlsform_group_repeat_nested/first_group/first_uuid',
            '/xlsform_group_repeat_nested/first_group/second_group/second_name',
            '/xlsform_group_repeat_nested/first_group/second_group/second_uuid',
            '/xlsform_group_repeat_nested/first_group/second_group/third_group/thrid_name',
            '/xlsform_group_repeat_nested/first_group/second_group/third_group/thrid_uuid',
            '/xlsform_group_repeat_nested/meta/instanceID',
        ];

        $xform = file_get_contents('/var/www/html/tests/xforms/xform_group_repeat_nested');
        $this->assertEquals($expected, (new Xform($xform))->getNodeset());
    }
    
    #[Test]
    public function shouldReturnShortAllNodesetAsArray(): void
    {
        $expected = [
            'first_group/first_name',
            'first_group/first_uuid',
            'first_group/second_group/second_name',
            'first_group/second_group/second_uuid',
            'first_group/second_group/third_group/thrid_name',
            'first_group/second_group/third_group/thrid_uuid',
            'meta/instanceID',
        ];

        $xform = file_get_contents('/var/www/html/tests/xforms/xform_group_repeat_nested');
        $this->assertEquals($expected, (new Xform($xform))->shortGetNodeset());
    }
}