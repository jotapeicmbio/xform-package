<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Xform;
use PHPUnit\Framework\Attributes\Test;

/**
 * Teste de integração com XForm validado pelo pyxform
 * 
 * Este teste usa um XForm real validado pela biblioteca oficial
 * pyxform para verificar se getSurvey() extrai corretamente
 * todos os tipos de campos possíveis.
 */
class XLSFormIntegrationValidatedTest extends TestCase
{
    private string $validatedXmlPath;
    private string $validatedXmlContent;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->validatedXmlPath = __DIR__ . '/../xforms/survey_biodiversidade_completo.xml';
        $this->validatedXmlContent = file_get_contents($this->validatedXmlPath);
    }
    
    #[Test]
    public function xformValidatedByPyxformExists(): void
    {
        $this->assertFileExists(
            $this->validatedXmlPath,
            'Arquivo XML validado pelo pyxform deve existir'
        );
    }
    
    #[Test]
    public function getSurveyWithValidatedXform(): void
    {
        $this->assertFileExists($this->validatedXmlPath);
        
        $xform = new Xform($this->validatedXmlContent);
        $survey = $xform->getSurvey();
        
        // Verifica estrutura básica
        $this->assertIsArray($survey, 'getSurvey() deve retornar array');
        $this->assertNotEmpty($survey, 'Survey não pode estar vazio');
        
        // Verifica campos obrigatórios esperados
        $expectedFields = [
            'nome_formulario',
            'numero_registro', 
            'valor_monetario',
            'data_coleta',
            'hora_coleta',
            'timestamp_completo',
            'coordenada_ponto',
            'trajeto',
            'area_estudo',
            'qtd_machos',
            'qtd_femeas',
            'qtd_total',
            'especie_observada',
            'tipos_habitat',
            'tem_filhotes',
            'qtd_filhotes',
            'cpf',
            'email',
            'idade',
            'peso',
            'foto_evidencia',
            'gravacao_som',
            'video_comportamento',
            'documento_anexo',
            'codigo_amostra',
            'inicio_formulario',
            'fim_formulario',
            'data_hoje',
            'id_dispositivo',
            'nome_usuario',
            'confirmacao_final'
        ];
        
        $foundFields = [];
        foreach ($survey as $field) {
            $this->assertIsArray($field, 'Cada field deve ser um array');
            $this->assertArrayHasKey('name', $field, 'Field deve ter name');
            $foundFields[] = $field['name'];
        }
        
        // Verifica se campos principais foram encontrados
        $missingFields = array_diff($expectedFields, $foundFields);
        $this->assertEmpty(
            $missingFields,
            'Campos obrigatórios não encontrados: ' . implode(', ', $missingFields)
        );
        
        // printf("\n✅ Total de campos extraídos: %d\n", count($survey));
        // printf("✅ Campos esperados encontrados: %d/%d\n", 
        //     count($expectedFields) - count($missingFields), 
        //     count($expectedFields)
        // );
    }
    
    #[Test]
    public function fieldTypesFromValidatedXform(): void
    {
        $this->assertFileExists($this->validatedXmlPath);
        
        $xform = new Xform($this->validatedXmlContent);
        $survey = $xform->getSurvey();
        
        $fieldTypes = [];
        foreach ($survey as $field) {
            if (isset($field['type'])) {
                $fieldTypes[] = $field['type'];
            }
        }
        
        // Remove duplicatas e conta tipos únicos
        $uniqueTypes = array_unique($fieldTypes);
        
        // Tipos esperados baseados no XLSForm criado
        $expectedTypes = [
            'string',      // text fields
            'int',         // integer fields  
            'decimal',     // decimal fields
            'date',        // date fields
            'time',        // time fields
            'dateTime',    // datetime fields
            'geopoint',    // GPS coordinates
            'geotrace',    // GPS trace
            'geoshape',    // GPS area
            'select1',     // select_one
            'select',      // select_multiple
            'binary'       // attachments (image, audio, video, file)
        ];
        
        // printf("\n📊 Tipos de campo encontrados: %s\n", implode(', ', $uniqueTypes));
        
        // Verifica se temos diversidade de tipos
        $this->assertGreaterThanOrEqual(
            8, 
            count($uniqueTypes),
            'Deve haver pelo menos 8 tipos diferentes de campo'
        );
        
        // Verifica se tipos geograficos estão presentes
        $geoTypes = array_intersect(['geopoint', 'geotrace', 'geoshape'], $uniqueTypes);
        $this->assertGreaterThanOrEqual(
            3,
            count($geoTypes), 
            'Deve haver os 3 tipos geográficos: geopoint, geotrace, geoshape'
        );
    }
    
    #[Test]
    public function fieldConstraintsFromValidatedXform(): void
    {
        $this->assertFileExists($this->validatedXmlPath);
        
        $xform = new Xform($this->validatedXmlContent);
        $survey = $xform->getSurvey();
        
        $fieldsWithConstraints = [];
        foreach ($survey as $field) {
            if (isset($field['constraint']) && !empty($field['constraint'])) {
                $fieldsWithConstraints[] = $field['name'];
            }
        }
        
        // Deve haver campos com constraints baseado no XLSForm
        $this->assertGreaterThanOrEqual(
            5,
            count($fieldsWithConstraints),
            'Deve haver pelo menos 5 campos com constraints'
        );
        
        // printf("\n🔒 Campos com constraints: %s\n", implode(', ', $fieldsWithConstraints));
    }
    
    #[Test]
    public function fieldLabelsFromValidatedXform(): void
    {
        $this->assertFileExists($this->validatedXmlPath);
        
        $xform = new Xform($this->validatedXmlContent);  
        $survey = $xform->getSurvey();
        
        $fieldsWithLabels = [];
        foreach ($survey as $field) {
            if (isset($field['label']) && !empty($field['label'])) {
                $fieldsWithLabels[] = $field['name'];
            }
        }
        
        // A maioria dos campos deve ter label
        $this->assertGreaterThanOrEqual(
            25,
            count($fieldsWithLabels),
            'Deve haver pelo menos 25 campos com labels'
        );
        
        // printf("\n🏷️ Campos com labels: %d\n", count($fieldsWithLabels));
    }
    
    /**
     * Teste específico para verificar campos de grupos repetitivos
     */
    #[Test]
    public function repeatFieldsFromValidatedXform(): void
    {
        $this->assertFileExists($this->validatedXmlPath);
        
        $xform = new Xform($this->validatedXmlContent);
        $survey = $xform->getSurvey();
        
        $repeatFields = [];
        foreach ($survey as $field) {
            if (isset($field['nodeset']) && str_contains($field['nodeset'] ?? '', 'registros_multiplos')) {
                $repeatFields[] = $field['name'];
            }
        }
        
        // Deve haver campos do grupo repetitivo
        $this->assertGreaterThanOrEqual(
            3,
            count($repeatFields),
            'Deve haver pelo menos 3 campos do grupo repetitivo registros_multiplos'
        );
        
        // printf("\n🔄 Campos de repeat group: %s\n", implode(', ', $repeatFields));
    }
}
