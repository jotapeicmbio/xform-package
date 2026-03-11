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
}