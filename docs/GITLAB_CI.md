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

### 📚 Próximos passos

1. **Merge Request aprovado** → Merge para `develop`
2. **Download do artefato** → Conteúdo para Packagist
3. **Tag de versão** → Publicação oficial
4. **Packagist update** → Disponível via Composer

---

💡 **Dica**: O artefato está otimizado para publicação direta no Packagist!