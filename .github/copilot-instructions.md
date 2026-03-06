---
description: "Regras de desenvolvimento para o ICMBio XForm Package - biblioteca PHP para processamento de formulários XForm"
applyTo: "**"
---

# ICMBio XForm Package - Regras de Desenvolvimento

## 🎯 **Contexto do Projeto**

Biblioteca PHP especializada para processamento e manipulação de formulários XForm, desenvolvida pelo ICMBio para coleta e análise de dados de pesquisa de campo com foco em biodiversidade e dados geográficos.

## 📋 **Padrões de Código Obrigatórios**

### **PSR Standards**
- ✅ **PSR-4**: Autoloading `Icmbio\Xform\`
- ✅ **PSR-12**: Coding Style rigorosamente seguido
- ✅ **Semantic Versioning**: Versionamento semântico estrito

### **PHP 8.1+ Type Safety**
- 🔒 **Type hints rigorosos**: Todos os métodos DEVEM ter tipos declarados
- 🔒 **Return types**: Sempre especificar tipos de retorno
- 🔒 **Strict types**: `declare(strict_types=1)` em todos os arquivos
- 🔒 **Nullable types**: Usar `?string`, `?array` quando apropriado

### **Documentação**
- 📚 **PHPDoc**: Obrigatório em todos os métodos públicos e protegidos
- 📚 **Exemplos**: Incluir `@example` em métodos complexos
- 📚 **Parameters**: Documentar todos os parâmetros com `@param`
- 📚 **Exceptions**: Documentar exceções com `@throws`

## 🧪 **Qualidade e Testes**

### **Cobertura de Testes**
- 🎯 **100% coverage**: Meta obrigatória para código novo
- 🎯 **Unit tests**: Cada método público deve ter teste
- 🎯 **Integration tests**: Para fluxos completos de processamento XForm
- 🎯 **Edge cases**: Testar casos extremos (XML malformado, XForms vazios)

### **Estrutura de Testes**
```
tests/
├── Unit/           # Testes unitários por classe/trait
│   ├── Exceptions/ # Testes para hierarquia de exceções
│   └── Concerns/   # Testes para traits
├── Integration/    # Testes de fluxo completo
├── xforms/         # Arquivos XForm para testes
└── fixtures/       # Dados de teste padronizados
```

## 🏗️ **Arquitetura e Design Patterns**

### **Traits Modulares**
- 🧩 **Single Responsibility**: Cada trait tem um propósito específico
- 🧩 **SurveyInstance**: Gerencia instâncias e metadados
- 🧩 **SurveyGeo**: Manipula dados geográficos (geopoint, geotrace, geoshape)
- 🧩 **SurveyAttachment**: Processa anexos e mídias
- 🧩 **SurveyGroupRepeat**: Lida com grupos repetitivos
- 🧩 **SurveyOsm**: Integração OpenStreetMap

### **XML Processing Standards**
- 🔍 **XPath**: Sempre usar XPath com namespaces registrados
- 🔍 **Error handling**: Tratar libxml errors graciosamente
- 🔍 **BOM handling**: Remover Byte Order Mark automaticamente
- 🔍 **Validation**: Validar estrutura XML antes do processamento

## ⚡ **Performance e Otimização**

### **Memory Management**
- 🚀 **Large XML**: Considerar streaming para arquivos grandes
- 🚀 **Caching**: Cache resultados XPath quando apropriado
- 🚀 **Lazy loading**: Carregar dados sob demanda

### **Benchmarking**
- 📊 **Performance tests**: Para formulários grandes (>1MB)
- 📊 **Memory profiling**: Monitorar uso de memória
- 📊 **XPath optimization**: Otimizar queries complexas

## ⚙️ **Ambiente de Desenvolvimento**

### **Docker Workflow**
- 🐳 **Container obrigatório**: Todo desenvolvimento via Docker
- 🐳 **Comandos automatizados**: Scripts wrapper para container
- 🐳 **Isolamento**: Ambiente PHP 8.2+ padronizado
- 🐳 **Dependências**: Composer e PHPUnit via container

### **Scripts de Desenvolvimento**
- `./test` - Executa PHPUnit via container Docker
- `./composer` - Executa Composer via container Docker
- `./php` - Executa PHP via container Docker
- `docker-compose up` - Inicia serviços se necessário

## 🔄 **Workflow de Desenvolvimento**

### **TDD Obrigatório (Test-Driven Development)**
1. 🔴 **Red**: Escrever teste que falha PRIMEIRO
2. 🟢 **Green**: Implementar código mínimo para teste passar
3. 🔵 **Refactor**: Melhorar código mantendo testes passando
4. **Nunca implementar funcionalidade sem teste correspondente**

### **Desenvolvimento Incremental**
1. **Pequenas mudanças frequentes** (commits atômicos)
2. **Feature branches** para novas funcionalidades
3. **TDD cycle**: Red → Green → Refactor para TODA mudança
4. **Review antes merge**: Toda mudança passa por review

### **Commit Standards**
```
feat: adicionar suporte a geoshape em SurveyGeo
fix: corrigir parsing de UTF-8 em XML malformado
docs: atualizar ejemplos no README
test: adicionar testes para grupos aninhados
refactor: melhorar performance de XPath queries
```

### **Antes de Commit**
- ✅ **TDD completo**: Red → Green → Refactor cumprido
- ✅ Executar `./test` (100% pass)
- ✅ Verificar `./composer phpstan` (se configurado)
- ✅ Confirmar PSR-12 compliance
- ✅ Atualizar documentação se necessário
- ✅ **Coverage**: Funcionalidade nova tem teste correspondente

## 🛡️ **Compatibilidade e Estabilidade**

### **PHP Versions**
- 🔧 **Container**: PHP 8.2+ via Docker
- 🔧 **Comandos**: Via scripts wrapper (`./test`, `./composer`, `./php`)
- 🔧 **Tested**: 8.1, 8.2, 8.3 (container matrix)
- 🔧 **CI/CD**: Testes em múltiplas versões

### **XForm Standards**
- 📋 **ODK XForms**: Compatibilidade com padrão ODK
- 📋 **KoBo Toolbox**: Suporte a extensões KoBo
- 📋 **OpenRosa**: Compliance com especificação OpenRosa

### **Backward Compatibility**
- ⚠️ **Breaking changes**: Só em major versions
- ⚠️ **Deprecation**: Avisos antes de remoção
- ⚠️ **Migration guides**: Documentar mudanças

## 🚨 **Error Handling**

### **Exception Hierarchy**
```php
XformException
├── XformParseException     // Problemas parsing XML
├── XformValidationException // XForm inválido
├── XformAttachmentException // Problemas com anexos
└── XformGeoException       // Erros dados geográficos
```

### **Logging Standards**
- 📝 **PSR-3**: Usar interface padrão de logging
- 📝 **Context**: Sempre incluir contexto relevante
- 📝 **Levels**: ERROR para falhas, WARN para problemas não-críticos

## 🌍 **Dados Geográficos - Especializações**

### **Coordinate Systems**
- 🌐 **WGS84**: Sistema padrão (EPSG:4326)
- 🌐 **Validation**: Validar ranges lat/lon (-90/90, -180/180)
- 🌐 **Precision**: Maintain precision adequada para biodiversidade

### **ICMBio Specific Requirements**
- 🏞️ **Unidades de Conservação**: Suporte a dados específicos UC
- 🏞️ **Monitoramento**: Padrões para dados de monitoramento
- 🏞️ **Relatórios**: Formatos compatíveis com sistemas ICMBio

## 🔍 **Code Review Checklist**

- [ ] **TDD seguido**: Teste criado ANTES da implementação
- [ ] Type hints em todos os métodos
- [ ] PHPDoc completo
- [ ] Testes com 100% coverage da nova funcionalidade 
- [ ] PSR-12 compliance
- [ ] Performance considerada
- [ ] Error handling apropriado
- [ ] Backward compatibility mantida
- [ ] Documentação atualizada
- [ ] Exemplos funcionais
- [ ] Edge cases cobertos nos testes
- [ ] **Red-Green-Refactor** cycle documentado

---

**Lembre-se**: Este projeto é fundamental para pesquisa de biodiversidade no Brasil. Qualidade e confiabilidade são prioridades máximas.