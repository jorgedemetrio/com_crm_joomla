# TODO - Auditoria e Otimização `com_crm_joomla` para Joomla 5

Este arquivo rastreia o progresso da auditoria e otimização do componente `com_crm_joomla`.

## Fase 1: Análise e Validação

- [ ] **Compatibilidade com Joomla 5**
  - [ ] Validar uso de namespaces modernos (`Joomla\Component\Crm\...`).
  - [ ] Validar uso das novas classes MVC (MVCComponent, BaseController, AdminModel, etc.).
  - [ ] Validar uso de Injeção de Dependência em vez de `Factory`.
  - [ ] Validar uso do `WebAssetManager` para CSS/JS.
  - [ ] Validar sistema de rotas (`router.php` e `RouterView`).
  - [ ] Validar estrutura de pastas (`src`, `forms`, etc.) e comparar com o `com_crm_joomla.xml`.

- [ ] **Qualidade do Código (Análise Estática)**
  - [ ] Executar `php -l` em todos os arquivos PHP.
  - [ ] Executar `phpmd` e analisar os resultados.
  - [ ] Executar `phpstan` e analisar os resultados.
  - [ ] Validar aderência aos padrões SOLID e KISS (revisão manual).
  - [ ] Validar `docs/api/openapi.yml` (se existir).

- [ ] **Funcionalidade vs. Especificação**
  - [ ] Validar se todas as funcionalidades do `FUNCIONAL.md` estão implementadas.
  - [ ] Validar se todas as tabelas do `install.sql` são gerenciáveis no painel de administração.
  - [ ] Validar implementação do ACL (`access.xml` e uso no código).
  - [ ] Validar criação de todos os links de menu necessários no frontend (`tmpl/*.xml`).

- [ ] **Internacionalização (i18n)**
  - [ ] Verificar se todas as strings visíveis usam `Text::_()`.
  - [ ] Confirmar a existência dos arquivos de idioma para: pt-BR, en-GB, es-ES, de-DE, fr-FR, zh-CN, ja-JP, it-IT.

- [ ] **Manifesto de Instalação (`com_crm_joomla.xml`)**
  - [ ] Validar informações do autor (Jorge Demetrio, etc.). (Já verificado)
  - [ ] Validar URL do servidor de atualização. (Já verificado)
  - [ ] Validar se todos os arquivos e pastas necessários estão incluídos.

- [ ] **Baixa Prioridade**
  - [ ] Procurar por strings, números ou datas repetidas que possam ser convertidas em constantes.

## Fase 2: Correção e Implementação

- [ ] (A ser preenchido com os itens encontrados na Fase 1)

## Fase 3: Testes e Finalização

- [ ] Investigar e propor uma solução para um ambiente de testes automatizado ou local.
- [ ] Criar um commit com todas as alterações de código.
- [ ] Submeter o trabalho para revisão.
