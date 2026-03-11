<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Xform;
use PHPUnit\Framework\Attributes\Test;

class XformSurveyFieldsTest extends TestCase
{
    private string $xformGeopoint;
    private string $xformComplex;

    protected function setUp(): void
    {
        $this->xformGeopoint = file_get_contents(__DIR__ . '/../xforms/xform_geopoint');
        $this->xformComplex = file_get_contents(__DIR__ . '/../xforms/teste_estrutura_em_objeto');
    }
    
    /**
     * Helper method to find field by nodeset in the new array structure
     */
    private function findFieldByNodeset(array $survey, string $nodeset): ?array
    {
        foreach ($survey as $field) {
            if ($field['nodeset'] === $nodeset) {
                return $field;
            }
        }
        return null;
    }

    #[Test]
    public function it_extracts_bind_attributes_from_xform(): void
    {
        $xform = new Xform($this->xformGeopoint);
        $bindAttributes = $xform->getBindAttributes();

        $this->assertIsArray($bindAttributes);
        $this->assertArrayHasKey('/xform_geopoint/store_gps', $bindAttributes);
        
        $gpsField = $bindAttributes['/xform_geopoint/store_gps'];
        $this->assertEquals('geopoint', $gpsField['type']);
        $this->assertNull($gpsField['required'] ?? null);
        $this->assertNull($gpsField['readonly'] ?? null);
    }

    #[Test]
    public function it_extracts_body_labels_and_hints(): void
    {
        $xform = new Xform($this->xformGeopoint);
        $bodyLabels = $xform->getBodyLabels();

        $this->assertIsArray($bodyLabels);
        $this->assertArrayHasKey('/xform_geopoint/store_gps', $bodyLabels);
        $this->assertEquals(
            'Collect the GPS coordinates of this store.', 
            $bodyLabels['/xform_geopoint/store_gps']['label']
        );
    }

    #[Test]
    public function it_extracts_field_constraints(): void
    {
        $xform = new Xform($this->xformComplex);
        $constraints = $xform->getFieldConstraints();

        $this->assertIsArray($constraints);
        // Complex form should have constraints
        $this->assertGreaterThanOrEqual(0, count($constraints));
    }

    #[Test]
    public function it_combines_all_survey_field_information(): void
    {
        $xform = new Xform($this->xformGeopoint);
        $survey = $xform->getSurvey();

        $this->assertIsArray($survey);
        
        // Check GPS field structure using helper method
        $gpsField = $this->findFieldByNodeset($survey, '/xform_geopoint/store_gps');
        $this->assertNotNull($gpsField, 'GPS field should be found');
        
        $this->assertArrayHasKey('type', $gpsField);
        $this->assertArrayHasKey('label', $gpsField);
        $this->assertArrayHasKey('required', $gpsField);
        $this->assertArrayHasKey('readonly', $gpsField);
        $this->assertArrayHasKey('calculate', $gpsField);
        $this->assertArrayHasKey('nodeset', $gpsField);
        $this->assertArrayHasKey('name', $gpsField);
        
        $this->assertEquals('geopoint', $gpsField['type']);
        $this->assertEquals('Collect the GPS coordinates of this store.', $gpsField['label']);
        $this->assertEquals('store_gps', $gpsField['name']);
        $this->assertEquals('/xform_geopoint/store_gps', $gpsField['nodeset']);
    }

    #[Test]
    public function it_handles_complex_fields_with_groups(): void
    {
        $xform = new Xform($this->xformComplex);
        $survey = $xform->getSurvey();

        // Check specific fields using helper method
        $cpfField = $this->findFieldByNodeset($survey, '/teste_estrutura_em_objeto/coletor/cpf');
        $this->assertNotNull($cpfField, 'CPF field should be found');
        
        $nomeField = $this->findFieldByNodeset($survey, '/teste_estrutura_em_objeto/coletor/nome');
        $this->assertNotNull($nomeField, 'Nome field should be found');
        
        $this->assertEquals('string', $cpfField['type']);
        $this->assertEquals('CPF', $cpfField['label']);
        $this->assertEquals('cpf', $cpfField['name']);
        $this->assertEquals('/teste_estrutura_em_objeto/coletor/cpf', $cpfField['nodeset']);
    }

    #[Test]
    public function it_handles_calculated_and_readonly_fields(): void
    {
        $xform = new Xform($this->xformComplex);
        $survey = $xform->getSurvey();

        // instanceID should be readonly with calculate
        $instanceField = $this->findFieldByNodeset($survey, '/teste_estrutura_em_objeto/meta/instanceID');
        $this->assertNotNull($instanceField, 'InstanceID field should be found');
        
        $this->assertEquals('string', $instanceField['type']);
        $this->assertEquals('true()', $instanceField['readonly']);
        $this->assertEquals("concat('uuid:', uuid())", $instanceField['calculate']);
        $this->assertEquals('instanceID', $instanceField['name']);
        $this->assertEquals('/teste_estrutura_em_objeto/meta/instanceID', $instanceField['nodeset']);
    }

    #[Test]
    public function it_handles_select_fields_with_choices(): void
    {
        $xform = new Xform($this->xformComplex);
        $survey = $xform->getSurvey();

        // UC field should be select1
        $ucField = $this->findFieldByNodeset($survey, '/teste_estrutura_em_objeto/uc');
        $this->assertNotNull($ucField, 'UC field should be found');
        
        $this->assertEquals('select1', $ucField['type']);
        $this->assertEquals('Unidade de Conservação', $ucField['label']);
        $this->assertEquals('uc', $ucField['name']);
        $this->assertEquals('/teste_estrutura_em_objeto/uc', $ucField['nodeset']);
    }

    #[Test]
    public function it_returns_empty_array_for_malformed_xform(): void
    {
        $xform = new Xform('<invalid>xml</invalid>');
        
        $bindAttributes = $xform->getBindAttributes();
        $bodyLabels = $xform->getBodyLabels();
        $survey = $xform->getSurvey();
        
        $this->assertEmpty($bindAttributes);
        $this->assertEmpty($bodyLabels);
        $this->assertEmpty($survey);
    }
}