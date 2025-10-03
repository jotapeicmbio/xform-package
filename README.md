# ICMBio XForm

[![Latest Version](https://img.shields.io/packagist/v/icmbio/xform.svg)](https://packagist.org/packages/icmbio/xform)
[![PHP Version](https://img.shields.io/packagist/php-v/icmbio/xform.svg)](https://packagist.org/packages/icmbio/xform)
[![License](https://img.shields.io/packagist/l/icmbio/xform.svg)](https://packagist.org/packages/icmbio/xform)

Biblioteca PHP para processamento e manipulação de XForms do ICMBio, facilitando a coleta e análise de dados de pesquisa de campo.

## 📋 Características

- ✅ Processamento de formulários XForm
- ✅ Suporte a attachments (mídia)
- ✅ Manipulação de dados geográficos (geopoint)
- ✅ Grupos repetitivos (repeat groups)
- ✅ Instâncias de survey
- ✅ Integração OSM (OpenStreetMap)

## 🚀 Instalação

```bash
composer require icmbio/xform
```

## 📖 Uso Básico

```php
<?php

use Icmbio\Xform\Xform;

// Inicializar o XForm
$xform = new Xform();

// Processar um formulário
$result = $xform->process($xmlData);

// Trabalhar com attachments
$attachments = $xform->getAttachments();

// Manipular dados geográficos
$geoData = $xform->getGeoData();
```

## 🔧 Funcionalidades

### Processamento de Survey

```php
use Icmbio\Xform\Concerns\SurveyInstance;

$survey = new SurveyInstance();
$data = $survey->processInstance($xmlContent);
```

### Attachments (Mídia)

```php
use Icmbio\Xform\Concerns\SurveyAttachment;

$attachment = new SurveyAttachment();
$files = $attachment->extractAttachments($formData);
```

### Dados Geográficos

```php
use Icmbio\Xform\Concerns\SurveyGeo;

$geo = new SurveyGeo();
$coordinates = $geo->extractGeopoints($surveyData);
```

### Grupos Repetitivos

```php
use Icmbio\Xform\Concerns\SurveyGroupRepeat;

$repeats = new SurveyGroupRepeat();
$groups = $repeats->processRepeats($formData);
```

## 📚 Documentação

Para documentação completa, exemplos avançados e guias de integração, visite:
- [Repositório no GitLab](https://gitlab.com/icmbio/xform-package)
- [Wiki do Projeto](https://gitlab.com/icmbio/xform-package/-/wikis/home)

## 🔍 Requisitos

- PHP 8.1 ou superior
- Extensões PHP: xml, json

## 🧪 Testes

```bash
composer test
```

## 📄 Licença

Este projeto está licenciado sob a [Licença MIT](LICENSE).

## 🤝 Contribuição

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📞 Suporte

Para dúvidas, sugestões ou reportar bugs:
- 🐛 [Issues no GitLab](https://gitlab.com/icmbio/xform-package/-/issues)
- 📧 Email: joao.garcia.bolsista@icmbio.gov.br

## 🏛️ Sobre o ICMBio

Instituto Chico Mendes de Conservação da Biodiversidade - ICMBio
- Website: [icmbio.gov.br](https://www.icmbio.gov.br)
- Missão: Proteger e conservar a biodiversidade brasileira

---

Feito com ❤️ para a conservação da biodiversidade brasileira.