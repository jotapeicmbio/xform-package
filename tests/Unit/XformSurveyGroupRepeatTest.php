<?php

namespace Icmbio\Xform\Tests\Unit;

use Icmbio\Xform\Xform;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XformSurveyGroupRepeatTest extends TestCase
{
     #[Test]
    public function shouldReturnFalseWhenXformNotHasGroupRepeat(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_geopoint');
        $this->assertFalse((new Xform($xform))->hasGroupRepeat());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasGroupRepeat(): void
    {
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_group_repeat_simple');
        $this->assertTrue((new Xform($xform))->hasGroupRepeat());
    }

    #[Test]
    public function shouldReturnArrayWhenXformHasGroupRepeat(): void
    {
        $expected = [
            '/xlsform_group_repeat_simple/first_group',
        ];

        $xform = file_get_contents('/var/www/html/tests/xforms/xform_group_repeat_simple');
        $this->assertEquals($expected, (new Xform($xform))->getGroupRepeats());
    }

    #[Test]
    public function shouldReturnArrayWhenXformHasGroupRepeatNested(): void
    {
        $expected = [
            '/xlsform_group_repeat_nested/first_group',
            '/xlsform_group_repeat_nested/first_group/second_group',
            '/xlsform_group_repeat_nested/first_group/second_group/third_group',
        ];

        $xform = file_get_contents('/var/www/html/tests/xforms/xform_group_repeat_nested');
        $this->assertEquals($expected, (new Xform($xform))->getGroupRepeats());
    }

    #[Test]
    public function shouldReturnArrayWhenXformHasGroupRepeatWithUuid(): void
    {
        $expected = [
            '/xlsform_group_repeat_simple/first_group/uuid',
        ];

        
        $xform = file_get_contents('/var/www/html/tests/xforms/xform_group_repeat_simple');
        $this->assertEquals($expected, (new Xform($xform, 'group'))->getGroupRepeatsWithUuid());
    }


    #[Test]
    public function shouldReturnArrayWhenXformHasGroupRepeatNestedWithUuid(): void
    {
       $expected = [
            '/xlsform_group_repeat_nested/first_group/first_uuid',
            '/xlsform_group_repeat_nested/first_group/second_group/second_uuid',
            '/xlsform_group_repeat_nested/first_group/second_group/third_group/thrid_uuid',
        ];

        $xform = file_get_contents('/var/www/html/tests/xforms/xform_group_repeat_nested');
        $this->assertEquals($expected, (new Xform($xform, 'group'))->getGroupRepeatsWithUuid());
    }
}