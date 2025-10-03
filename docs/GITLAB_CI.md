# GitLab CI/CD - Pacote Xform

Este pipeline automatiza a criação de artefatos para publicação no Packagist.

## 🚀 Como funciona

### Trigger
O pipeline é executado automaticamente quando:
- É criado um **Merge Request** para a branch `develop`
- Todas as alterações são validadas e empacotadas

### Jobs

#### `package_for_packagist`
- **Objetivo**: Criar artefato limpo do diretório `package/` 
- **Trigger**: Merge Request → `develop`
- **Duração**: ~2-3 minutos
- **Artefato**: `package-artifact/` (expira em 30 dias)

### 📦 Conteúdo do Artefato

O artefato gerado contém apenas os arquivos necessários para o Packagist:

```
package-artifact/
├── src/                     # Código fonte
│   ├── Xform.php
│   └── Concerns/
├── composer.json            # Metadados do pacote
├── vendor/                  # Dependências de produção
└── BUILD_INFO.md           # Informações do build
```

### 📥 Como baixar o artefato

1. Acesse o Merge Request no GitLab
2. Vá na aba **Pipelines**
3. Clique no pipeline executado
4. Na seção **Job artifacts**, baixe `xform-package-{hash}`
5. Extraia o conteúdo de `package-artifact/`

### 🔧 Configuração

O pipeline usa:
- **Imagem**: `composer:2.6` (inclui PHP 8.2)
- **Cache**: Dependências do Composer
- **Dependências**: Apenas produção (`--no-dev`)

### 📋 Arquivos excluídos automaticamente

- `tests/` - Testes unitários
- `.phpunit.cache/` - Cache do PHPUnit  
- `phpunit.xml` - Configuração de testes
- Arquivos de desenvolvimento

### 🐛 Troubleshooting

**Pipeline não executa:**
- Verifique se o MR é para `develop`
- Confirme que há mudanças no diretório `package/`

**Artefato vazio:**
- Verifique se `composer.json` está válido
- Confirme estrutura do diretório `package/src/`

**Erro de dependências:**
- Verifique se `composer.json` tem todas as dependências
- Use `composer validate` localmente

## 📚 Como publicar no Packagist

### Pré-requisitos
1. **Conta no Packagist**: Crie em [packagist.org](https://packagist.org)
2. **Repositório público**: O repo deve estar acessível publicamente
3. **composer.json válido**: Com nome único no formato `vendor/package`

### 🔄 Fluxo completo

#### 1. **Preparar o artefato**
```bash
# Após o pipeline gerar o artefato
1. Baixe o artefato do GitLab CI
2. Extraia o conteúdo de package-artifact/
3. Verifique se composer.json está correto
```

#### 2. **Criar repositório separado (Recomendado)**
```bash
# Opção A: Repositório dedicado ao pacote
git clone https://gitlab.com/seu-usuario/xform-package-dist.git
cd xform-package-dist
cp -r /caminho/do/artefato/* .
git add .
git commit -m "Release v1.0.0"
git tag v1.0.0
git push origin main
git push origin v1.0.0
```

#### 3. **Registrar no Packagist**
1. Acesse [packagist.org](https://packagist.org)
2. Faça login com GitHub/GitLab
3. Clique em **"Submit"**
4. Cole a URL do repositório: `https://gitlab.com/seu-usuario/xform-package-dist`
5. Clique em **"Check"** → **"Submit"**

#### 4. **Configurar auto-update (Opcional)**
```bash
# No GitLab, vá em Settings > Webhooks
# Adicione a URL do Packagist webhook:
https://packagist.org/api/update-package?username=SEU_USER&apiToken=SEU_TOKEN
```

### 🎯 **Opção alternativa: Subtree/Submodule**

Se preferir manter tudo no mesmo repositório:

```bash
# Criar subtree para o package
git subtree push --prefix=package origin package-dist

# Registrar a branch package-dist no Packagist
URL: https://gitlab.com/seu-usuario/xform-package/tree/package-dist
```

### 📋 **Checklist antes de publicar**

- [ ] `composer.json` tem nome único (`icmbio/xform`)
- [ ] Versão está definida ou usa tags Git
- [ ] Descrição e keywords estão preenchidas
- [ ] Licença está especificada
- [ ] Autoload PSR-4 está correto
- [ ] Dependências estão listadas corretamente
- [ ] README.md existe e está documentado

### 🏷️ **Versionamento**

```bash
# Criar tags para versões
git tag v1.0.0
git push origin v1.0.0

# Packagist detecta automaticamente as tags como versões
```

### 🔧 **composer.json otimizado para Packagist**

```json
{
    "name": "icmbio/xform",
    "description": "Biblioteca para processamento de XForms do ICMBio",
    "type": "library",
    "license": "MIT",
    "keywords": ["xform", "xml", "forms", "icmbio"],
    "homepage": "https://gitlab.com/icmbio/xform-package",
    "authors": [
        {
            "name": "João Pedro Garcia",
            "email": "joao.garcia.bolsista@icmbio.gov.br"
        }
    ],
    "require": {
        "php": ">=8.1"
    },
    "autoload": {
        "psr-4": {
            "Icmbio\\Xform\\": "src/"
        }
    },
    "extra": {
        "branch-alias": {
            "dev-main": "1.0-dev"
        }
    }
}
```

### � **Instalação pelo usuário final**

Após publicado, outros desenvolvedores poderão instalar:

```bash
composer require icmbio/xform
```

### 🐛 **Troubleshooting comum**

**"Package name already exists"**
- Mude o nome no `composer.json`
- Use formato `seu-vendor/xform`

**"Could not load package"**
- Verifique se repositório é público
- Confirme se `composer.json` está na raiz
- Valide JSON: `composer validate`

**"No valid composer.json"**
- Arquivo deve estar na raiz do repositório
- Syntax deve estar correta
- Required fields: name, autoload

---

💡 **Dica**: Use repositório separado para o pacote distribuível - mantém o código limpo e facilita o versionamento!