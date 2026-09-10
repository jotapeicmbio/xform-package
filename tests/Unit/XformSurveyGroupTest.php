<?php

namespace Icmbio\Xform\Tests\Unit;

use Icmbio\Xform\Xform;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XformSurveyGroupTest extends TestCase
{
    #[Test]
    public function shouldReturnFalseAndEmptyArraysWhenXformHasNoGroup(): void
    {
        $xform = new Xform($this->loadXform('xform_geopoint'));

        $this->assertFalse($xform->hasGroup());
        $this->assertSame([], $xform->getGroups());
        $this->assertSame([], $xform->getWithoutRepeatGroups());
    }

    #[Test]
    public function shouldReturnSimpleGroupReferences(): void
    {
        $xform = new Xform($this->loadXform('xform_campo_data_grupo'));

        $this->assertTrue($xform->hasGroup());
        $this->assertSame(
            ['/xform_campo_data_grupo/grupo'],
            $xform->getGroups()
        );
        $this->assertSame(
            [
                '/xform_campo_data_grupo/grupo',
                '/xform_campo_data_grupo/grupo/data',
            ],
            $xform->getWithoutRepeatGroups()
        );
    }

    #[Test]
    public function shouldReturnNestedGroupReferences(): void
    {
        $xform = new Xform($this->loadXform('xform_group_repeat_nested'));

        $this->assertSame(
            [
                '/xlsform_group_repeat_nested/first_group',
                '/xlsform_group_repeat_nested/first_group/second_group',
                '/xlsform_group_repeat_nested/first_group/second_group/third_group',
            ],
            $xform->getGroups()
        );
        $this->assertSame(
            [
                '/xlsform_group_repeat_nested/first_group',
                '/xlsform_group_repeat_nested/first_group/first_name',
                '/xlsform_group_repeat_nested/first_group/second_group',
                '/xlsform_group_repeat_nested/first_group/second_group/second_name',
                '/xlsform_group_repeat_nested/first_group/second_group/third_group',
                '/xlsform_group_repeat_nested/first_group/second_group/third_group/thrid_name',
            ],
            $xform->getWithoutRepeatGroups()
        );
    }

    #[Test]
    public function shouldReturnItemsFromGroupNodeset(): void
    {
        $xform = new Xform($this->loadXform('xform_medias'));

        $this->assertSame(
            [
                '/xform_medias/medias/name_image',
                '/xform_medias/medias/name_audio',
                '/xform_medias/medias/name_video',
                '/xform_medias/medias/name_file',
            ],
            $xform->getGroupItems('/xform_medias/medias')
        );
    }

    private function loadXform(string $filename): string
    {
        $content = file_get_contents(__DIR__ . '/../xforms/' . $filename);

        $this->assertIsString($content);

        return $content;
    }
}
