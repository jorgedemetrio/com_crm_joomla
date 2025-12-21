# 🤖 Configuração de Agentes de IA - CRM Joomla

Este documento descreve a configuração e uso de diferentes agentes de IA no projeto `com_crm_joomla`.

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Agentes Configurados](#agentes-configurados)
  - [GitHub Copilot](#github-copilot)
  - [Jules (Google AI)](#jules-google-ai)
- [Estrutura de Arquivos](#estrutura-de-arquivos)
- [Diretrizes Comuns](#diretrizes-comuns)
- [Como Adicionar Novos Agentes](#como-adicionar-novos-agentes)

---

## Visão Geral

O projeto utiliza múltiplos agentes de IA para auxiliar no desenvolvimento. Cada agente possui um arquivo de configuração específico com instruções personalizadas que garantem consistência e aderência aos padrões do projeto.

### Objetivo
- **Manter consistência** no código gerado por diferentes IAs
- **Documentar padrões** do projeto de forma centralizada
- **Facilitar onboarding** de novos desenvolvedores e IAs
- **Garantir qualidade** através de diretrizes claras

---

## Agentes Configurados

### GitHub Copilot

**Arquivo de Configuração**: `.github/copilot-instructions.md`

#### Descrição
GitHub Copilot é o assistente de código da Microsoft integrado ao VS Code. Fornece sugestões de código em tempo real e assistência contextual.

#### Características
- ✅ Integração nativa com VS Code
- ✅ Sugestões inline durante digitação
- ✅ Chat interativo para perguntas complexas
- ✅ Geração de código baseada em comentários

#### Configuração
O arquivo `.github/copilot-instructions.md` contém:
- Contexto do projeto (Joomla 3, PHP 8.3, MySQL 8)
- Princípios de desenvolvimento
- Padrões de banco de dados
- Regras de interface e i18n
- Instruções específicas para frontend e backend

#### Quando Usar
- Durante o desenvolvimento no VS Code
- Para sugestões rápidas de código
- Para completar funções e classes
- Para gerar queries SQL

#### Comandos Úteis
```
@workspace <pergunta>  - Pergunta sobre o workspace
/explain              - Explica código selecionado
/fix                  - Sugere correção para código
/tests               - Gera testes
```

---

### Jules (Google AI)

**Arquivo de Configuração**: `.jules/instructions.md`

#### Descrição
Jules é o agente de IA da Google (Google AI Studio/Gemini) com capacidades avançadas de compreensão de contexto e geração de código.

#### Características
- ✅ Compreensão profunda de contexto
- ✅ Geração de código estruturado
- ✅ Análise de documentação técnica
- ✅ Suporte a múltiplos idiomas

#### Configuração
O arquivo `.jules/instructions.md` é mais detalhado e inclui:
- Visão geral completa do projeto
- Documentação essencial a consultar
- Checklist de qualidade
- Exemplos de workflow
- Comandos úteis do Joomla 3
- Padrões de código

#### Quando Usar
- Para análises complexas de código
- Para gerar documentação
- Para refatorações planejadas
- Para questões arquiteturais

#### Localização
```
.jules/
└── instructions.md
```

---

## Estrutura de Arquivos

```
com_crm_joomla/
├── .github/
│   └── copilot-instructions.md    # Instruções para GitHub Copilot
├── .jules/
│   └── instructions.md            # Instruções para Jules (Google AI)
├── docs/
│   ├── DEVELOPMENT_GUIDELINES.md  # Diretrizes técnicas
│   ├── funcional/
│   │   └── FUNCIONAL.md          # Especificações funcionais
│   ├── MER.md                    # Modelo de dados
│   └── AGENTS.md                 # Este arquivo
└── README.md
```

---

## Diretrizes Comuns

Todos os agentes de IA configurados seguem estes princípios fundamentais:

### 1. Contexto do Projeto
- **Framework**: Joomla 3.x (estrutura legada, sem namespaces)
- **Backend**: PHP 8.3
- **Banco de Dados**: MySQL 8
- **Frontend**: Bootstrap 5, HTML5, jQuery
- **Idioma**: Português Brasil

### 2. Princípios de Desenvolvimento
- ✅ **Correções pontuais** - nunca refazer arquivos completos
- ✅ **Análise de padrões** - verificar arquivos similares ao corrigir erros
- ✅ **Soft delete** - todas as exclusões são lógicas (state = -2)
- ✅ **UUID** - usar CHAR(36) como chave primária padrão
- ❌ **Não usar namespaces** - Joomla 3 não suporta
- ❌ **Não usar `use`** - usar `jimport()` para importações

### 3. Internacionalização (i18n)
Suporte obrigatório a **8 idiomas**:
1. Português Brasil (pt-BR)
2. Inglês (en-GB)
3. Espanhol (es-ES)
4. Italiano (it-IT)
5. Francês (fr-FR)
6. Alemão (de-DE)
7. Chinês (zh-CN)
8. Japonês (ja-JP)

**Regras**:
- NUNCA remover traduções existentes
- Ao adicionar funcionalidade: traduzir em TODOS os idiomas
- Usar sempre `JText::_('COM_CRM_KEY')`

### 4. Campos de Auditoria (Obrigatórios)
Todas as tabelas devem ter:
```sql
state TINYINT(3) NOT NULL DEFAULT 1,
ordering INT(11) NOT NULL DEFAULT 0,
checked_out INT(11) UNSIGNED NOT NULL DEFAULT 0,
checked_out_time DATETIME NULL,
created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
created_by INT(11) NOT NULL DEFAULT 0,
modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
modified_by INT(11) NULL,
criacao_session_id VARCHAR(200) NULL,
criacao_tracking_id VARCHAR(36) NULL,
criacao_ip VARCHAR(45) NULL,
criacao_ip_proxy VARCHAR(45) NULL,
alteracao_session_id VARCHAR(200) NULL,
alteracao_tracking_id VARCHAR(36) NULL,
alteracao_ip VARCHAR(45) NULL,
alteracao_ip_proxy VARCHAR(45) NULL
```

### 5. Documentação Obrigatória
Antes de fazer alterações, SEMPRE consulte:
- `docs/DEVELOPMENT_GUIDELINES.md` - Padrões técnicos
- `docs/funcional/FUNCIONAL.md` - Especificações funcionais
- `docs/MER.md` - Modelo de dados

### 6. Interface do Usuário
- **Listagens**: Paginação + ordenação por colunas
- **Ícones**: Bootstrap Icons
- **Modais**: Ajuda (todas as telas) + Suporte (formulários)
- **CSRF**: JToken obrigatório em todos os forms
- **Tracking**: UUID em forms de gravação/alteração

### 7. Validação de Dados
- **Lead Válido** = email válido OU telefone válido
- Campo `site` é OPCIONAL
- Validação client-side E server-side

---


## 🔍 Validações Obrigatórias Antes de Commits

### 1. Validação de Sintaxe PHP
Antes de efetuar commits, valide o PHP executando offline:
```bash
php -l <ARQUIVO>
```

### 2. Validação com PHPMD
Use o phpmd para validar o código:
```bash
phpmd <PATH_DO_CODIGO> text cleancode,codesize,controversial,design,naming,unusedcode
```

### 3. Validação com PHPStan
Tente validar com PHPStan para análise estática:
```bash
vendor/bin/phpstan analyse <path do codigo>
```

### 4. Validação de YAML (Swagger/OpenAPI)
Antes de efetuar commits, valide o YAML do Swagger executando:
```bash
python3 - <<'EOF'
import yaml
yaml.safe_load(open('docs/api/openapi.yml'))
print('YAML OK')
EOF
```

---

## Como Adicionar Novos Agentes

### Passo 1: Criar Arquivo de Configuração
Determine o local padrão para o novo agente:
- GitHub Copilot: `.github/copilot-instructions.md`
- Jules: `.jules/instructions.md`
- Cursor: `.cursor/rules`
- Outros: `.ai/<nome-agente>/instructions.md`

### Passo 2: Estruturar Instruções
Use como base os arquivos existentes e inclua:

1. **Contexto do Projeto**
   - Nome e tipo do projeto
   - Stack tecnológica
   - Idioma de comunicação

2. **Documentação Essencial**
   - Links para docs importantes
   - Estrutura do projeto

3. **Princípios de Desenvolvimento**
   - Abordagem ao código
   - Padrões específicos do framework
   - Regras de modificação

4. **Padrões de Banco de Dados**
   - Schema de tabelas
   - Campos obrigatórios
   - Nomenclaturas

5. **Interface e UX**
   - Padrões visuais
   - Componentes reutilizáveis
   - Acessibilidade

6. **Internacionalização**
   - Idiomas suportados
   - Processo de tradução

7. **Checklist de Qualidade**
   - Itens a verificar antes de finalizar

8. **Exemplos Práticos**
   - Workflows típicos
   - Snippets de código

### Passo 3: Atualizar Este Documento
Adicione uma nova seção em [Agentes Configurados](#agentes-configurados) com:
- Nome do agente
- Localização do arquivo de configuração
- Descrição e características
- Quando usar
- Comandos/funcionalidades úteis

### Passo 4: Testar Configuração
1. Faça perguntas básicas ao agente
2. Solicite geração de código simples
3. Verifique aderência aos padrões
4. Ajuste instruções conforme necessário

### Passo 5: Documentar no README
Atualize o `README.md` principal com informações sobre o novo agente.

---

## Checklist de Configuração de Agente

Ao configurar um novo agente, certifique-se de:

- [ ] Arquivo de configuração criado no local correto
- [ ] Contexto do projeto documentado
- [ ] Stack tecnológica especificada
- [ ] Princípios de desenvolvimento descritos
- [ ] Padrões de banco de dados incluídos
- [ ] Regras de i18n documentadas
- [ ] Campos de auditoria especificados
- [ ] Documentação essencial linkada
- [ ] Checklist de qualidade incluído
- [ ] Exemplos práticos fornecidos
- [ ] AGENTS.md atualizado
- [ ] README.md atualizado
- [ ] Agente testado com casos reais

---

## Comparação de Agentes

| Característica | GitHub Copilot | Jules (Google AI) |
|---------------|----------------|-------------------|
| **Integração** | VS Code nativo | API/Interface web |
| **Sugestões inline** | ✅ Sim | ❌ Não |
| **Chat** | ✅ Sim | ✅ Sim |
| **Contexto workspace** | ✅ Excelente | ✅ Muito bom |
| **Geração de código** | ✅ Rápida | ✅ Estruturada |
| **Análise complexa** | ⚠️ Média | ✅ Avançada |
| **Documentação** | ⚠️ Básica | ✅ Detalhada |
| **Multiidioma** | ✅ Sim | ✅ Sim |
| **Configuração** | `.github/` | `.jules/` |

### Quando Usar Cada Um

**GitHub Copilot**:
- Durante desenvolvimento ativo
- Sugestões rápidas
- Completar código
- Pequenas correções

**Jules**:
- Análises arquiteturais
- Geração de documentação
- Refatorações planejadas
- Questões complexas

---

## Manutenção

### Atualização de Instruções
As instruções dos agentes devem ser atualizadas quando:
- Mudanças significativas no projeto
- Novos padrões adotados
- Feedback de uso inadequado
- Atualização de versões (Joomla, PHP, etc.)

### Versionamento
Mantenha registro de alterações importantes:
```markdown
## Histórico de Alterações

### v1.1 - 21/12/2024
- Adicionado suporte ao Jules (Google AI)
- Melhorado detalhamento de i18n
- Incluído checklist de qualidade

### v1.0 - 20/12/2024
- Configuração inicial do GitHub Copilot
- Documentação base do projeto
```

---

## Recursos Adicionais

### Documentação do Projeto
- [DEVELOPMENT_GUIDELINES.md](../docs/DEVELOPMENT_GUIDELINES.md)
- [FUNCIONAL.md](../docs/funcional/FUNCIONAL.md)
- [MER.md](../docs/MER.md)

### Documentação Externa
- [Joomla 3 Documentation](https://docs.joomla.org/J3.x:Developing_an_MVC_Component)
- [Joomla API Reference](https://api.joomla.org/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)

### Comunidade
- [Joomla Community Portal](https://community.joomla.org/)
- [Joomla Stack Exchange](https://joomla.stackexchange.com/)

---

## Suporte

Para questões sobre configuração de agentes:
1. Consulte este documento
2. Revise os arquivos de configuração existentes
3. Teste com casos simples primeiro
4. Documente problemas e soluções

---

**Última Atualização**: 21 de Dezembro de 2024  
**Versão**: 1.0  
**Mantido por**: Equipe de Desenvolvimento CRM

---

## Licença

Este documento e as configurações de agentes seguem a mesma licença do projeto principal.
