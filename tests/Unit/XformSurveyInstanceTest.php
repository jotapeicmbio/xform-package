<?php

namespace Icmbio\Xform\Tests\Unit;

use Icmbio\Xform\Instance;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class XformSurveyInstanceTest extends TestCase
{
    #[Test]
    public function shouldReturnTrueWhenXformHasId(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_medias');
        $this->assertIsString($xform);
        $this->assertTrue((new Instance($xform))->hasId());
    }

    #[Test]
    public function shouldReturnStringWhenXformHasId(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_medias');
        $this->assertIsString($xform);
        $this->assertEquals('xform_medias', (new Instance($xform))->getId());
    }

    #[Test]
    public function shouldReturnTrueWhenXformHasVersion(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_medias');
        $this->assertIsString($xform);
        $this->assertTrue((new Instance($xform))->hasVersion());
    }

    #[Test]
    public function shouldReturnStringWhenXformHasVersion(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_medias');
        $this->assertIsString($xform);
        $this->assertEquals('20250930', (new Instance($xform))->getVersion());
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

        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_nested');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Instance($xform))->getNodeset());
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

        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_nested');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Instance($xform))->shortGetNodeset());
    }

    #[Test]
    public function shouldReturnSurveyWithNameAndLabelWithoutGroup(): void
    {
        $expected = [
            [
                'name' => 'store_gps',
                'label' => 'Collect the GPS coordinates of this store.',
            ],

            [
                'name' => 'pipe',
                'label' => 'Pipeline',
            ],

            [
                'name' => 'border',
                'label' => 'Border',
            ]
        ];

        
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_geopoint');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Instance($xform))->getSimpleInfoSurvey());
    }

    #[Test]
    public function shouldReturnSurveyWithNameAndLabelWithGroup(): void
    {
        $expected = [
            [
                'name' => 'first_name',
                'label' => 'First You name',
            ],

            [
                'name' => 'second_name',
                'label' => 'Second You name',
            ],

            [
                'name' => 'thrid_name',
                'label' => 'Thrid You name',
            ]
        ];

        
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_group_repeat_nested');
        $this->assertIsString($xform);
        $this->assertEquals($expected, (new Instance($xform))->getSimpleInfoSurvey());
    }

    #[Test]
    public function shouldReturnCollectionDateFromGroupXform(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xform_campo_data_grupo');
        $this->assertIsString($xform);
        $this->assertTrue((new Instance($xform))->hasCollectionDate());
        $this->assertEquals(
            '/xform_campo_data_grupo/grupo/data',
            (new Instance($xform))->getCollectionDate()
        );
    }

    #[Test]
    public function shouldReturnCollectionDateFromRootXform(): void
    {
        $xform = file_get_contents(__DIR__ . '/../xforms/xfrm_campo_data_raiz');
        $this->assertIsString($xform);
        $this->assertTrue((new Instance($xform))->hasCollectionDate());
        $this->assertEquals(
            '/xfrm_campo_data_raiz/data',
            (new Instance($xform))->getCollectionDate()
        );
    }
}
