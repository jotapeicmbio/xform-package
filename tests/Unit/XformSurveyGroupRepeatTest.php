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
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_geopoint');
        $this->assertIsString($xform);
        $this->assertFalse((new Xform($xform))->hasGroupRepeat());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasGroupRepeat(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_simple');
        $this->assertIsString($xform);
        $this->assertTrue((new Xform($xform))->hasGroupRepeat());
    }

    #[Test]
    public function shouldReturnArrayWhenXformHasGroupRepeat(): void
    {
        $expected = [
            '/xlsform_group_repeat_simple/first_group',
        ];

        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_simple');
        $this->assertIsString($xform);
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

        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_nested');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Xform($xform))->getGroupRepeats());
    }

    #[Test]
    public function shouldReturnArrayWhenXformHasGroupRepeatWithUuid(): void
    {
        $expected = [
            '/xlsform_group_repeat_simple/first_group/uuid',
        ];

        
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_simple');
        $this->assertIsString($xform);
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

        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_nested');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Xform($xform, 'group'))->getGroupRepeatsWithUuid());
    }

    #[Test]
    public function shouldReturnArraWithNamesXform(): void
    {
        $expected = [
            '/xlsform_group_repeat_nested/first_group',
            '/xlsform_group_repeat_nested/first_group/first_name',
            '/xlsform_group_repeat_nested/first_group/second_group',
            '/xlsform_group_repeat_nested/first_group/second_group/second_name',
            '/xlsform_group_repeat_nested/first_group/second_group/third_group',
            '/xlsform_group_repeat_nested/first_group/second_group/third_group/thrid_name',
        ];

        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_nested');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Xform($xform))->getNamesXform());
    }
}
