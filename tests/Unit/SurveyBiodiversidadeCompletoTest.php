<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Icmbio\Xform\Xform;

/**
 * Teste completo do array esperado vs gerado para survey_biodiversidade_completo.xml
 * 
 * Este teste verifica se o método getSurvey() retorna exatamente
 * a estrutura esperada para o XForm validado pelo pyxform.
 */
class SurveyBiodiversidadeCompletoTest extends TestCase
{
    private string $validatedXmlContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validatedXmlContent = file_get_contents('/var/www/html/tests/xforms/survey_biodiversidade_completo.xml');
    }

    public function testCompleteExpectedArrayStructure(): void
    {
        $xform = new Xform($this->validatedXmlContent);
        $actualSurvey = $xform->getSurvey();
        
        $expectedSurvey = $this->getExpectedSurveyArray();
        
        // Verifica quantidade total
        $this->assertCount(
            count($expectedSurvey), 
            $actualSurvey,
            sprintf('Esperado %d campos, obtido %d', count($expectedSurvey), count($actualSurvey))
        );
        
        // Verifica cada campo individualmente
        for ($i = 0; $i < count($expectedSurvey); $i++) {
            $expected = $expectedSurvey[$i];
            $actual = $actualSurvey[$i] ?? null;
            
            $this->assertNotNull($actual, "Campo $i não encontrado no resultado");
            
            // Verifica estrutura do campo
            $this->assertEquals($expected['name'], $actual['name'], "Nome do campo $i não confere");
            $this->assertEquals($expected['nodeset'], $actual['nodeset'], "Nodeset do campo $i não confere");
            $this->assertEquals($expected['type'], $actual['type'], "Tipo do campo $i não confere");
            $this->assertEquals($expected['required'], $actual['required'], "Required do campo $i não confere");
            $this->assertEquals($expected['readonly'], $actual['readonly'], "Readonly do campo $i não confere");
            $this->assertEquals($expected['calculate'], $actual['calculate'], "Calculate do campo $i não confere");
            $this->assertEquals($expected['constraint'], $actual['constraint'], "Constraint do campo $i não confere");
            $this->assertEquals($expected['relevant'], $actual['relevant'], "Relevant do campo $i não confere");
            $this->assertEquals($expected['label'], $actual['label'], "Label do campo $i não confere");
            $this->assertEquals($expected['hint'], $actual['hint'], "Hint do campo $i não confere");
            $this->assertEquals($expected['constraintMsg'], $actual['constraintMsg'], "ConstraintMsg do campo $i não confere");
        }
    }
    
    public function testFieldsByName(): void
    {
        $xform = new Xform($this->validatedXmlContent);
        $actualSurvey = $xform->getSurvey();
        
        // Converte para array indexado por name para testes específicos
        $fieldsByName = [];
        foreach ($actualSurvey as $field) {
            $fieldsByName[$field['name']] = $field;
        }
        
        // Testa campos específicos importantes
        $this->assertArrayHasKey('nome_formulario', $fieldsByName);
        $this->assertEquals('string', $fieldsByName['nome_formulario']['type']);
        $this->assertEquals('true()', $fieldsByName['nome_formulario']['required']);
        
        $this->assertArrayHasKey('coordenada_ponto', $fieldsByName);
        $this->assertEquals('geopoint', $fieldsByName['coordenada_ponto']['type']);
        
        $this->assertArrayHasKey('qtd_total', $fieldsByName);
        $this->assertNotEmpty($fieldsByName['qtd_total']['calculate']);
        
        $this->assertArrayHasKey('cpf', $fieldsByName);
        $this->assertNotEmpty($fieldsByName['cpf']['constraint']);
        $this->assertNotEmpty($fieldsByName['cpf']['constraintMsg']);
    }

    private function getExpectedSurveyArray(): array
    {
        return [
            [
                'name' => 'nome_formulario',
                'nodeset' => '/data/grupo_basico/nome_formulario',
                'type' => 'string',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Nome do Formulário',
                'hint' => 'Digite o nome do formulário',
                'constraintMsg' => null,
            ],
            [
                'name' => 'numero_registro',
                'nodeset' => '/data/grupo_basico/numero_registro',
                'type' => 'int',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => '. >= 1',
                'relevant' => null,
                'label' => 'Número de Registro',
                'hint' => 'Número sequencial do registro',
                'constraintMsg' => 'Deve ser maior que zero',
            ],
            [
                'name' => 'valor_monetario',
                'nodeset' => '/data/grupo_basico/valor_monetario',
                'type' => 'decimal',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => '. >= 0',
                'relevant' => null,
                'label' => 'Valor Monetário',
                'hint' => 'Valor em reais',
                'constraintMsg' => 'Deve ser positivo',
            ],
            [
                'name' => 'data_coleta',
                'nodeset' => '/data/grupo_basico/data_coleta',
                'type' => 'date',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Data da Coleta',
                'hint' => 'Quando os dados foram coletados',
                'constraintMsg' => null,
            ],
            [
                'name' => 'hora_coleta',
                'nodeset' => '/data/grupo_basico/hora_coleta',
                'type' => 'time',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Hora da Coleta',
                'hint' => 'Hora exata da coleta',
                'constraintMsg' => null,
            ],
            [
                'name' => 'timestamp_completo',
                'nodeset' => '/data/grupo_basico/timestamp_completo',
                'type' => 'dateTime',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Data e Hora Completa',
                'hint' => 'Timestamp completo',
                'constraintMsg' => null,
            ],
            [
                'name' => 'instrucao_inicial',
                'nodeset' => '/data/grupo_basico/instrucao_inicial',
                'type' => 'string',
                'required' => null,
                'readonly' => 'true()',
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Instruções',
                'hint' => 'Leia atentamente antes de prosseguir',
                'constraintMsg' => null,
            ],
            [
                'name' => 'coordenada_ponto',
                'nodeset' => '/data/grupo_localizacao/coordenada_ponto',
                'type' => 'geopoint',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Coordenada GPS',
                'hint' => 'Marque a localização exata',
                'constraintMsg' => null,
            ],
            [
                'name' => 'trajeto',
                'nodeset' => '/data/grupo_localizacao/trajeto',
                'type' => 'geotrace',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Trajeto Percorrido',
                'hint' => 'Trace a rota realizada',
                'constraintMsg' => null,
            ],
            [
                'name' => 'area_estudo',
                'nodeset' => '/data/grupo_localizacao/area_estudo',
                'type' => 'geoshape',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Área de Estudo',
                'hint' => 'Delimite a área de interesse',
                'constraintMsg' => null,
            ],
            [
                'name' => 'qtd_machos',
                'nodeset' => '/data/grupo_contagem/qtd_machos',
                'type' => 'int',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => '. >= 0',
                'relevant' => null,
                'label' => 'Machos',
                'hint' => 'Quantidade de machos observados',
                'constraintMsg' => 'Deve ser maior ou igual a zero',
            ],
            [
                'name' => 'qtd_femeas',
                'nodeset' => '/data/grupo_contagem/qtd_femeas',
                'type' => 'int',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => '. >= 0',
                'relevant' => null,
                'label' => 'Fêmeas',
                'hint' => 'Quantidade de fêmeas observadas',
                'constraintMsg' => 'Deve ser maior ou igual a zero',
            ],
            [
                'name' => 'qtd_total',
                'nodeset' => '/data/grupo_contagem/qtd_total',
                'type' => 'string',
                'required' => null,
                'readonly' => null,
                'calculate' => ' /data/grupo_contagem/qtd_machos  +  /data/grupo_contagem/qtd_femeas ',
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'especie_observada',
                'nodeset' => '/data/grupo_selecao/especie_observada',
                'type' => 'string',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Espécie Observada',
                'hint' => 'Selecione a espécie encontrada',
                'constraintMsg' => null,
            ],
            [
                'name' => 'tipos_habitat',
                'nodeset' => '/data/grupo_selecao/tipos_habitat',
                'type' => 'string',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Tipos de Habitat',
                'hint' => 'Selecione todos os habitats presentes',
                'constraintMsg' => null,
            ],
            [
                'name' => 'tem_filhotes',
                'nodeset' => '/data/grupo_selecao/tem_filhotes',
                'type' => 'string',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Tem Filhotes?',
                'hint' => 'A fêmea estava com filhotes?',
                'constraintMsg' => null,
            ],
            [
                'name' => 'qtd_filhotes',
                'nodeset' => '/data/grupo_selecao/qtd_filhotes',
                'type' => 'int',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => ' /data/grupo_selecao/tem_filhotes  = "sim"',
                'label' => 'Quantidade de Filhotes',
                'hint' => 'Quantos filhotes foram observados?',
                'constraintMsg' => null,
            ],
            [
                'name' => 'cpf',
                'nodeset' => '/data/grupo_validacao/cpf',
                'type' => 'string',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => 'regex(., "^[0-9]{11}$")',
                'relevant' => null,
                'label' => 'CPF',
                'hint' => 'Digite o CPF do responsável',
                'constraintMsg' => 'CPF deve ter 11 dígitos',
            ],
            [
                'name' => 'email',
                'nodeset' => '/data/grupo_validacao/email',
                'type' => 'string',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => 'regex(., "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$")',
                'relevant' => null,
                'label' => 'E-mail',
                'hint' => 'E-mail para contato',
                'constraintMsg' => 'E-mail inválido',
            ],
            [
                'name' => 'idade',
                'nodeset' => '/data/grupo_validacao/idade',
                'type' => 'int',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => '. >= 18 and . <= 100',
                'relevant' => null,
                'label' => 'Idade',
                'hint' => 'Idade do responsável',
                'constraintMsg' => 'Idade deve estar entre 18 e 100 anos',
            ],
            [
                'name' => 'peso',
                'nodeset' => '/data/grupo_validacao/peso',
                'type' => 'decimal',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => '. > 0 and . <= 1000',
                'relevant' => null,
                'label' => 'Peso (kg)',
                'hint' => 'Peso do animal',
                'constraintMsg' => 'Peso deve estar entre 0 e 1000kg',
            ],
            [
                'name' => 'foto_evidencia',
                'nodeset' => '/data/grupo_media/foto_evidencia',
                'type' => 'binary',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'gravacao_som',
                'nodeset' => '/data/grupo_media/gravacao_som',
                'type' => 'binary',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'video_comportamento',
                'nodeset' => '/data/grupo_media/video_comportamento',
                'type' => 'binary',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'documento_anexo',
                'nodeset' => '/data/grupo_media/documento_anexo',
                'type' => 'binary',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'codigo_amostra',  
                'nodeset' => '/data/grupo_media/codigo_amostra',
                'type' => 'barcode',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Código da Amostra',
                'hint' => 'Escaneie o código da amostra',
                'constraintMsg' => null,
            ],
            [
                'name' => 'especie_registro',
                'nodeset' => '/data/registros_multiplos/especie_registro',
                'type' => 'string',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Espécie',
                'hint' => 'Selecione a espécie deste registro',
                'constraintMsg' => null,
            ],
            [
                'name' => 'quantidade_registro',
                'nodeset' => '/data/registros_multiplos/quantidade_registro',
                'type' => 'int',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => '. > 0',
                'relevant' => null,
                'label' => 'Quantidade',
                'hint' => 'Quantos indivíduos desta espécie',
                'constraintMsg' => 'Deve ser maior que zero',
            ],
            [
                'name' => 'local_registro',
                'nodeset' => '/data/registros_multiplos/local_registro',
                'type' => 'geopoint',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Local do Registro',
                'hint' => 'Coordenada específica deste registro',
                'constraintMsg' => null,
            ],
            [
                'name' => 'inicio_formulario',
                'nodeset' => '/data/grupo_meta/inicio_formulario',
                'type' => 'dateTime',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'fim_formulario',
                'nodeset' => '/data/grupo_meta/fim_formulario',
                'type' => 'dateTime',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'data_hoje',
                'nodeset' => '/data/grupo_meta/data_hoje',
                'type' => 'date',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'id_dispositivo',
                'nodeset' => '/data/grupo_meta/id_dispositivo',
                'type' => 'string',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'nome_usuario',
                'nodeset' => '/data/grupo_meta/nome_usuario',
                'type' => 'string',
                'required' => null,
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
            [
                'name' => 'confirmacao_final',
                'nodeset' => '/data/grupo_meta/confirmacao_final',
                'type' => 'string',
                'required' => 'true()',
                'readonly' => null,
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => 'Confirmação',
                'hint' => 'Confirmo que todas as informações estão corretas',
                'constraintMsg' => null,
            ],
            [
                'name' => 'instanceID',
                'nodeset' => '/data/meta/instanceID',
                'type' => 'string',
                'required' => null,
                'readonly' => 'true()',
                'calculate' => null,
                'constraint' => null,
                'relevant' => null,
                'label' => null,
                'hint' => null,
                'constraintMsg' => null,
            ],
        ];
    }

    /**
     * @test
     */
    public function deve_retornar_survey_hierarquico_com_repeat_groups(): void
    {
        $xform = new Xform($this->validatedXmlContent);
        $survey = $xform->getSurveyHierarchical();
        
        // Should be array
        $this->assertIsArray($survey);
        
        // Should have at least some fields
        $this->assertGreaterThan(0, count($survey));
        
        // Find repeat group (registros_multiplos) 
        $repeatGroup = null;
        foreach ($survey as $field) {
            if ($field['name'] === 'registros_multiplos' && $field['type'] === 'repeat') {
                $repeatGroup = $field;
                break;
            }
        }
        
        $this->assertNotNull($repeatGroup, 'Deve ter grupo repetitivo registros_multiplos');
        $this->assertArrayHasKey('children', $repeatGroup, 'Repeat group deve ter children');
        $this->assertIsArray($repeatGroup['children'], 'Children deve ser array');
        
        // Check repeat group children structure
        $this->assertGreaterThan(0, count($repeatGroup['children']), 'Repeat group deve ter filhos');
        
        // Verify child field structure
        $firstChild = $repeatGroup['children'][0];
        $this->assertArrayHasKey('name', $firstChild);
        $this->assertArrayHasKey('label', $firstChild);
        $this->assertArrayHasKey('type', $firstChild);
        
        // Check name format for repeat children (should be parent/child)
        $this->assertStringContainsString('/', $firstChild['name'], 'Nome do filho deve conter / (parent/child)');
        
        // Check for specific expected fields in repeat group
        $childNames = array_column($repeatGroup['children'], 'name');
        $this->assertContains('registros_multiplos/especie_registro', $childNames);
        $this->assertContains('registros_multiplos/quantidade_registro', $childNames);
        $this->assertContains('registros_multiplos/local_registro', $childNames);
    }

    /**
     * @test
     */
    public function deve_retornar_campos_simples_sem_children(): void
    {
        $xform = new Xform($this->validatedXmlContent);
        $survey = $xform->getSurveyHierarchical();
        
        // Find a simple field (not repeat group)
        $simpleField = null;
        foreach ($survey as $field) {
            if ($field['name'] === 'grupo_basico/nome_formulario') {
                $simpleField = $field;
                break;
            }
        }
        
        $this->assertNotNull($simpleField, 'Deve encontrar campo simples grupo_basico/nome_formulario');
        $this->assertEquals('grupo_basico/nome_formulario', $simpleField['name']);
        $this->assertEquals('Nome do Formulário', $simpleField['label']);
        $this->assertEquals('text', $simpleField['type']);
        $this->assertArrayNotHasKey('children', $simpleField, 'Campo simples não deve ter children');
    }

    /**
     * @test
     */
    public function deve_retornar_estrutura_hierarquica_esperada_completa(): void
    {
        $xform = new Xform($this->validatedXmlContent);
        $actualHierarchical = $xform->getSurveyHierarchical();
        
        $expectedHierarchical = $this->getExpectedHierarchicalArray();
        
        // Verifica quantidade total
        $this->assertCount(
            count($expectedHierarchical), 
            $actualHierarchical,
            sprintf('Esperado %d campos/grupos, obtido %d', count($expectedHierarchical), count($actualHierarchical))
        );
        
        // Verifica cada campo/grupo individualmente
        for ($i = 0; $i < count($expectedHierarchical); $i++) {
            $expected = $expectedHierarchical[$i];
            $actual = $actualHierarchical[$i] ?? null;
            
            $this->assertNotNull($actual, "Campo/grupo $i não encontrado no resultado");
            
            // Verifica estrutura básica
            $this->assertEquals($expected['name'], $actual['name'], "Nome do campo $i não confere");
            $this->assertEquals($expected['label'], $actual['label'], "Label do campo $i não confere");
            $this->assertEquals($expected['type'], $actual['type'], "Tipo do campo $i não confere");
            
            // Se campo tem choices, verifica choices
            if (isset($expected['choices'])) {
                $this->assertArrayHasKey('choices', $actual, "Campo $i deve ter choices");
                $this->assertIsArray($actual['choices'], "Choices do campo $i deve ser array");
                $this->assertCount(
                    count($expected['choices']),
                    $actual['choices'],
                    "Quantidade de choices do campo $i não confere"
                );
                
                // Verifica cada choice
                for ($c = 0; $c < count($expected['choices']); $c++) {
                    $expectedChoice = $expected['choices'][$c];
                    $actualChoice = $actual['choices'][$c] ?? null;
                    
                    $this->assertNotNull($actualChoice, "Choice $c do campo $i não encontrada");
                    $this->assertEquals($expectedChoice['value'], $actualChoice['value'], "Value da choice $c do campo $i não confere");
                    $this->assertEquals($expectedChoice['label'], $actualChoice['label'], "Label da choice $c do campo $i não confere");
                }
            } else {
                // Campo sem choices não deve ter o atributo
                $this->assertArrayNotHasKey('choices', $actual, "Campo simples $i não deve ter choices");
            }
            
            // Se é repeat group, verifica children
            if ($expected['type'] === 'repeat') {
                $this->assertArrayHasKey('children', $actual, "Repeat group $i deve ter children");
                $this->assertIsArray($actual['children'], "Children do grupo $i deve ser array");
                
                $expectedChildren = $expected['children'];
                $actualChildren = $actual['children'];
                
                $this->assertCount(
                    count($expectedChildren),
                    $actualChildren,
                    "Quantidade de children do grupo $i não confere"
                );
                
                // Verifica cada child
                for ($j = 0; $j < count($expectedChildren); $j++) {
                    $expectedChild = $expectedChildren[$j];
                    $actualChild = $actualChildren[$j] ?? null;
                    
                    $this->assertNotNull($actualChild, "Child $j do grupo $i não encontrado");
                    $this->assertEquals($expectedChild['name'], $actualChild['name'], "Nome do child $j do grupo $i não confere");
                    $this->assertEquals($expectedChild['label'], $actualChild['label'], "Label do child $j do grupo $i não confere"); 
                    $this->assertEquals($expectedChild['type'], $actualChild['type'], "Tipo do child $j do grupo $i não confere");
                    
                    // Se child tem choices, verifica choices
                    if (isset($expectedChild['choices'])) {
                        $this->assertArrayHasKey('choices', $actualChild, "Child $j do grupo $i deve ter choices");
                        $this->assertIsArray($actualChild['choices'], "Choices do child $j do grupo $i deve ser array");
                        $this->assertCount(
                            count($expectedChild['choices']),
                            $actualChild['choices'],
                            "Quantidade de choices do child $j do grupo $i não confere"
                        );
                        
                        // Verifica cada choice do child
                        for ($c = 0; $c < count($expectedChild['choices']); $c++) {
                            $expectedChoice = $expectedChild['choices'][$c];
                            $actualChoice = $actualChild['choices'][$c] ?? null;
                            
                            $this->assertNotNull($actualChoice, "Choice $c do child $j do grupo $i não encontrada");
                            $this->assertEquals($expectedChoice['value'], $actualChoice['value'], "Value da choice $c do child $j do grupo $i não confere");
                            $this->assertEquals($expectedChoice['label'], $actualChoice['label'], "Label da choice $c do child $j do grupo $i não confere");
                        }
                    } else {
                        // Child sem choices não deve ter o atributo
                        $this->assertArrayNotHasKey('choices', $actualChild, "Child simples $j do grupo $i não deve ter choices");
                    }
                }
            } else {
                // Campo simples não deve ter children
                $this->assertArrayNotHasKey('children', $actual, "Campo simples $i não deve ter children");
            }
        }
    }

    private function getExpectedHierarchicalArray(): array
    {
        return [
            [
                'name' => 'grupo_basico/nome_formulario',
                'label' => 'Nome do Formulário',
                'type' => 'text',
            ],
            [
                'name' => 'grupo_basico/numero_registro',
                'label' => 'Número de Registro',
                'type' => 'integer',
            ],
            [
                'name' => 'grupo_basico/valor_monetario',
                'label' => 'Valor Monetário',
                'type' => 'decimal',
            ],
            [
                'name' => 'grupo_basico/data_coleta',
                'label' => 'Data da Coleta',
                'type' => 'date',
            ],
            [
                'name' => 'grupo_basico/hora_coleta',
                'label' => 'Hora da Coleta',
                'type' => 'time',
            ],
            [
                'name' => 'grupo_basico/timestamp_completo',
                'label' => 'Data e Hora Completa',
                'type' => 'dateTime',
            ],
            [
                'name' => 'grupo_basico/instrucao_inicial',
                'label' => 'Instruções',
                'type' => 'text',
            ],
            [
                'name' => 'grupo_localizacao/coordenada_ponto',
                'label' => 'Coordenada GPS',
                'type' => 'geopoint',
            ],
            [
                'name' => 'grupo_localizacao/trajeto',
                'label' => 'Trajeto Percorrido',
                'type' => 'geotrace',
            ],
            [
                'name' => 'grupo_localizacao/area_estudo',
                'label' => 'Área de Estudo',
                'type' => 'geoshape',
            ],
            [
                'name' => 'grupo_contagem/qtd_machos',
                'label' => 'Machos',
                'type' => 'integer',
            ],
            [
                'name' => 'grupo_contagem/qtd_femeas',
                'label' => 'Fêmeas',
                'type' => 'integer',
            ],
            [
                'name' => 'grupo_contagem/qtd_total',
                'label' => null,
                'type' => 'text',
            ],
            [
                'name' => 'grupo_selecao/especie_observada',
                'label' => 'Espécie Observada',
                'type' => 'select1',
                'choices' => [
                    ['value' => 'jaguar', 'label' => 'Jaguar (Panthera onca)'],
                    ['value' => 'puma', 'label' => 'Puma (Puma concolor)'],
                    ['value' => 'jaguatirica', 'label' => 'Jaguatirica (Leopardus pardalis)'],
                    ['value' => 'anta', 'label' => 'Anta (Tapirus terrestris)'],
                    ['value' => 'capivara', 'label' => 'Capivara (Hydrochoerus hydrochaeris)'],
                    ['value' => 'lobo_guara', 'label' => 'Lobo-guará (Chrysocyon brachyurus)'],
                ],
            ],
            [
                'name' => 'grupo_selecao/tipos_habitat',
                'label' => 'Tipos de Habitat',
                'type' => 'select',
                'choices' => [
                    ['value' => 'mata_ciliar', 'label' => 'Mata Ciliar'],
                    ['value' => 'cerrado', 'label' => 'Cerrado'],
                    ['value' => 'pantanal', 'label' => 'Pantanal'],
                    ['value' => 'mata_atlantica', 'label' => 'Mata Atlântica'],
                    ['value' => 'caatinga', 'label' => 'Caatinga'],
                    ['value' => 'amazonia', 'label' => 'Amazônia'],
                ],
            ],
            [
                'name' => 'grupo_selecao/tem_filhotes',
                'label' => 'Tem Filhotes?',
                'type' => 'select1',
                'choices' => [
                    ['value' => 'sim', 'label' => 'Sim'],
                    ['value' => 'nao', 'label' => 'Não'],
                ],
            ],
            [
                'name' => 'grupo_selecao/qtd_filhotes',
                'label' => 'Quantidade de Filhotes',
                'type' => 'integer',
            ],
            [
                'name' => 'grupo_validacao/cpf',
                'label' => 'CPF',
                'type' => 'text',
            ],
            [
                'name' => 'grupo_validacao/email',
                'label' => 'E-mail',
                'type' => 'text',
            ],
            [
                'name' => 'grupo_validacao/idade',
                'label' => 'Idade',
                'type' => 'integer',
            ],
            [
                'name' => 'grupo_validacao/peso',
                'label' => 'Peso (kg)',
                'type' => 'decimal',
            ],
            [
                'name' => 'grupo_media/foto_evidencia',
                'label' => null,
                'type' => 'file',
            ],
            [
                'name' => 'grupo_media/gravacao_som',
                'label' => null,
                'type' => 'file',
            ],
            [
                'name' => 'grupo_media/video_comportamento',
                'label' => null,
                'type' => 'file',
            ],
            [
                'name' => 'grupo_media/documento_anexo',
                'label' => null,
                'type' => 'file',
            ],
            [
                'name' => 'grupo_media/codigo_amostra',
                'label' => 'Código da Amostra',
                'type' => 'barcode',
            ],
            [
                'name' => 'registros_multiplos',
                'label' => 'Registros Múltiplos',
                'type' => 'repeat',
                'children' => [
                    [
                        'name' => 'registros_multiplos/especie_registro',
                        'label' => 'Espécie',
                        'type' => 'select1',
                        'choices' => [
                            ['value' => 'jaguar', 'label' => 'Jaguar (Panthera onca)'],
                            ['value' => 'puma', 'label' => 'Puma (Puma concolor)'],
                            ['value' => 'jaguatirica', 'label' => 'Jaguatirica (Leopardus pardalis)'],
                            ['value' => 'anta', 'label' => 'Anta (Tapirus terrestris)'],
                            ['value' => 'capivara', 'label' => 'Capivara (Hydrochoerus hydrochaeris)'],
                            ['value' => 'lobo_guara', 'label' => 'Lobo-guará (Chrysocyon brachyurus)'],
                        ],
                    ],
                    [
                        'name' => 'registros_multiplos/quantidade_registro',
                        'label' => 'Quantidade',
                        'type' => 'integer',
                    ],
                    [
                        'name' => 'registros_multiplos/local_registro',
                        'label' => 'Local do Registro',
                        'type' => 'geopoint',
                    ],
                ],
            ],
            [
                'name' => 'grupo_meta/inicio_formulario',
                'label' => null,
                'type' => 'dateTime',
            ],
            [
                'name' => 'grupo_meta/fim_formulario',
                'label' => null,
                'type' => 'dateTime',
            ],
            [
                'name' => 'grupo_meta/data_hoje',
                'label' => null,
                'type' => 'date',
            ],
            [
                'name' => 'grupo_meta/id_dispositivo',
                'label' => null,
                'type' => 'text',
            ],
            [
                'name' => 'grupo_meta/nome_usuario',
                'label' => null,
                'type' => 'text',
            ],
            [
                'name' => 'grupo_meta/confirmacao_final',
                'label' => 'Confirmação',
                'type' => 'text',
            ],
            [
                'name' => 'instanceID',
                'label' => null,
                'type' => 'text',
            ],
        ];
    }
}